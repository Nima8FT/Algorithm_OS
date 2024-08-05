var txtInstance = document.getElementById('instance');
var txtProcess = document.getElementById('process');
var txtColumn = document.getElementById('column');
var btnSubmit = document.getElementById('btn-submit');
var tableAllocationMax = document.getElementById('table-allocation-max');
var btnFind = document.getElementById('btn-find');
var allocationColumn = document.getElementById('allocation-column');
var allocationProcess = document.getElementById('allocation-process');
var maxColumn = document.getElementById('max-column');
var maxRow = document.getElementById('max-process');
var tableNeedAvailable = document.getElementById('table-need-available');
var needHead = document.getElementById('need-head');
var needRow = document.getElementById('need-row');
var availableHead = document.getElementById('available-head');
var availableRow = document.getElementById('available-row');
var processSequence = document.getElementById('process-sequence');
var menuResponsive = document.getElementById('menu-list');
var menuResponsiveBtn = document.getElementById('hamburger-menu');
var arrayLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
var algorithm = 'banker';
var apiUrl = 'http://127.0.0.1:8000/api/banker';

btnSubmit.addEventListener('click', function () {
    txtInstance.value = deleteSpace(txtInstance.value);
    if (txtInstance.value.length > 0 && txtProcess.value.length > 0 && txtColumn.value.length > 0) {
        if (isNumber(txtInstance.value)) {
            tableAllocationMax.classList.remove('d-none');
            btnFind.classList.remove('d-none');
            var allocationColumnHtml = '<th></th>';
            var maxColumnHtml = '<th></th>';
            for (let i = 0; i < txtColumn.value; i++) {
                allocationColumnHtml += '<th>' + arrayLetters[i] + '</th>';
                maxColumnHtml += '<th>' + arrayLetters[i] + '</th>';
            }
            allocationColumn.innerHTML = allocationColumnHtml;
            maxColumn.innerHTML = maxColumnHtml;
            var allocationRowHtml = '';
            var maxRowHtml = '';
            for (let i = 0; i < txtProcess.value; i++) {
                allocationRowHtml += '<tr><td>P' + (i + 1) + '</td>';
                maxRowHtml += '<tr><td>P' + (i + 1) + '</td>';
                for (let j = 0; j < txtColumn.value; j++) {
                    allocationRowHtml += '<td><input type="number" class="w-100 text-center border-0" id="txt-allocation-' + i + j + '"></td>';
                    maxRowHtml += '<td><input type="number" class="w-100 text-center border-0" id="txt-max-' + i + j + '"></td>';
                }
                allocationRowHtml += '</tr>';
                maxRowHtml += '</tr>';
            }
            allocationProcess.innerHTML = allocationRowHtml;
            maxRow.innerHTML = maxRowHtml;
        }
        else {
            Swal.fire({
                icon: "error",
                title: "Invalid Input",
                text: "Please enter integer values",
            });
        }
    }
    else {
        Swal.fire({
            icon: "error",
            title: "Invalid Input",
            text: "Please enter values",
        });
    }
});

btnFind.addEventListener('click', function () {
    var arrayAllocation = '';
    var arrayMax = '';
    for (let i = 0; i < txtProcess.value; i++) {
        for (let j = 0; j < txtColumn.value; j++) {
            var txtAllocation = document.getElementById('txt-allocation-' + i + j + '');
            var txtMax = document.getElementById('txt-max-' + i + j + '');
            if (j == (txtColumn.value) - 1) {
                arrayAllocation += txtAllocation.value;
                arrayMax += txtMax.value;
            }
            else {
                arrayAllocation += txtAllocation.value + ',';
                arrayMax += txtMax.value + ',';
            }
        }
        if (i !== (txtProcess.value - 1)) {
            arrayAllocation += '#';
            arrayMax += '#';
        }
    }

    if (arrayAllocation.length >= 29 && arrayMax.length >= 29) {
        tableNeedAvailable.classList.remove('d-none');
        connectApi(algorithm, arrayAllocation, arrayMax, txtInstance);
    }
    else {
        Swal.fire({
            icon: "error",
            title: "Invalid Input",
            text: "Please enter currect values",
        });
    }
});

