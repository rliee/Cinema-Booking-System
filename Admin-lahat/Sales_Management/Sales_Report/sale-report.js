// Wrapped in an IIFE so top-level const/let declarations (movieLabels,
// movieData, chartJsAvailable, etc.) stay local to this file instead of
// leaking into the shared global scope. Classic <script src="..."> tags all
// share ONE global lexical scope, so a top-level `const movieLabels` here
// and another top-level `const movieLabels` in finance.js would otherwise
// throw "Identifier 'movieLabels' has already been declared" the moment the
// second script loads -- which silently kills that entire script (no
// charts, no tabs, no search), not just the duplicated variable.
(function () {

// Scope all lookups to the sales-report section so this script never touches
// elements belonging to other sections (e.g. finance) on the same page.
// Wrap the sales-report markup in <div id="page-reports"> ... </div> (already present in admindup.php).
const salesReportRoot = document.getElementById("page-reports") || document;

// Data injected by PHP as window.reportData (see admindup.php)
const reportData = window.reportData || {};
const dailyLabels = reportData.dailyLabels || [];
const dailyData = reportData.dailyData || [];
const monthLabels = reportData.monthLabels || [];
const monthData = reportData.monthData || [];
const movieLabels = reportData.movieLabels || [];
const movieData = reportData.movieData || [];

// If Chart.js failed to load (e.g. the <script src="...chart.js"> tag 404'd
// or points at the wrong path), `Chart` is undefined and calling `new
// Chart(...)` below would throw -- which previously aborted this entire
// script, so nothing after the first chart (including the movie-chart legend
// hover wiring) ever ran either. Guarding on this keeps every other chart
// and the legend hover working independently of one another.
const chartJsAvailable = typeof Chart !== "undefined";

if (!chartJsAvailable) {
    console.error(
        "[sale-report.js] Chart.js was not found on the page (the `Chart` global is undefined). " +
        "Charts will not render. Check that the <script src=\"...chart.js\"> tag loads successfully " +
        "(open the Network tab and look for a 404 or wrong path)."
    );
}

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

if (chartJsAvailable && document.getElementById('reportDailyChart')) {
    try {
        new Chart(document.getElementById('reportDailyChart'), {
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
    } catch (err) {
        console.error("[sale-report.js] Failed to create reportDailyChart:", err);
    }
}

if (chartJsAvailable && document.getElementById('reportMonthlyChart')) {
    try {
        new Chart(document.getElementById('reportMonthlyChart'), {
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
    } catch (err) {
        console.error("[sale-report.js] Failed to create reportMonthlyChart:", err);
    }
}

const movieRevenue = Array.isArray(movieData) ? movieData : [];
const formatCurrency = (value) => new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
}).format(value || 0);

if (document.getElementById('reportMovieChart')) {

    const legendRows = salesReportRoot.querySelectorAll(".legend-row");

    function highlightLegend(index) {
        legendRows.forEach(row => row.classList.remove("active"));

        if (index !== null && legendRows[index]) {
            legendRows[index].classList.add("active");
        }
    }

    let movieChart = null;

    if (chartJsAvailable) {
        try {
            movieChart = new Chart(document.getElementById('reportMovieChart'), {
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
        } catch (err) {
            console.error("[sale-report.js] Failed to create reportMovieChart:", err);
        }
    }

    legendRows.forEach(row => {
        row.addEventListener("mouseenter", () => {
            if (!movieChart) return;
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
            if (!movieChart) return;
            movieChart.setActiveElements([]);
            highlightLegend(null);
            movieChart.update();
        });
    });
}

})();