document.addEventListener("DOMContentLoaded", () => {
    const distributionWrapper = document.querySelector('.distribution-wrapper');
    
    function updateGenreDistribution() {
        if (!distributionWrapper) return;
        const bars = Array.from(distributionWrapper.querySelectorAll('.bar'));

        // 1. Process and evaluate structural numbers inside rows
        const barData = bars.map(bar => {
            const countText = bar.querySelector('.count').textContent;
            const countValue = parseInt(countText.replace(/\D/g, '')) || 0;
            return { element: bar, count: countValue };
        });

        // 2. Identify maximum reference value for percentage distribution
        const maxCount = Math.max(...barData.map(d => d.count));

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
    
    // Bind to the global window object to allow other scripts (like edit/add) to trigger refreshes
    window.updateGenreDistribution = updateGenreDistribution;
});