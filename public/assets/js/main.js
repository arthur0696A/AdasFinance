document.addEventListener('DOMContentLoaded', function() {
    formatTable();
    calculateObjectivePercentageDifference();
    addEventListeners();
});

let percentageDiff;

function formatTable() {
    new mdb.Dropdown(document.getElementById('navbarDropdownMenuAvatar'));
    
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
        showAlert();
    } else {
        closeAlert();
    }
}

function addEventListeners() {
    let objectivePercentageInputs = document.querySelectorAll('.objective-percentage');

    objectivePercentageInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            const container = input.parentNode;
            const spinner = container.querySelector('.spinner');

            input.style.display = 'none';
            spinner.style.display = 'block';

            calculateObjectivePercentageDifference();

            //console.log(input.getAttribute('data-user-asset-id'))
            setTimeout(() => {
                input.style.display = '';
                spinner.style.display = 'none';
            }, 2000);
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

function convertToFloat(value) {
    value = value.trim();
    value = value.replace('%', '');
    value = value.replace('R$', '');
    value = value.replace(",", ".");

    return value;
}

function closeAlert() {
    document.querySelector('.alert-container').style.display = 'none';
}

function showAlert() {
    document.querySelector('.alert-container').style.display = 'block';
}