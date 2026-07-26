// ==========================================
// WIRE-UP: populate the Edit Hall Status modal
// when an "Edit Hall" button is clicked, and
// attach the submit handler to the form.
// (Previously missing: data-hall-id/-name/-status-id
// on the button were never read into the modal, and
// handleHallStatusSubmit was never bound to anything.)
// ==========================================
document.addEventListener("DOMContentLoaded", () => {
    document.addEventListener("click", (e) => {
        const trigger = e.target.closest('[data-bs-target="#editHallStatusModal"]');
        if (!trigger) return;

        const modalElement = document.getElementById('editHallStatusModal');
        if (!modalElement) return;

        const hallId = trigger.getAttribute('data-hall-id') || '';
        const hallName = trigger.getAttribute('data-hall-name') || '';
        const statusId = trigger.getAttribute('data-status-id') || '1';

        // Store the active hall id on the modal for handleHallStatusSubmit to read
        modalElement.dataset.activeHallId = hallId;

        const idInput = document.getElementById('editHallId');
        if (idInput) idInput.value = hallId;

        const titleName = document.getElementById('editHallTitleName');
        if (titleName) titleName.textContent = hallName;

        const statusSelect = document.getElementById('editHallStatusSelect');
        if (statusSelect) statusSelect.value = statusId;
    });

    const editHallStatusForm = document.getElementById('editHallStatusForm');
    if (editHallStatusForm) {
        editHallStatusForm.addEventListener('submit', handleHallStatusSubmit);
    }
});

// ==========================================
// Recalculate the hall overview cards
// (Active / Maintenance / Closed / Total Capacity)
// from the hall cards currently in the DOM.
// This function was called but never defined.
// ==========================================
function updateHallMetrics() {
    const cards = document.querySelectorAll('.hall-card');
    if (!cards.length) return;

    let active = 0;
    let maintenance = 0;
    let closed = 0;
    let totalCapacity = 0;

    cards.forEach(card => {
        const badge = card.querySelector('.hall-status-badge');
        const status = badge ? badge.textContent.trim().toLowerCase() : '';

        if (status === 'operational') active++;
        else if (status.includes('maintenance')) maintenance++;
        else if (status.includes('closed') || status.includes('close')) closed++;

        const capacityEl = card.querySelector('.spec-capacity');
        const capacityVal = capacityEl ? parseInt(capacityEl.textContent.replace(/\D/g, ''), 10) || 0 : 0;
        totalCapacity += capacityVal;
    });

    const elActive = document.getElementById('metricActiveHalls');
    const elCapacity = document.getElementById('metricTotalCapacity');
    const elMaintenance = document.getElementById('metricMaintenanceHalls');
    const elClosed = document.getElementById('metricClosedHalls');

    if (elActive) elActive.textContent = active;
    if (elCapacity) elCapacity.textContent = totalCapacity;
    if (elMaintenance) elMaintenance.textContent = maintenance;
    if (elClosed) elClosed.textContent = closed;
}

async function handleHallStatusSubmit(event) {
    event.preventDefault();

    const modalElement = document.getElementById('editHallStatusModal');
    if (!modalElement) return;

    const rawHallId = modalElement.dataset.activeHallId || '';
    const numericHallId = rawHallId.replace(/[^0-9]/g, '');

    const statusSelect = document.getElementById('editHallStatusSelect');
    if (!statusSelect) return;

    const selectedStatusId = statusSelect.value; // "1", "2", or "3"

    try {
        const response = await fetch('Admin/Operations/Cinema_Halls/update_hall_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `hall_id=${encodeURIComponent(numericHallId)}&status=${encodeURIComponent(selectedStatusId)}`
        });

        const result = await response.json();

        if (!result.success) {
            alert('Error updating database: ' + (result.message || 'Unknown error'));
            return;
        }

        // Find matching hall card in the UI
        const allCards = document.querySelectorAll('.hall-card');
        allCards.forEach(card => {
            const subtitle = card.querySelector('.card-subtitle');
            const subText = subtitle ? subtitle.textContent.trim().toLowerCase() : '';
            const cardAttr = card.getAttribute('data-hall-id') || '';

            // Match hall-1, hall-2, or numeric hall ID
            if (cardAttr.includes(numericHallId) || subText.includes(numericHallId)) {
                const badge = card.querySelector('.hall-status-badge');
                if (badge) {
                    // Update badge text and CSS class directly from server response
                    badge.className = `hall-status-badge ${result.badge_class}`;
                    badge.textContent = result.status_name;
                }

                // Zero out seats if status is Maintenance (2) or Closed (3)
                if (result.status_id == 2 || result.status_id == 3) {
                    const specBoxes = card.querySelectorAll('.spec-box');
                    specBoxes.forEach(box => {
                        const label = box.querySelector('.spec-label');
                        if (label && label.textContent.trim().toLowerCase().includes('occupied')) {
                            const val = box.querySelector('.spec-value');
                            if (val) val.textContent = '0';
                        }
                    });

                    const progressText = card.querySelector('.progress-text');
                    if (progressText) progressText.textContent = '0% Full';

                    const progressBar = card.querySelector('.progress-bar .number-bar');
                    if (progressBar) progressBar.style.width = '0%';
                }
            }
        });

        // Recalculate top overview metric counters
        updateHallMetrics();

        // Close modal
        if (typeof bootstrap !== 'undefined') {
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) modalInstance.hide();
        }

    } catch (error) {
        console.error('Network or server response error:', error);
        alert('Failed to parse response or connect to server.');
    }
}