document.addEventListener('DOMContentLoaded', function () {
    // Attach book button handlers (idempotent)
    document.querySelectorAll('.btn-book-ticket, .btn-book-now').forEach(btn => {
        if (btn.dataset._attached === '1') return;
        btn.addEventListener('click', function (e) {
            const mv = this.dataset.movie || this.getAttribute('data-movie');
            if (mv) {
                localStorage.setItem('movie', mv);
                // navigate to booking page with param for compatibility
                window.location.href = 'booking.php?movie=' + encodeURIComponent(mv);
            } else {
                window.location.href = 'booking.php';
            }
        });
        btn.dataset._attached = '1';
    });

    // Synchronize customer type selector across pages
    function wireCustomerType(sel) {
        try {
            const saved = localStorage.getItem('customerType') || 'Regular';
            sel.value = saved;
            if (sel.dataset._attached === '1') return;
            sel.addEventListener('change', function () {
                localStorage.setItem('customerType', this.value);
                // notify other listeners
                window.dispatchEvent(new CustomEvent('customerTypeChanged', { detail: this.value }));
            });
            sel.dataset._attached = '1';
        } catch (e) { /* ignore */ }
    }

    document.querySelectorAll('#customerTypeSelect').forEach(wireCustomerType);

    // If pages dynamically add the select later, observe mutations to wire them
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
