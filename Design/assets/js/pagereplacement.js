var selectAlgorithm = document.getElementById('algorithm');
var btnSubmit = document.getElementById('btn-submit');
var showAlgorithm = document.getElementById('show-algorithm');
var txtRefrences = document.getElementById('refrences');
var txtFrames = document.getElementById('frames');
var output = document.getElementById('output');
var tableHead = document.getElementById('table-head');
var tableBody = document.getElementById('table-row');
var numPageFault = document.getElementById('num-page-fault');
var menuResponsive = document.getElementById('menu-list');
var menuResponsiveBtn = document.getElementById('hamburger-menu');
var algorithm = 'fifo';
var apiUrl = 'http://127.0.0.1:8000/api/fifo';

selectAlgorithm.addEventListener('change', function () {
    algorithm = selectAlgorithm.value;
    apiUrl = 'http://127.0.0.1:8000/api/' + selectAlgorithm.value;
});

btnSubmit.addEventListener('click', function () {
    txtRefrences.value = deleteSpace(txtRefrences.value);
    if (txtRefrences.value.length > 0 && txtFrames.value.length > 0) {
        if (isNumber(txtRefrences.value)) {
            output.classList.remove('d-none');
            if (algorithm == 'randompagereplacement' || algorithm == 'optimalpagereplacement') {
                algorithm = (algorithm == 'randompagereplacement') ? 'Random Page Replacement' : 'Optimal Page Replacement';
            }
            else {
                algorithm = algorithm.toUpperCase();
            }
            showAlgorithm.innerHTML = algorithm;
            connectApi(algorithm, txtRefrences, txtFrames);
        }
        else {
            Swal.fire({
                icon: "error",
                title: "Invalid Input",
                text: "Please enter correct values",
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

function connectApi(algorithm, txtRefrences, txtFrames) {
    const postData = {
        "Algorithm": algorithm,
        "Refrences": txtRefrences.value,
        "Frames": txtFrames.value,
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
            getDataApi(data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function getDataApi(data) {
    var chart = data.chart;
    var tableHeadHtml = '<th>Process</th>';
    for (let i = 0; i < chart.length; i++) {
        tableHeadHtml += '<th>' + chart[i]["process"] + '</th>';
    }
    tableHead.innerHTML = tableHeadHtml;
    var tableRowHtml = '';
    for (let i = 1; i <= txtFrames.value; i++) {
        tableRowHtml += '<tr scope="row"><th>Frame ' + i + '</th>';
        for (let j = 0; j < chart.length; j++) {
            if (chart[j]["frame"][i - 1] == undefined) {
                tableRowHtml += '<td></td>';
            }
            else {
                tableRowHtml += '<td>' + chart[j]["frame"][i - 1] + '</td>';
            }
        }
        tableRowHtml += '</tr>';
    }
    var pageFaultHtml = '<tr><th>Page Fault</th>';
    for (let i = 0; i < chart.length; i++) {
        pageFaultHtml += '<td>' + chart[i]["page fault"] + '</td>'
    }
    pageFaultHtml += '</tr>';
    tableBody.innerHTML = tableRowHtml + pageFaultHtml;
    numPageFault.innerHTML = "The Page Fault has: " + data["page_fault"];
}