document.addEventListener('DOMContentLoaded', function() {
    formatTable();
    calculateObjectivePercentageDifference();
    addEventListeners();
    addMessages();
});

function formatTable() {
    let rows = document.querySelectorAll('tr');
    rows.forEach(function(row) {
        if (!row.classList.contains("asset-transaction-history")) {
            addAssetIcon(row);
        }
    });
    
    let columns = document.querySelectorAll('td');
    columns.forEach(function(column) {
        column.classList.add('text-center');
    });

    spans = document.querySelectorAll('.difference');
    spans.forEach(function(span) {
        let spanValue = convertToFloat(span.innerText);
        let spanParentNode = span.parentNode;
        let icon = spanParentNode.querySelector('i');

        if (spanValue > 0) {
            spanParentNode.classList.add('text-success');
            icon.classList.add('fa-caret-up');
        } else if (spanValue == 0) {
            spanParentNode.classList.add('text-secondary');
            icon.classList.add('fa-minus');
        } else {
            spanParentNode.classList.add('text-danger');
            icon.classList.add('fa-caret-down'); 
        }
    });

    let transactionsType = document.querySelectorAll('.transaction-type');
    transactionsType.forEach(function(transactionType) {
        const textContent = transactionType.childNodes[0].nodeValue.trim();
        if (textContent == "COMPRA") {
            transactionType.closest('tr').style.color = "green";
            transactionType.childNodes[1].classList.add("fa-up-long");
        } else {
            transactionType.closest('tr').style.color = "red";
            transactionType.childNodes[1].classList.add("fa-down-long");
        }
    });
}

function calculateObjectivePercentageDifference() {
    let inputs = document.querySelectorAll('.objective-percentage');
    let objectivePercentageSum = 0;

    inputs.forEach(function(input) {
        let td = input.closest('td');
        let previousTd = td.previousElementSibling;
        let nextTd = td.nextElementSibling;

        let spanCurrentPercentage = previousTd.querySelector('.primary-value span');
        let spanDifference = previousTd.querySelector('.second-value span');
        let innerSpan = spanDifference.querySelector('span');

        let icon = previousTd.querySelector('.second-value i');
        let badge = nextTd.querySelector('.badge');

        let currentPercentage = convertToFloat(spanCurrentPercentage.textContent);
        let objectivePercentage = convertToFloat(input.value);
        let percentageDiff = (currentPercentage - objectivePercentage).toFixed(2);

        objectivePercentageSum += parseFloat(objectivePercentage);
        innerSpan.textContent = percentageDiff;

        spanDifference.classList.remove('text-success', 'text-secondary', 'text-danger');
        icon.classList.remove('fa-caret-up', 'fa-minus', 'fa-caret-down');
        badge.classList.remove('badge-success', 'badge-secondary', 'badge-danger');

        if (percentageDiff > 0) {
            spanDifference.classList.add('text-success');
            icon.classList.add('fa-caret-up');
            badge.classList.add('badge-danger');
            badge.innerText = "Venda";
        } else if (percentageDiff == 0) {
            spanDifference.classList.add('text-secondary');
            icon.classList.add('fa-minus');
            badge.classList.add('badge-secondary');
            badge.innerText = "Neutro";
        } else {
            spanDifference.classList.add('text-danger');
            icon.classList.add('fa-caret-down');
            badge.classList.add('badge-success');
            badge.innerText = "Compra";
        }
    });

    if (objectivePercentageSum > 100) {
        showAlertContainer();
        return false;
    }

    closeAlertContainer();
    return true;
}

