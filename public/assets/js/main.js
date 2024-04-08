document.addEventListener('DOMContentLoaded', function() {
    formatTable();
    calculateObjectivePercentageDifference();
    addEventListeners();
    addMessages();
});

function formatTable() {
    let cells = document.querySelectorAll('td');
    cells.forEach(function(cell) {
        cell.classList.add('text-center');
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
    const transactionAssetDate = document.getElementById('asset-transaction-date');

    const newTransactionElements = document.querySelectorAll('.new-transaction');
    newTransactionElements.forEach((button) => {
        button.addEventListener('click', () => {
            let userAssetId = button.getAttribute('data-user-asset-id');
            
            transactionAssetIdElement.value = document.getElementById(`asset-id-${userAssetId}`).value;
            transactionAssetSymbolElement.value = document.getElementById(`symbol-${userAssetId}`).textContent;
            transactionAssetNameElement.value = document.getElementById(`name-${userAssetId}`).textContent;

            // transactionAssetSymbolElement.dispatchEvent(inputEvent);
            // transactionAssetNameElement.dispatchEvent(inputEvent);
            // transactionAveragePriceElement.dispatchEvent(inputEvent);
            // transactionAssetDate.dispatchEvent(inputEvent);
        });
    });

    const expandButtons = document.querySelectorAll('.expand-button');
    expandButtons.forEach((button) => {
        button.addEventListener('click', () => {
            let userAssetId = button.getAttribute('data-user-asset-id');

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
            
        });
    });

    const synchronizeButton = document.getElementById('synchronize');
    synchronizeButton.addEventListener('click', () => {
        let userId = synchronizeButton.getAttribute('data-user-id');
        synchronizeUserAssets(userId);
    });
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
    let currentValue = parseInt(inputElement.value);
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

function convertToFloat(value) {
    value = value.trim();
    value = value.replace('%', '');
    value = value.replace('R$', '');
    value = value.replace(",", ".");

    return value;
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
        doubleMask(input);
        break;
    case 'objective-percentage':
        objectivePercentageMask(input);
        break;
    case 'quantity':
        intMask(input);
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

const doubleMask = (input) => {
    let value = input.value.replace(/\D/g, '');
    value = parseFloat(value) / 100;

    const formattedValue = value.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 2,
    });

    input.value = formattedValue;
}

const intMask = (input) => {
    let value = input.value.replace('.', '').replace(',', '').replace(/\D/g, '');
    input.value = value;
}

const doubleMaskValue = (value) => {
    value = value.replace(/\D/g, '');
    value = parseFloat(value) / 100;

    const formattedValue = value.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 2,
    });

    return formattedValue;
}

const removeNumberMask = (input) => {
    const inputId = input.id;

    switch (inputId) {
        case 'last-price':
        case 'average-price':
            return removeDoubleMask(input);
        case 'objective-percentage':
            return removeObjectivePercentageMask(input);
        case 'quantity':
            return removeIntMask(input);
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
    let numericValue = input.value.replace(/[^\d.]/g, '');
    input.value = parseFloat(numericValue) / 100;
}

const removeIntMask = (input) => {
    let numericValue = input.value.replace(/[^\d]/g, '');
    input.value = parseInt(numericValue, 10);
}