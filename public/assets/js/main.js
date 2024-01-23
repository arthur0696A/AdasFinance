document.addEventListener('DOMContentLoaded', function() {
    formatTable();
    //calculatePercentageDifference();
});

let currentPercentage = 6;
let percentageGoal = 4;
let percentageDiff = (currentPercentage - percentageGoal).toFixed(2);

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

        if(spanValue >= 0) {
            spanParentNode.classList.add('text-success');
            icon.classList.add('fa-caret-up');
        } else {
            spanParentNode.classList.add('text-danger');
            icon.classList.add('fa-caret-down'); 
        }
    });

    badges = document.querySelectorAll('.badge');
    badges.forEach(function(badge) {
        if (percentageDiff > 0) {
            badge.classList.add('badge-success');
            badge.innerText = "Compra";
        } else if (percentageDiff === 0) {
            badge.classList.add('badge-secondary');
            badge.innerText = "Neutro";
        } else {
            badge.classList.add('badge-danger');
            badge.innerText = "Venda";
        }
    });
}

// function calculatePercentageDifference() {
//     
// }

function convertToFloat(value) {
    value = value.replace('%', '');
    value = value.replace('R$', '');
    value = value.replace(",", ".");

    return value;
}