async function addEventListeners() {
    let objectivePercentageInputs = document.querySelectorAll('.objective-percentage');

    objectivePercentageInputs.forEach(function(input) {
        let originalValue = input.value;

        input.addEventListener('focus', function() {
            originalValue = input.value;
        });

        input.addEventListener('blur', async function() {
            if (input.value !== originalValue) {
                const container = input.parentNode;
                const spinner = container.querySelector('.spinner');

                if (calculateObjectivePercentageDifference()) {
                    input.style.display = 'none';
                    spinner.style.display = 'block';

                    const userAssetId = input.getAttribute('data-user-asset-id');
                    const newValue = input.value;

                    setTimeout(async () => {
                        await saveNewObjectivePercentageValue(userAssetId, newValue);
                        input.style.display = '';
                        spinner.style.display = 'none';
                    }, 1000);
                }
            }
        });
    });

    let doubleMaskInputs = document.querySelectorAll('.double-mask');
    doubleMaskInputs.forEach((doubleMaskInput) => {
        doubleMaskInput.addEventListener('keyup', (event) => {
            numberMask(doubleMaskInput);
        });
    });

    const autocompleteAssetInput = document.getElementById('autocomplete-asset-input');
    const autocompleteAssetList = document.getElementById('autocomplete-asset-list');
    let autocompleteData;

    const collapseForm = document.getElementById("collapse-form");

    autocompleteAssetInput.addEventListener('keyup', async () => {
        const searchQuery = autocompleteAssetInput.value.toUpperCase().trim();
        if (searchQuery !== "") {
            autocompleteData = await searchBySymbol(searchQuery);
        } else {
            autocompleteAssetList.innerHTML = '';
            collapseForm.classList.remove("show");
            return;
        }

        autocompleteAssetList.innerHTML = '';
        const filteredData = autocompleteData
            .filter(item => item.symbol.toUpperCase().includes(searchQuery) || item.name.toUpperCase().includes(searchQuery))
            .slice(0, 5); 

        filteredData.forEach(item => {
            const listItem = document.createElement('li');
            listItem.classList.add('list-group-item');
            listItem.innerText = `${item.symbol} - ${item.name}`;
            listItem.dataset.id = item.id;
            listItem.dataset.symbol = item.symbol;
            listItem.dataset.name = item.name;
            listItem.dataset.lastPrice = item.lastPrice;
            autocompleteAssetList.appendChild(listItem);
        });
    });

    const assetIdElement = document.getElementById('asset-id');
    const lastPriceElement = document.getElementById('last-price');
    const assetNameElement = document.getElementById('asset-name');
    const averagePriceElement = document.getElementById('average-price');
    const quantityElement = document.getElementById('quantity');

    const inputEvent = new Event('input', { bubbles: true });

    autocompleteAssetList.addEventListener('click', (event) => {
        const selectedAssetId = event.target.dataset.id;
        const selectedSymbol = event.target.dataset.symbol;
        const selectedName = event.target.dataset.name;
        const selectedLastPrice = doubleMaskValue(event.target.dataset.lastPrice);

        numberMask(averagePriceElement);
        numberMask(quantityElement);

        const assetInfo = document.getElementById('asset-info');
        const assetNameBlock = document.getElementById('asset-name-block');

        autocompleteAssetList.innerHTML = '';
        autocompleteAssetInput.value = selectedSymbol;
    
        if (selectedAssetId && selectedSymbol && selectedName && selectedLastPrice) {
            assetIdElement.value = selectedAssetId;
            lastPriceElement.value = selectedLastPrice;
            assetNameElement.value = selectedName;

            collapseForm.classList.add('show');
            assetInfo.classList.add('d-flex');
            assetNameBlock.style.display = 'block';

            lastPriceElement.dispatchEvent(inputEvent);
            assetNameElement.dispatchEvent(inputEvent);
            averagePriceElement.dispatchEvent(inputEvent);
            quantityElement.dispatchEvent(inputEvent);
        }
    });

    const userAssetSaveForm = document.getElementById('user-asset-save');
    userAssetSaveForm.addEventListener('submit', () => {
        removeNumberMask(averagePriceElement);
        removeNumberMask(quantityElement);
    });

    const removeAssetButtons = document.querySelectorAll('.remove-asset');
    removeAssetButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            
            Swal.fire({
                title: "Deseja excluir o ativo?",
                text: "Isso deletará o ativo e todo o histórico de transações registrado da sua carteira!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sim, excluir ativo!",
                cancelButtonText: "Cancelar"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const userAssetId = button.getAttribute('data-user-asset-id');
                    deleteUserAsset(userAssetId);
                }
            });

        });
    });

    const transactionAssetIdElement = document.getElementById('asset-id-transaction');
    const transactionAssetSymbolElement = document.getElementById('asset-symbol-transaction');
    const transactionAssetNameElement = document.getElementById('asset-name-transaction');
    const transactionAveragePriceElement = document.getElementById('average-price-transaction');
    const transactionAssetDate = document.getElementById('date-transaction');
    const transactionQuantityElement = document.getElementById('quantity-transaction');

    const newTransactionElements = document.querySelectorAll('.new-transaction');
    newTransactionElements.forEach((button) => {
        button.addEventListener('click', () => {
            let userAssetId = button.getAttribute('data-user-asset-id');
            
            transactionAssetIdElement.value = document.getElementById(`asset-id-${userAssetId}`).value;
            transactionAssetSymbolElement.value = document.getElementById(`symbol-${userAssetId}`).textContent;
            transactionAssetNameElement.value = document.getElementById(`name-${userAssetId}`).textContent;
            transactionAveragePriceElement.value = document.getElementById(`last-price-${userAssetId}`).textContent;

            const currentDate = new Date();
            const year = currentDate.getFullYear();
            let month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
            let day = currentDate.getDate().toString().padStart(2, '0');
            const formattedDate = `${year}-${month}-${day}`;
            transactionAssetDate.value = formattedDate;

            numberMask(transactionAveragePriceElement);
            numberMask(transactionQuantityElement);

            transactionAssetSymbolElement.dispatchEvent(inputEvent);
            transactionAssetSymbolElement.dispatchEvent(inputEvent);
            transactionAssetNameElement.dispatchEvent(inputEvent);
            transactionAveragePriceElement.dispatchEvent(inputEvent);
            transactionAssetDate.dispatchEvent(inputEvent);
            transactionQuantityElement.dispatchEvent(inputEvent);
        });
    });

    const registerTransactionForm = document.getElementById('register-transaction');
    let quantityInput = document.getElementById('quantity-transaction');
    const buyRadioButton = document.getElementById('buy');
    const sellRadioButton = document.getElementById('sell');

    registerTransactionForm.addEventListener('submit', (event) => {
        if (quantityInput.value <= 0) {
            showAlertTransactionContainer('Quantidade deve ser maior que 0');
            
            event.preventDefault();
            return;
        }
      
        if (!buyRadioButton.checked && !sellRadioButton.checked) {
            showAlertTransactionContainer('Selecione pelo menos uma opção: Comprar ou Vender');
            
            event.preventDefault();
            return;
        }

        removeNumberMask(transactionAveragePriceElement);
        removeNumberMask(transactionQuantityElement);
    });

    const expandButtons = document.querySelectorAll('.expand-button');
    expandButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            let userAssetId = button.getAttribute('data-user-asset-id');
            let chartContainer = document.getElementById(`asset-chart-container-${userAssetId}`);
            let canvasId = chartContainer.querySelector('canvas').id;
            let canvasAssetSymbol = canvasId.split('-')[3];

            if (button.classList.contains("expanded")) {
                document.getElementById(`asset-transaction-history-${userAssetId}`).style.visibility = 'collapse';
                button.classList.remove("fa-chevron-up");
                button.classList.add("fa-chevron-down");
                button.classList.remove("expanded");
                return;
            }

            document.getElementById(`asset-transaction-history-${userAssetId}`).style.visibility = 'visible';
            button.classList.remove("fa-chevron-down");
            button.classList.add("fa-chevron-up");
            button.classList.add("expanded");
            
            if (!chartCreated[canvasAssetSymbol]) {
                createChart(canvasAssetSymbol).then(() => {
                
                });
                chartCreated[canvasAssetSymbol] = true;
            }
        });
    });

    const synchronizeButton = document.getElementById('synchronize');
    synchronizeButton.addEventListener('click', () => {
        let userId = synchronizeButton.getAttribute('data-user-id');
        synchronizeUserAssets(userId);
    });

    const navItems = document.querySelectorAll(".nav-link");
    navItems.forEach(button => {
        const groupId = button.getAttribute("data-group-id");
        let totalGroupValue = document.getElementById(`group-${groupId}-total`).value;

        if (!totalGroupValue) {
            totalGroupValue = 0;
        }

        button.innerHTML += totalGroupValue;

        button.addEventListener("click", () => {
            const rows = document.querySelectorAll(`.group-${groupId}`);
            button.classList.toggle("active");
            const isActive = button.classList.contains("active");

            rows.forEach(row => {
                row.style.display = isActive ? "" : "none";
            });
        });
    });

    const radios = document.querySelectorAll('input[name="periodo"]');

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            refreshChart(radio.value, dados);
        });
    });
}

