document.addEventListener('DOMContentLoaded', function() {
    formatTable();
    calculateObjectivePercentageDifference();
    addEventListeners();
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

        if(spanValue > 0) {
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

        if(percentageDiff > 0) {
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
            listItem.dataset.symbol = item.symbol;
            listItem.dataset.name = item.name;
            listItem.dataset.lastPrice = item.lastPrice;
            autocompleteAssetList.appendChild(listItem);
        });
    });

    autocompleteAssetList.addEventListener('click', (event) => {
        const selectedSymbol = event.target.dataset.symbol;
        const selectedName = event.target.dataset.name;
        const selectedLastPrice = doubleMaskValue(event.target.dataset.lastPrice);

        const lastPriceElement = document.getElementById('last-price');
        const assetNameElement = document.getElementById('asset-name');
        const averagePrice = document.getElementById('average-price');
        const quantity = document.getElementById('quantity');
        
        numberMask(averagePrice);
        numberMask(quantity);

        const assetInfo = document.getElementById('asset-info');
        const assetNameBlock = document.getElementById('asset-name-block');

        const inputEvent = new Event('input', { bubbles: true });

        autocompleteAssetList.innerHTML = '';
        autocompleteAssetInput.value = selectedSymbol;
    
        if (selectedSymbol && selectedName && selectedLastPrice) {
            lastPriceElement.value = selectedLastPrice;
            assetNameElement.value = selectedName;

            collapseForm.classList.add('show');
            assetInfo.classList.add('d-flex');
            assetNameBlock.style.display = 'block';

            lastPriceElement.dispatchEvent(inputEvent);
            assetNameElement.dispatchEvent(inputEvent);
            averagePrice.dispatchEvent(inputEvent);
            quantity.dispatchEvent(inputEvent);
        }
    });
}

async function searchBySymbol(symbol) {
    const url = `http://localhost:8000/search_by_symbol?symbol=${symbol}`;

    try {
        const response = await fetch(url);

        if (response.ok) {
            const data = await response.json();
            const resultArray = data.data.map(
                item => ({ 
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

    return fetch('http://localhost:8000/asset_goal_percentage', options)
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

function incrementValue(incrementBy) {
    const inputElement = document.getElementById('quantity');
    let currentValue = parseInt(inputElement.value);
    let newValue = currentValue + incrementBy;
    inputElement.value = newValue;
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