menuResponsiveBtn.addEventListener('click', function () {
    menuResponsive.classList.toggle('d-none');
});

function isNumber(value) {
    var valueSpace = value.replace(/\s+/g, '');
    return !isNaN(valueSpace);
}

function deleteSpace(value) {
    value = value.replace(/^ /, '');
    value = value.replace(/ $/, '');
    return value;
}

function connectApi(algorithm, arrayAllocation, arrayMax, txtInstance) {
    const postData = {
        "Algorithm": algorithm,
        "Allocation": arrayAllocation,
        "Max": arrayMax,
        "Instance": txtInstance.value,
    };
    fetch(apiUrl, {
        method: 'POST', // Specify the method
        headers: {
            'Content-Type': 'application/json' // Specify the content type
        },
        body: JSON.stringify(postData) // Convert the data to JSON
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json(); // Parse the response as JSON
        })
        .then(data => {
            if (data.status) {
                console.log(data);
                getDataApi(data);
            }
            else {
                tableNeedAvailable.classList.add('d-none');
                Swal.fire({
                    icon: "error",
                    title: "Deadlock",
                    text: "System is not in safe state",
                });
            }
        })
        .catch(error => {
            console.log(data);
            console.error('Error:', error);
        });
}

function getDataApi(data) {
    var need = data.Need;
    var needHeadHtml = '<th></th>';
    var availableHeadHtml = '<th></th>';
    for (let i = 0; i < txtColumn.value; i++) {
        needHeadHtml += '<th>' + arrayLetters[i] + '</th>';
        availableHeadHtml += '<th>' + arrayLetters[i] + '</th>';
    }
    needHead.innerHTML = needHeadHtml;
    availableHead.innerHTML = availableHeadHtml;
    var needRowHtml = '';
    for (let i = 0; i < txtProcess.value; i++) {
        needRowHtml += '<tr><td>P' + (i + 1) + '</td>';
        for (let j = 0; j < txtColumn.value; j++) {
            needRowHtml += '<td><span>' + need['P' + (i + 1)][j] + '</span></td>';
        }
        needRowHtml += '</tr>';
    }
    needRow.innerHTML = needRowHtml;

    console.log(data);
    var available = data.Available;
    var availableRowHtml = '<tr><td>Resource</td>';
    for (let i = 0; i < available["Resource"].length; i++) {
        availableRowHtml += '<td><span>' + available['Resource'][i] + '</span></td>';
    }
    availableRowHtml += '</tr>';
    let j = 1;
    let isProcessCompelete = Array.from({ length: txtProcess.value }, () => false);
    let arraySumAvailable = Array.from({ length: txtProcess.value }, () => 0);
    for (let i = 0; i < txtProcess.value; i++) {
        for (let j = 0; j < txtColumn.value; j++) {
            arraySumAvailable[i] += available['P' + (i + 1)][j];
        }
    }
    while (j <= txtProcess.value) {
        let shortAvailable = Number.MAX_SAFE_INTEGER;
        let index = -1;
        for (let i = 0; i < txtProcess.value; i++) {
            if (!isProcessCompelete[i] && arraySumAvailable[i] < shortAvailable) {
                shortAvailable = arraySumAvailable[i];
                index = i;
            }
        }
        if (index !== -1) {
            isProcessCompelete[index] = true
            availableRowHtml += '<tr><td>P' + (index + 1) + '</td>';
            for (let i = 0; i < txtColumn.value; i++) {
                availableRowHtml += '<td><span>' + available['P' + (index + 1)][i] + '</span></td>';
            }
            availableRowHtml += '</tr>';
        }
        j++;
    }
    availableRow.innerHTML = availableRowHtml;
    var processSequenceHtml = '';
    for (let i = 0; i < txtProcess.value; i++) {
        processSequenceHtml += '<th>' + data["Process Sequence"][i] + '</th>';
    }
    processSequence.innerHTML = processSequenceHtml;
}