// Wrapped in an IIFE so top-level const/let declarations (movieLabels,
// movieData, chartJsAvailable, etc.) stay local to this file instead of
// leaking into the shared global scope. Classic <script src="..."> tags all
// share ONE global lexical scope, so a top-level `const movieLabels` here
// and another top-level `const movieLabels` in sale-report.js would
// otherwise throw "Identifier 'movieLabels' has already been declared" the
// moment this script loads (since sale-report.js loads first) -- which
// silently killed this ENTIRE file: no finance charts, no legend hover, no
// payment tabs, no payment search.
(function () {

// Scope all lookups to the finance section so this script never touches
// elements belonging to other sections (e.g. sale-report) on the same page.
// Wrap the finance markup in <div id="page-finance"> ... </div> (already present in admindup.php).
const financeRoot = document.getElementById("page-finance") || document;

// Data injected by PHP as window.financeData (see admindup.php)
const financeData = window.financeData || {};
const todayRevenue = financeData.todayRevenue || 0;
const weekRevenue = financeData.weekRevenue || 0;
const monthRevenue = financeData.monthRevenue || 0;
const totalRevenue = financeData.totalRevenue || 0;
const movieLabels = financeData.movieLabels || [];
const movieData = financeData.movieData || [];

// ================= Chart setup guard =================

const chartJsAvailable = typeof Chart !== "undefined";

const datalabelsAvailable = chartJsAvailable && typeof ChartDataLabels !== "undefined";

if (!chartJsAvailable) {
    console.error(
        "[finance.js] Chart.js was not found on the page (the `Chart` global is undefined). " +
        "Charts will not render. Check that the <script src=\"...chart.js\"> tag loads successfully " +
        "(open the Network tab and look for a 404 or wrong path)."
    );
} else if (!datalabelsAvailable) {
    console.warn(
        "[finance.js] ChartDataLabels plugin was not found; revenue bars will render without value labels."
    );
}

// ================= Revenue Chart =================

let revenueChart = null;
const revenueChartEl = document.getElementById("financeRevenueChart");

if (chartJsAvailable && revenueChartEl) {
    try {
        revenueChart = new Chart(revenueChartEl, {

            type: "bar",
            plugins: datalabelsAvailable ? [ChartDataLabels] : [],

            data: {
                labels: [
                    "Today",
                    "This Week",
                    "This Month",
                    "Total Revenue"
                ],

                datasets: [{
                    data: [
                        todayRevenue,
                        weekRevenue,
                        monthRevenue,
                        totalRevenue
                    ],

                    backgroundColor: [
                        "#FFD54F", // Today
                        "#FFB300", // This Week
                        "#E53935", // This Month
                        "#B71C1C"  // Total Revenue
                    ],

                    hoverBackgroundColor: [
                        "#FFD54F", // Today
                        "#FFB300", // This Week
                        "#E53935", // This Month
                        "#B71C1C"  // Total Revenue
                    ],

                    categoryPercentage: .55,
                    barPercentage: .75,
                    maxBarThickness: 60,
                    borderRadius: 15,
                    borderSkipped: false,
                    hoverBorderWidth: 3,
                    hoverBorderColor: "#FFD54A",

                    animation: {
                        duration: 1500,
                        easing: "easeOutQuart"
                    }

                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    },
                    // Ito ang number sa loob ng bar
                    datalabels: {
                        color: "#FFFFFF",
                        font: {
                            size: 16,
                        },
                        formatter: (value) => value
                    }
                },
                // Ito ang number sa gilid (Y-axis) at labels sa baba (X-axis)
                scales: {
                    x: {
                        ticks: {
                            color: "#FFFFFF",
                        },
                        grid: {
                            color: "rgba(255,255,255,0.05)"
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: "#FFFFFF",

                        },
                        grid: {
                            color: "rgba(255,255,255,0.05)"
                        }
                    }
                }
            }
        });
    } catch (err) {
        console.error("[finance.js] Failed to create financeRevenueChart:", err);
    }
}

// ================= Legend =================

const legendRows = financeRoot.querySelectorAll(".legend-row");

function highlightLegend(index) {

    legendRows.forEach(row => row.classList.remove("active"));
    if (index !== null && legendRows[index]) {
        legendRows[index].classList.add("active");

    }

}
// ================= Doughnut =================
let ticketChart = null;
const ticketChartEl = document.getElementById("financeTicketChart");

if (chartJsAvailable && ticketChartEl) {
    try {
        ticketChart = new Chart(ticketChartEl, {
            type: "doughnut",
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
                        elements.length ? "pointer" : "default";
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
                        callbacks: {
                            title: function (context) {
                                return movieLabels[context[0].dataIndex];
                            },
                            label: function (context) {
                                return context.raw + " Tickets";
                            }
                        }
                    }
                }
            }
        });
    } catch (err) {
        console.error("[finance.js] Failed to create financeTicketChart:", err);
    }
}

// ================= Legend Hover =================
legendRows.forEach(row => {
    row.addEventListener("mouseenter", () => {
        if (!ticketChart) return;
        const index = Number(row.dataset.index);
        ticketChart.setActiveElements([{
            datasetIndex: 0,
            index: index
        }]);
        highlightLegend(index);
        ticketChart.update();
    });
    row.addEventListener("mouseleave", () => {
        if (!ticketChart) return;
        ticketChart.setActiveElements([]);
        highlightLegend(null);
        ticketChart.update();
    });
});

//payment transaction
const tabs = financeRoot.querySelectorAll('#financePaymentTabs .tab-btn');
let currentFilter = 'all';

function applyTransactionFilter() {
    const paymentSearch = financeRoot.querySelector('#financePaymentSearch');
    const searchValue = paymentSearch ? paymentSearch.value.trim().toLowerCase() : '';
    const rows = financeRoot.querySelectorAll('#financePaymentTable tr');

    rows.forEach(row => {
        const status = (row.dataset.status || '').toLowerCase();
        const text = row.textContent.toLowerCase();
        const matchesFilter = currentFilter === 'all' || status === currentFilter.toLowerCase();
        const matchesSearch = searchValue === '' || text.includes(searchValue);

        row.style.display = matchesFilter && matchesSearch ? '' : 'none';
    });
}

tabs.forEach(tab => {
    tab.addEventListener('click', function () {
        tabs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.dataset.filter || 'all';
        applyTransactionFilter();
    });
});

/* ===========================
   PAYMENT SEARCH
=========================== */
const paymentSearch = financeRoot.querySelector('#financePaymentSearch');

if (paymentSearch) {
    paymentSearch.addEventListener('keyup', applyTransactionFilter);
}

})();