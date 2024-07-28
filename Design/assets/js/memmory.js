var selectAlgorithm = document.getElementById('algorithm');
var btnSubmit = document.getElementById('btn-submit');
var blockSize = document.getElementById('block-size');
var processSize = document.getElementById('process-size');
var algorithmName = document.getElementById('algorithm-name');
var output = document.getElementById('output');
var chartWrite = document.getElementById('chart-write');
var tableRowProcess = document.getElementById('table-row');
var algorithm = "firstfit";
var apiUrl = 'http://127.0.0.1:8000/api/firstfit';


selectAlgorithm.addEventListener('change', function (e) {
    algorithm = selectAlgorithm.value;
    apiUrl = 'http://127.0.0.1:8000/api/' + algorithm;
});

btnSubmit.addEventListener('click', function (e) {
    if (blockSize.value.length > 0 && processSize.value.length > 0) {
        if (isNumber(blockSize.value) && isNumber(processSize.value)) {
            connectApi(algorithm, blockSize, processSize);
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

function isNumber(value) {
    var valueSpace = value.replace(/\s+/g, '');
    return !isNaN(valueSpace);
}

function connectApi(algorithm, blockSize, processSize) {
    const postData = {
        "Algorithm": algorithm,
        "Block": blockSize.value,
        "Process": processSize.value,
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
            getDataApi(data, algorithm);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function getDataApi(data, algorithm) {
    algorithm = algorithm.split('fit');
    algorithmName.innerHTML = algorithm[0].toUpperCase() + " " + "FIT";
    output.classList.remove('d-none');
    var chart = data.chart;
    var chartHtml = '';
    for (let i = 0; i < chart.length; i++) {
        chartHtml += '<div class="d-flex justify-content-start align-items-center mt-3"><div class="fw-bold px-2 w-50">Process ' + chart[i]["process"] + '</div>';
        for (let j = 0; j < chart[i].chart.length; j++) {
            chartHtml += '<div class="w-100 h-25 bg-blue p-2 text-center border border-secondary text-white fw-bold">' + chart[i].chart[j] + '</div>';
        }
        chartHtml += '</div>';
    }
    chartWrite.innerHTML = chartHtml;
    var process = data.process;
    var processHtml = '';
    for (let i = 0; i < process.length; i++) {
        processHtml += '<tr scope="row"><td>' + process[i]["process"] + '</td><td>' + process[i]["process_size"] + '</td><td>' + process[i]["block_size"] + '</td><td>' + process[i]["left_over"] + '</td></tr>'
    }
    tableRowProcess.innerHTML = processHtml;
}