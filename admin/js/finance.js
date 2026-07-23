Chart.register(ChartDataLabels);

// ================= Revenue Chart =================

const revenueChart = new Chart(document.getElementById("revenueChart"), {

    type: "bar",

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
// ================= Legend =================

const legendRows = document.querySelectorAll(".legend-row");

function highlightLegend(index) {

    legendRows.forEach(row => row.classList.remove("active"));
    if (index !== null && legendRows[index]) {
        legendRows[index].classList.add("active");

    }

}
// ================= Doughnut =================
const ticketChart = new Chart(document.getElementById("ticketChart"), {
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
// ================= Legend Hover =================
legendRows.forEach(row => {
    row.addEventListener("mouseenter", () => {
        const index = Number(row.dataset.index);
        ticketChart.setActiveElements([{
            datasetIndex: 0,
            index: index
        }]);
        highlightLegend(index);
        ticketChart.update();
    });
    row.addEventListener("mouseleave", () => {
        ticketChart.setActiveElements([]);
        highlightLegend(null);
        ticketChart.update();
    });
});
//payment transaction
const tabs = document.querySelectorAll('#paymentTabs .tab-btn');
let currentFilter = 'all';

function applyTransactionFilter() {
    const paymentSearch = document.getElementById('paymentSearch');
    const searchValue = paymentSearch ? paymentSearch.value.trim().toLowerCase() : '';
    const rows = document.querySelectorAll('#paymentTable tr');

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
const paymentSearch = document.getElementById('paymentSearch');

if (paymentSearch) {
    paymentSearch.addEventListener('keyup', applyTransactionFilter);
}


