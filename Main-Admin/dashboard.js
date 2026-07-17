const dashboard = {

    transactions: [

        {
            customer: "John Doe",
            movie: "Avengers",
            ticket: 3,
            total: "₱750"
        },

        {
            customer: "Maria Cruz",
            movie: "Superman",
            ticket: 2,
            total: "₱500"
        },

        {
            customer: "James Lee",
            movie: "Deadpool",
            ticket: 4,
            total: "₱1,000"
        },

        {
            customer: "Claire Santos",
            movie: "Batman",
            ticket: 2,
            total: "₱900"
        }

    ]

};

// Total revenue is calculated by summing the numeric value from each transaction amount.
dashboard.revenue = dashboard.transactions.reduce((sum, item) => {
    const amount = Number(item.total.replace(/[^0-9]/g, ""));
    return sum + amount;
}, 0);

// Total tickets sold across all transactions.
dashboard.tickets = dashboard.transactions.reduce((sum, item) => sum + item.ticket, 0);

// Unique movie count based on distinct movie titles in the transactions.
dashboard.movies = new Set(dashboard.transactions.map(item => item.movie)).size;

// Total number of transactions screened as the number of transaction rows.
dashboard.screenings = dashboard.transactions.length;

/**
 * Animate a numeric dashboard metric from start to end.
 * Updates the element text to create a counting effect.
 * @param {string} id - DOM element id of the metric display.
 * @param {number} start - Starting value for the animation.
 * @param {number} end - Ending value for the animation.
 * @param {number} duration - Total duration of the animation in milliseconds.
 */
function animateValue(id, start, end, duration) {

    let obj = document.getElementById(id);
    let range = end - start;
    let current = start;
    let increment = end > start ? 1 : -1;
    let step = Math.abs(Math.floor(duration / range));

    let timer = setInterval(() => {
        current += increment;
        obj.innerHTML = id == "revenue" ?
            "₱" + current.toLocaleString() :
            current;

        if (current == end) {
            clearInterval(timer);
        }
    }, step);
}

// Animate each dashboard metric when the page loads.
animateValue("revenue", 0, dashboard.revenue, 1500);
animateValue("tickets", 0, dashboard.tickets, 1000);
animateValue("movies", 0, dashboard.movies, 800);
animateValue("screenings", 0, dashboard.screenings, 800);