function addAssetIcon(userAsset) {
    
    const groupId = userAsset.getAttribute("data-group-id");
    const assetIcon = userAsset.querySelector('.asset-icon');

    switch (groupId) {
        case '1':
            assetIcon.classList.add("fa-circle-dollar-to-slot");
            assetIcon.style.color = "indianred";
            break;
        case '2':
            assetIcon.classList.add("fa-house-chimney");
            assetIcon.style.color = "indigo";
            break;
        case '3':
            assetIcon.classList.remove("fa-solid");
            assetIcon.classList.add("fa-brands");
            assetIcon.classList.add("fa-btc");
            assetIcon.style.color = "darkgreen";
            break;
    }
}

function addMessages() {
    const urlParams = new URLSearchParams(window.location.search);

    const deleteSuccess = urlParams.get('deleteSuccess');
    if (deleteSuccess === 'true') {
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: 'Ativo deletado com sucesso',
            timer: 2000
        });
    }

    const syncSuccess = urlParams.get('syncSuccess');
    if (syncSuccess === 'true') {
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: 'Ativos sincronizados com sucesso',
            timer: 2000
        });
    }
    //  else {
    //     Swal.fire({
    //         icon: 'error',
    //         title: 'Erro ao sincronizar os ativos',
    //         text: 'Limite de requisições diárias excedidas',
    //         timer: 2000
    //     });
    // }
}

