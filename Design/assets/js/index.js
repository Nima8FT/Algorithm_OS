//cpu scheduling
var selectAlgorithm = document.getElementById('algorithm');
var divQuantomTime = document.getElementById('txt-quantom-time');
var divPriority = document.getElementById('txt-priority');
var btnCpuSubmit = document.getElementById('btn-cpu');
var txtArrival = document.getElementById('arrival');
var txtBurst = document.getElementById('burst');
var txtQuantomTime = document.getElementById('quantom');
var txtPriority = document.getElementById('priority');
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
    }
    else {
        divPriority.classList.add('d-none');
    }
});


btnCpuSubmit.addEventListener('click', function () {
    if (txtArrival.value.length > 0 && txtBurst.value.length > 0) {
        if (txtArrival.value.length === txtBurst.value.length) {
            if (isNumber(txtArrival.value) && isNumber(txtBurst.value)) {
                if (algorithm == "NONPREEMPTIVE" || algorithm == "PREEMPTIVE") {
                    if ((txtPriority.value.length > 0) && (txtPriority.value.length === txtArrival.value.length) && isNumber(txtPriority.value)) {
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

function isNumber(value) {
    var valueSpace = value.replace(/\s+/g, '');
    return !isNaN(valueSpace);
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
            getDataApi(data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function getDataApi(data) {
    console.log(data);
}