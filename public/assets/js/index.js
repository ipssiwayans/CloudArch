$(function() {
    var chartElement = document.getElementById('chart1');
    var totalFiles = chartElement.getAttribute('data-total-files');
    var filesToday = chartElement.getAttribute('data-today-files');

    var ctx = chartElement.getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Fichiers totaux', 'Fichiers d\'aujourd\'hui'],
            datasets: [{
                data: [totalFiles, filesToday],
                backgroundColor: [
                    '#f73757',
                    '#923eb9'
                ],
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: 125,
            plugins: {
                legend: {
                    display: false,
                }
            }
        }
    });
});

$(function() {
    var chartElement = document.getElementById('chart2');
    var totalStorage = chartElement.getAttribute('data-total-storage');
    var usedStorage = chartElement.getAttribute('data-used-storage');
    var availableStorage = chartElement.getAttribute('data-available-storage');

    var ctx = chartElement.getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Stockage utilisé', 'Stockage disponible'],
            datasets: [{
                data: [usedStorage, availableStorage],
                backgroundColor: [
                    '#f73757',
                    '#923eb9'
                ],
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: 125,
            plugins: {
                legend: {
                    display: false,
                }
            }
        }
    });
});