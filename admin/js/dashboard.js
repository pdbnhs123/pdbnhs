document.addEventListener('DOMContentLoaded', function() {
    // Initialize all charts
    initDocumentChart();
    setupCityChartButton();
});

function initDocumentChart() {
    const ctx = document.getElementById('documentChart').getContext('2d');
    const documentData = window.documentData || {};
    
    const labels = Object.keys(documentData);
    const data = Object.values(documentData);
    const backgroundColors = generateColors(labels.length, 0.7);
    const borderColors = generateColors(labels.length, 1);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Documents Submitted',
                data: data,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.parsed.y} submissions`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeOutQuart'
            }
        }
    });
}

function setupCityChartButton() {
    const btn = document.getElementById('cityChartBtn');
    btn.addEventListener('click', function() {
        const cityData = window.cityData || {};
        showCityModal(cityData);
    });
}

function showCityModal(cityData) {
    const modal = new bootstrap.Modal(document.getElementById('cityModal'));
    const ctx = document.getElementById('cityPieChart').getContext('2d');
    
    // Destroy previous chart if exists
    if (window.cityChart) {
        window.cityChart.destroy();
    }
    
    const labels = Object.keys(cityData);
    const data = Object.values(cityData);
    const backgroundColors = generateColors(labels.length, 0.7);
    
    window.cityChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: backgroundColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((context.parsed / total) * 100);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });
    
    modal.show();
}

function generateColors(count, opacity = 1) {
    const colors = [];
    const hueStep = 360 / count;
    
    for (let i = 0; i < count; i++) {
        const hue = i * hueStep;
        colors.push(`hsla(${hue}, 70%, 50%, ${opacity})`);
    }
    
    return colors;
}