async function searchBySymbol(symbol) {
    const url = `http://localhost:8000/search_by_symbol?symbol=${symbol}`;

    try {
        const response = await fetch(url);

        if (response.ok) {
            const data = await response.json();
            const resultArray = data.data.map(
                item => ({
                    id: item.id,
                    symbol: item.symbol, 
                    name: item.name, 
                    lastPrice: item.last_price 
                })
            );

            return resultArray;
        } else {
            throw new Error('Erro na solicitação');
        }
    } catch (error) {
        console.error('Falha na solicitação:', error);
    }
}

async function saveNewObjectivePercentageValue(userAssetId, value) {
    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            userAssetId: userAssetId,
            newObjectivePercentageValue: value
        }),
    }

    return fetch('http://localhost:8000/user_asset_goal_percentage', options)
        .then(response => {
            if (response.ok) {
                showSuccessContainer();
                setTimeout(async () => {
                    closeSuccessContainer()
                }, 4000);
            }
        })
        .catch(error => {
            console.error('Falha ao cadastrar:', error);
        });
}

async function deleteUserAsset(userAssetId) {
    const options = {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            userAssetId: userAssetId
        }),
    }

    try {
        const response = await fetch('http://localhost:8000/user_asset_delete', options);
        if (response.ok) {
            window.location.href = 'http://localhost:8000/home?deleteSuccess=true';
        } else {
            console.error('Ativo deletado com sucesso:', response.statusText);
        }
    } catch (error) {
        console.error('Falha ao deletar ativo:', error);
    }
}

