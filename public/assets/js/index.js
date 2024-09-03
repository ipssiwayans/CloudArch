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
                    '#f73757', // Rouge
                    '#923eb9'  // Violet
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