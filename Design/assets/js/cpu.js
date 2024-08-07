//cpu scheduling
var selectAlgorithm = document.getElementById('algorithm');
var divQuantomTime = document.getElementById('txt-quantom-time');
var divPriority = document.getElementById('txt-priority');
var btnCpuSubmit = document.getElementById('btn-cpu');
var txtArrival = document.getElementById('arrival');
var txtBurst = document.getElementById('burst');
var txtQuantomTime = document.getElementById('quantom');
var txtPriority = document.getElementById('priority');
var showAlgorithm = document.getElementById('show-algorithm');
var divOutput = document.getElementById('output');
var ganttChartContainer = document.getElementById('ganttchart-container');
var tableRowProcess = document.getElementById('table-row');
var avgTable = document.getElementById('avg-table');
var menuResponsive = document.getElementById('menu-list');
var menuResponsiveBtn = document.getElementById('hamburger-menu');
var priorityColumn = document.getElementById('priority-column');
var apiUrl = 'http://127.0.0.1:8000/api/fcfs';
var algorithm = 'FCFS';
var apiData;

//select diffrent algorithm
selectAlgorithm.addEventListener('change', function () {

    //show and unshow input in select
    var algorithmValue = selectAlgorithm.value;
    algorithm = algorithmValue.toUpperCase();
    apiUrl = 'http://127.0.0.1:8000/api/' + algorithmValue;
    if (algorithmValue == "rr") {
        divQuantomTime.classList.remove('d-none');
    }
    else {
        divQuantomTime.classList.add('d-none');
    }

    if (algorithmValue == "nonpreemptive" || algorithmValue == "preemptive") {
        divPriority.classList.remove('d-none');
        priorityColumn.classList.remove('d-none');
    }
    else {
        divPriority.classList.add('d-none');
        priorityColumn.classList.add('d-none');
    }
});


