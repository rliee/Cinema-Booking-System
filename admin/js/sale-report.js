const chartBaseOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    },
    scales: {
        x: {
            grid: { color: '#222' },
            ticks: { color: '#9f9f9f' }
        },
        y: {
            grid: { color: '#222' },
            ticks: { color: '#9f9f9f' }
        }
    }
};

if (document.getElementById('dailyChart')) {
    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{
                label: 'Daily Sales',
                data: dailyData,
                borderColor: '#FFD54A',
                backgroundColor: 'rgba(255, 213, 74, .16)',
                fill: true,
                tension: .4,
                pointRadius: 3,
                pointHoverRadius: 5,
                pointBackgroundColor: '#FFD54A'
            }]
        },
        options: chartBaseOptions
    });
}

if (document.getElementById('monthlyChart')) {
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Monthly Sales',
                data: monthData,
                backgroundColor: 'rgba(255, 213, 74, 0.9)',
                borderColor: '#FFD54A',
                borderWidth: 1,
                borderRadius: 10,
                maxBarThickness: 44,
                barPercentage: 0.8,
                categoryPercentage: 0.7,
                hoverBackgroundColor: '#FFE082',
                hoverBorderColor: '#FFD54A',
                hoverBorderWidth: 1
            }]
        },
        options: {
            ...chartBaseOptions,
            plugins: {
                ...chartBaseOptions.plugins,
                tooltip: {
                    backgroundColor: 'rgba(24, 24, 24, 0.95)',
                    titleColor: '#FFD54A',
                    bodyColor: '#ffffff',
                    borderColor: '#FFD54A',
                    borderWidth: 1,
                    cornerRadius: 10,
                    padding: 10
                }
            },
            scales: {
                x: {
                    ...chartBaseOptions.scales.x,
                    grid: { display: false }
                },
                y: {
                    ...chartBaseOptions.scales.y,
                    beginAtZero: true,
                    ticks: { color: '#9f9f9f' }
                }
            }
        }
    });
}

const movieRevenue = Array.isArray(movieData) ? movieData : [];
const formatCurrency = (value) => new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
}).format(value || 0);

if (document.getElementById('movieChart')) {

    const legendRows = document.querySelectorAll(".legend-row");

    function highlightLegend(index) {
        legendRows.forEach(row => row.classList.remove("active"));

        if (index !== null && legendRows[index]) {
            legendRows[index].classList.add("active");
        }
    }

    const movieChart = new Chart(document.getElementById('movieChart'), {
        type: 'doughnut',
        data: {
            labels: movieLabels,
            datasets: [{
                data: movieData,
                backgroundColor: [
                    "#1565C0", // Sapphire Blue
                    "#2E7D32", // Emerald Green
                    "#F4C430", // Popcorn Gold
                    "#D4AF37", // Cinema Gold
                    "#E53935", // Crimson
                    "#B71C1C", // Velvet Red
                    "#6A1B9A", // Royal Purple
                    "#00897B"  // Teal
                ],
                hoverOffset: 35,
                hoverBorderWidth: 5,
                borderWidth: 5,
                borderColor: "#181818",
                animation: {
                    duration: 1500,
                    animateRotate: true,
                    animateScale: true
                },

            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: "72%",
            onHover: (event, elements) => {
                event.native.target.style.cursor =
                    elements.length ? 'pointer' : 'default';
                if (elements.length) {
                    highlightLegend(elements[0].index);
                } else {
                    highlightLegend(null);
                }

            },
            plugins: {
                legend: {
                    display: false
                },
                datalabels: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: "#111",
                    titleColor: "#fff",
                    bodyColor: "#fff",
                    borderColor: "#FFD54F",
                    borderWidth: 1,
                    padding: 12,
                    displayColors: true,

                    callbacks: {
                        title: function (context) {
                            return context[0].label;
                        },

                        label: function (context) {
                            const index = context.dataIndex;

                            const tickets = context.raw; // Same as movieData[index]

                            const revenue = movieRevenue
                                ? movieRevenue[index]
                                : 0;

                            const labels = [
                                
                            ];

                            if (movieRevenue) {
                                labels.push(`💰 Revenue: ₱${Number(revenue).toLocaleString()}`);
                            }

                            return labels;
                        }
                    }
                }
            }
        }
    });
    legendRows.forEach(row => {
        row.addEventListener("mouseenter", () => {
            const index = Number(row.dataset.index);
            movieChart.setActiveElements([
                {
                    datasetIndex: 0,
                    index: index
                }
            ]);
            highlightLegend(index);
            movieChart.update();
        });
        row.addEventListener("mouseleave", () => {
            movieChart.setActiveElements([]);
            highlightLegend(null);
            movieChart.update();
        });
    });
}