async function synchronizeUserAssets(userId) {

    showProcessingModal();
    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            userId: userId
        }),
    }

    try {
        const response = await fetch('http://localhost:8000/synchronize_user_assets', options);
        if (response.ok) {
            closeProcessingModal();
            window.location.href = 'http://localhost:8000/home?syncSuccess=true';
        } else {
            console.error('Falha ao sincronizar:', response.statusText);
        }
    } catch (error) {
        console.error('Falha ao sincronizar:', error);
    }
}

function incrementValue(incrementBy) {
    const inputElement = document.getElementById('quantity');
    let currentValue = parseFloat(inputElement.value);
    let newValue = currentValue + incrementBy;
    inputElement.value = newValue;
}

function incrementValueTransaction(incrementBy) {
    const inputElement = document.getElementById('quantity-transaction');
    let currentValue = parseFloat(inputElement.value);
    let newValue = currentValue + incrementBy;
    inputElement.value = newValue;
}

function toggleButtons(selected, other) {
    document.getElementById(selected).checked = true;
    document.getElementById(other).checked = false;

    document.querySelector('label[for="' + selected + '"]').classList.add(`btn-${selected}`);
    document.querySelector('label[for="' + selected + '"]').classList.remove(`btn-${other}`, 'btn-default');

    document.querySelector('label[for="' + other + '"]').classList.add('btn-default');
    document.querySelector('label[for="' + other + '"]').classList.remove('btn-buy', 'btn-sell');
}

function mouseOverBuy(element) {
    let input = document.getElementById(element.htmlFor);
    if (!input.checked) {
        element.classList.remove('btn-default');
        element.classList.add('btn-buy');
    }
}

function mouseOverSell(element) {
    let input = document.getElementById(element.htmlFor);
    if (!input.checked) {
        element.classList.remove('btn-default');
        element.classList.add('btn-sell');
    }
}

function removeBtnStyle(element) {
    let input = document.getElementById(element.htmlFor);
    if (!input.checked) {
        element.id == "buy" ? element.classList.remove('btn-buy') : element.classList.remove('btn-sell');
        element.classList.add('btn-default');
    }
}

function convertToFloat(value) {
    value = value.trim();
    value = value.replace('%', '');
    value = value.replace('R$', '');
    if (value.includes(',') && value.includes('.')) {
        value = value.replace(',', '');
    } else if (value.includes(',')) {
        value = value.replace(',', '.');
    }

    return parseFloat(value);
}

function showSuccessContainer() {
    document.querySelector('.success-container').style.display = 'block';
}

function closeSuccessContainer() {
    document.querySelector('.success-container').style.display = 'none';
}

function showAlertContainer() {
    document.querySelector('.alert-container').style.display = 'block';
}

function closeAlertContainer() {
    document.querySelector('.alert-container').style.display = 'none';
}

const alertTransactionError = document.getElementById('alert-transaction-error');

function showAlertTransactionContainer(error) {
    alertTransactionError.innerHTML = `<div><i class="fa-solid fa-triangle-exclamation"></i><span><strong>Atenção: </strong>${error}</span></div>`;
    alertTransactionError.style.display = 'block';

    setTimeout(async () => {
        closeAlertTransactionContainer();
    }, 3000);
}

function closeAlertTransactionContainer() {
    alertTransactionError.innerHTML = "";
    alertTransactionError.style.display = 'none';
}

function showProcessingModal() {
    document.getElementById('processing-modal').style.display = 'block';
}

function closeProcessingModal() {
    document.getElementById('processing-modal').style.display = 'none';
}

const numberMask = (input) => {
    if (input.value.trim() === '') {
        input.value = 0;
    }

    const inputId = input.id;

    switch (inputId) {
    case 'average-price':
    case 'average-price-transaction':
        doublePriceMask(input);
        break;
    case 'objective-percentage':
        objectivePercentageMask(input);
        break;
    case 'quantity':
    case 'quantity-transaction':
        doubleMask(input);
        break;
    }
}