btnCpuSubmit.addEventListener('click', function () {
    txtArrival.value = deleteSpace(txtArrival.value);
    txtBurst.value = deleteSpace(txtBurst.value);
    if (txtArrival.value.length > 0 && txtBurst.value.length > 0) {
        if (isNumber(txtArrival.value) && isNumber(txtBurst.value)) {
            if (algorithm == "NONPREEMPTIVE" || algorithm == "PREEMPTIVE") {
                txtPriority.value = deleteSpace(txtPriority.value);
                if ((txtPriority.value.length > 0) && isNumber(txtPriority.value)) {
                    connectApi(algorithm, txtArrival, txtBurst, txtQuantomTime, txtPriority);
                }
                else {
                    Swal.fire({
                        icon: "error",
                        title: "Invalid Input",
                        text: "Please enter current values",
                    });
                }
            }
            else if (algorithm == "RR") {
                if ((txtQuantomTime.value.length == 1) && isNumber(txtQuantomTime.value)) {
                    connectApi(algorithm, txtArrival, txtBurst, txtQuantomTime, txtPriority);
                }
                else {
                    Swal.fire({
                        icon: "error",
                        title: "Invalid Input",
                        text: "Please enter current values",
                    });
                }
            }
            else {
                connectApi(algorithm, txtArrival, txtBurst, txtQuantomTime, txtPriority);
            }
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

function connectApi(algorithm, txtArrival, txtBurst, txtQuantomTime, txtPriority) {
    const postData = {
        "Algorithm": algorithm,
        "Arrival": txtArrival.value,
        "Burst": txtBurst.value,
        "Quantom": txtQuantomTime.value,
        "Priority": txtPriority.value,
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
    var ganttChartNameHtml = '';
    var ganttChartNumHtml = '';
    var ganttChartContainerHtml = '';
    var processHtml = '';
    divOutput.classList.remove('d-none');
    showAlgorithm.innerHTML = algorithm;
    var pageWidth = window.innerWidth;
    var ganttChart = data.chart;
    for (let i = 1; i <= ganttChart.length; i++) {
        if (i == 1) {
            ganttChartNameHtml += '<div class="d-flex justify-content-center align-items-center mt-2 flex-wrap col-12" id="ganttchart-name">';
            ganttChartNumHtml += '<div class="d-flex justify-content-center align-items-center ms-4 col-12" id="ganttchart-number">';
        }
        ganttChartNameHtml += '<div class="w-box h-25 bg-blue p-2 text-center border border-secondary text-white fw-bold">' + ganttChart[i - 1]["process"] + '</div>';
        ganttChartNumHtml += '<span class="w-number py-2">' + ganttChart[i - 1]['start'] + '</span>';
        if (i == ganttChart.length || (i % 7 == 0 && i != 1 && pageWidth < 700)) {
            ganttChartNumHtml += '<span class="w-number py-2">' + ganttChart[i - 1]['end'] + '</span>';
        }
        if (ganttChart.length >= 7) {
            if (pageWidth < 700) {
                if ((i % 7 == 0 && i != 1) || i == ganttChart.length) {
                    ganttChartNameHtml += '</div>';
                    ganttChartNumHtml += '</div>';
                    ganttChartContainerHtml += ganttChartNameHtml + ganttChartNumHtml;
                    ganttChartNameHtml = '<div class="d-flex justify-content-center align-items-center mt-2 flex-wrap col-12" id="ganttchart-name">';
                    ganttChartNumHtml = '<div class="d-flex justify-content-center align-items-center ms-4 col-12" id="ganttchart-number">';
                }
            }
            else {
                if ((i % 15 == 0 && i != 1) || i == ganttChart.length) {
                    ganttChartNameHtml += '</div>';
                    ganttChartNumHtml += '</div>';
                    ganttChartContainerHtml += ganttChartNameHtml + ganttChartNumHtml;
                    ganttChartNameHtml = '<div class="d-flex justify-content-center align-items-center mt-2 flex-wrap col-12" id="ganttchart-name">';
                    ganttChartNumHtml = '<div class="d-flex justify-content-center align-items-center ms-4 col-12" id="ganttchart-number">';
                }
            }
        }
        else {
            ganttChartContainerHtml = ganttChartNameHtml + ganttChartNumHtml;
        }
    }
    ganttChartContainer.innerHTML = ganttChartContainerHtml;
    var process = data.Process;
    console.log(algorithm);
    for (let i = 0; i < process.length; i++) {
        if (algorithm == "PREEMPTIVE" || algorithm == "NONPREEMPTIVE") {
            processHtml += '<tr scope="row"><td>' + process[i]["process"] + '</td><td>' + process[i]["arrival_time"] + '</td><td>' + process[i]["burst_time"] + '</td><td>' + process[i]["priority"] + '</td><td>' + process[i]["finish_time"] + '</td><td>' + process[i]["turnaround_time"] + '</td><td>' + process[i]["waiting_time"] + '</td><td>' + process[i]["response_time"] + '</td>';
        }
        else {
            processHtml += '<tr scope="row"><td>' + process[i]["process"] + '</td><td>' + process[i]["arrival_time"] + '</td><td>' + process[i]["burst_time"] + '</td><td>' + process[i]["finish_time"] + '</td><td>' + process[i]["turnaround_time"] + '</td><td>' + process[i]["waiting_time"] + '</td><td>' + process[i]["response_time"] + '</td>';
        }
    }
    if (algorithm == "PREEMPTIVE" || algorithm == "NONPREEMPTIVE") {
        var avgProcess = '<tr scope="row" id="avg-table"><td colspan="5" class="text-end">Average</td><td>' + data.avg_turnaround + '</td><td>' + data.avg_waiting + '</td><td>' + data.avg_response + '</td></tr>';
    }
    else {
        var avgProcess = '<tr scope="row" id="avg-table"><td colspan="4" class="text-end">Average</td><td>' + data.avg_turnaround + '</td><td>' + data.avg_waiting + '</td><td>' + data.avg_response + '</td></tr>';
    }
    tableRowProcess.innerHTML = processHtml + avgProcess;
}