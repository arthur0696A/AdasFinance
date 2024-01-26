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

        input.addEventListener('keyup', function(event) {
            numberMask(input);
            event.preventDefault();
        });
    });
}

const numberMask = (input) => {
    let value = input.value.replace('.', '').replace(',', '').replace(/\D/g, '');
  
    let floatValue = Math.max(parseFloat(value) / 100, 0);
    floatValue = Math.min(floatValue, 100);

    const options = { minimumFractionDigits: 2 };
    const result = new Intl.NumberFormat('pt-BR', options).format(floatValue);

    input.value = result;
}

function saveNewObjectivePercentageValue(userAssetId, value) {
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