const objectivePercentageMask = (input) => {
    let value = input.value.replace('.', '').replace(',', '').replace(/\D/g, '');
  
    let floatValue = Math.max(parseFloat(value) / 100, 0);
    floatValue = Math.min(floatValue, 100);

    const options = { minimumFractionDigits: 2 };
    const result = new Intl.NumberFormat('pt-BR', options).format(floatValue);

    input.value = result;
}

const doublePriceMask = (input) => {
    let value = input.value.replace(/\D/g, '');
    value = parseFloat(value) / 100;

    const formattedValue = value.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 2,
    });

    input.value = formattedValue;
}

const doubleMask = (input) => {
    let value = input.value.replace(/[^\d,]/g, '');
    value = value.replace(/^0+(?=\d)/, '');
    value = value.replace(/(\,\d*?)\,/, '$1');
    input.value = value;
}

const intMask = (input) => {
    let value = input.value.replace('.', '').replace(',', '').replace(/\D/g, '');
    input.value = value;
}

const doubleMaskValue = (value) => {
    value = value.replace(/[^\d.]/g, '');

    if (value.indexOf('.') === -1) {
        value += '.00';
    } else if (value.split('.')[1].length === 1) {
        value += '0';
    }

    value = parseFloat(value).toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 2,
    });

    return value;
}

const removeNumberMask = (input) => {
    const inputId = input.id;
console.log(inputId);
    switch (inputId) {
        case 'last-price':
        case 'average-price':
        case 'quantity':
        case 'average-price-transaction':
        case 'quantity-transaction':
            return removeDoubleMask(input);
        case 'objective-percentage':
            return removeObjectivePercentageMask(input);
        default:
            return input.value;
    }
}

const removeObjectivePercentageMask = (input) => {
    let numericValue = input.value.replace(/[^\d.]/g, '');
    let floatValue = Math.max(parseFloat(numericValue) / 100, 0);
    floatValue = Math.min(floatValue, 100);
    input.value = floatValue;
}

const removeDoubleMask = (input) => {
    let numericValue = input.value;
    numericValue = numericValue.replace(/[^\d.,]/g, '');

    if (numericValue.includes('.')) {
        numericValue = numericValue.replace('.', '');
    }

    numericValue = numericValue.replace(',', '.');
    
    input.value = numericValue;
}

const removeIntMask = (input) => {
    let numericValue = input.value.replace(/[^\d]/g, '');
    input.value = parseInt(numericValue, 10);
}

let chart;
let chartSymbol;
let dados;
let chartCreated = {};

async function createChart(symbol) {
    try {
        const response = await fetch(`http://localhost:8000/chart_history?symbol=${symbol}`);
        console.log(response);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        dados = JSON.parse(data);
        chartSymbol = symbol;
        refreshChart('12', dados);
    } catch (error) {
        console.error('Erro ao fazer requisição:', error);
    }
}

function refreshChart(periodoSelecionado, dados) {
    const labels = [];
    const closeValues = [];

    let timeSeries = dados['Monthly Time Series'];

    if (periodoSelecionado !== 'total') {
        timeSeries = Object.entries(timeSeries).slice(0, periodoSelecionado);
        timeSeries.forEach(([date, values]) => {
            labels.push(date);
            closeValues.push(parseFloat(values['4. close']));
        });
    } else {
        Object.keys(timeSeries).forEach(date => {
            labels.push(date);
            closeValues.push(parseFloat(timeSeries[date]['4. close']));
        });
    }

    const ctx = document.getElementById(`price-history-chart-${chartSymbol}`).getContext('2d');

    if (chart) {
        chart.destroy();
    }

    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.reverse(),
            datasets: [{
                label: 'Valor de Fechamento',
                data: closeValues.reverse(),
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
}
