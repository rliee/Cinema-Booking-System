document.addEventListener('DOMContentLoaded', function () {
    // Use event delegation on document body for all book buttons
    // This works for both static buttons (hero) and dynamically rendered ones (movie cards)
    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-book-ticket, .btn-book-now, [data-movie]');
        if (!btn) return;

        const mv = btn.dataset.movie || btn.getAttribute('data-movie');
        if (!mv) return;

        e.preventDefault();
        e.stopPropagation();

        // Check login status
        if (localStorage.getItem('loggedIn') === 'true') {
            localStorage.setItem('movie', mv);
            window.location.href = 'booking.php?movie=' + encodeURIComponent(mv);
        } else {
            // Save pending movie
            localStorage.setItem('pendingMovie', mv);
            localStorage.setItem('movie', mv);

            // Try to show login modal (only exists on index.php)
            const loginModalEl = document.getElementById('loginModal');
            if (loginModalEl && typeof bootstrap !== 'undefined') {
                const modal = bootstrap.Modal.getInstance(loginModalEl) || new bootstrap.Modal(loginModalEl);
                modal.show();
            } else {
                alert('Please log in first to book tickets.');
            }
        }
    });

    // Synchronize customer type selector across pages
    function wireCustomerType(sel) {
        try {
            const saved = localStorage.getItem('customerType') || 'Regular';
            sel.value = saved;
            if (sel.dataset._attached === '1') return;
            sel.addEventListener('change', function () {
                localStorage.setItem('customerType', this.value);
                window.dispatchEvent(new CustomEvent('customerTypeChanged', { detail: this.value }));
            });
            sel.dataset._attached = '1';
        } catch (e) { /* ignore */ }
    }

    document.querySelectorAll('#customerTypeSelect').forEach(wireCustomerType);

    const observer = new MutationObserver(function (mutations) {
        for (const m of mutations) {
            for (const node of m.addedNodes) {
                if (node.querySelectorAll) {
                    node.querySelectorAll('#customerTypeSelect').forEach(wireCustomerType);
                }
            }
        }
    });
    observer.observe(document.body, { childList: true, subtree: true });
});

