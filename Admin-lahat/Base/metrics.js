document.addEventListener("DOMContentLoaded", () => {
    const distributionWrapper = document.querySelector('.distribution-wrapper');

    function updateGenreDistribution() {
        if (!distributionWrapper) return;
        const bars = Array.from(distributionWrapper.querySelectorAll('.bar'));

        // 1. Process and evaluate structural numbers inside rows
        const barData = bars.map(bar => {
            const countEl = bar.querySelector('.count');
            const countText = countEl ? countEl.textContent : '';
            const countValue = parseInt(countText.replace(/\D/g, ''), 10) || 0;
            return { element: bar, count: countValue };
        });

        // 2. Identify maximum reference value for percentage distribution
        //    (guard against an empty bar list, which would make Math.max
        //    return -Infinity instead of a usable number)
        const maxCount = barData.length ? Math.max(...barData.map(d => d.count)) : 0;

        // 3. Arrange from highest top metric down to lowest metric
        barData.sort((a, b) => b.count - a.count);

        // 4. Update width properties and place them arranged into the DOM wrapper
        barData.forEach(item => {
            const percentage = maxCount > 0 ? (item.count / maxCount) * 100 : 0;
            const filledTrack = item.element.querySelector('.number-bar');
            if (filledTrack) {
                filledTrack.style.width = `${percentage}%`;
            }
            distributionWrapper.appendChild(item.element);
        });
    }

    // Run initial sorting and render metrics
    updateGenreDistribution();

    // Bind to the global window object under its own name so it doesn't collide
    // with movie-manager.js's updateGenreDistribution (which recalculates counts).
    // movie-manager.js calls this one after it updates the .count text, so bars
    // get re-sorted and re-widthed on every add/edit/delete.
    window.renderGenreBarWidths = updateGenreDistribution;
});