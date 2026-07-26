window.isSeatEditing = false;
window.currentSleHallId = '1';
window.currentSleHallStatusId = '1'; // 1 = Operational, 2 = Maintenance, 3 = Closed

document.addEventListener("DOMContentLoaded", () => {
    const editHallModal = document.getElementById('editHallStatusModal');
    const editHallForm = document.getElementById('editHallStatusForm');
    const viewSeatsModal = document.getElementById('viewSeatsModal');

    // ---------------------------------------------------------
    // 1. LISTEN FOR EDIT HALL MODAL OPEN
    // ---------------------------------------------------------
    if (editHallModal) {
        editHallModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            if (!button) return;

            const hallCard = button.closest('.hall-card') || button.closest('.hall-card-item');

            let rawHallId = button.getAttribute('data-hall-id')
                || (hallCard ? hallCard.getAttribute('data-hall-id') || hallCard.getAttribute('data-id') : '')
                || '';

            if (!rawHallId && hallCard) {
                const subtitle = hallCard.querySelector('.card-subtitle') || hallCard.querySelector('.hall-id');
                if (subtitle) rawHallId = subtitle.textContent;
            }

            const numericId = rawHallId.replace(/[^0-9]/g, '');
            const hallName = button.getAttribute('data-hall-name') || (numericId ? `Hall ${numericId}` : '');

            const hiddenIdInput = document.getElementById('editHallId');
            if (hiddenIdInput) hiddenIdInput.value = numericId;
            editHallModal.dataset.activeHallId = numericId;

            const titleSpan = document.getElementById('editHallTitleName');
            if (titleSpan) {
                titleSpan.textContent = hallName ? `(${hallName})` : '';
            }

            let currentStatus = button.getAttribute('data-status-id');
            if (!currentStatus && hallCard) {
                const badge = hallCard.querySelector('.hall-status-badge');
                if (badge) {
                    const badgeText = badge.textContent.trim().toLowerCase();
                    if (badge.classList.contains('maintenance') || badge.classList.contains('under-maintenance') || badgeText.includes('maintenance')) {
                        currentStatus = '2';
                    } else if (badge.classList.contains('closed') || badgeText.includes('closed')) {
                        currentStatus = '3';
                    } else {
                        currentStatus = '1';
                    }
                }
            }

            const statusSelect = document.getElementById('editHallStatusSelect');
            if (statusSelect) {
                statusSelect.value = currentStatus || '1';
            }
        });
    }

    // ---------------------------------------------------------
    // 2. LISTEN FOR VIEW SEATS MODAL OPEN
    // ---------------------------------------------------------
    if (viewSeatsModal) {
        viewSeatsModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            if (!button) return;

            const hallCard = button.closest('.hall-card') || button.closest('.hall-card-item');

            let rawHallId = button.getAttribute('data-hall-id')
                || (hallCard ? hallCard.getAttribute('data-hall-id') || hallCard.getAttribute('data-id') : '')
                || '1';

            const numericId = rawHallId.replace(/[^0-9]/g, '') || '1';
            const hallTitle = hallCard ? hallCard.querySelector('.card-title')?.textContent : `Cinema Hall ${numericId}`;

            const subtitleElem = viewSeatsModal.querySelector('.id-subtitle');
            if (subtitleElem) {
                subtitleElem.textContent = `CINEMA HALL-${numericId} · ${hallTitle || 'Auditorium'}`;
            }

            renderSeatGrid(numericId, 'modal-dynamic-grid', {
                available: 'legend-available-count',
                occupied: 'legend-occupied-count',
                unavailable: 'legend-unavailable-count'
            });
        });
    }

    // ---------------------------------------------------------
    // 3. EDIT HALL FORM SUBMISSION
    // ---------------------------------------------------------
    if (editHallForm) {
        editHallForm.removeEventListener('submit', handleHallStatusSubmit);
        editHallForm.addEventListener('submit', handleHallStatusSubmit);
    }

    // ---------------------------------------------------------
    // 4. "EDIT LAYOUT" & "DONE EDITING" TOGGLE HANDLER
    // ---------------------------------------------------------
    let targetEditBtn = document.getElementById('editLayoutBtn')
        || document.querySelector('.btn-edit-layout, .sle-btn-action-edit');

    if (!targetEditBtn) {
        document.querySelectorAll('button').forEach(btn => {
            if (btn.textContent.includes('Edit Layout') || btn.textContent.includes('Done Editing')) {
                targetEditBtn = btn;
            }
        });
    }

    const editBtnLabel = targetEditBtn ? targetEditBtn.innerHTML : '';
    window.sleEditLayoutBtn = targetEditBtn;
    window.sleEditBtnDefaultLabel = editBtnLabel;

    if (targetEditBtn) {
        targetEditBtn.addEventListener('click', async () => {
            if (isHallLocked(window.currentSleHallStatusId)) {
                alert('This hall is under maintenance or closed. Seat editing is disabled until it is set back to Operational.');
                return;
            }

            if (window.isSeatEditing) {
                // Save layout permanently to the database
                targetEditBtn.disabled = true;
                const savedSuccessfully = await saveEntireHallLayout(window.currentSleHallId, 'sle-dynamic-grid');

                if (savedSuccessfully) {
                    alert('Seat layout successfully saved!');
                    window.isSeatEditing = false;
                    targetEditBtn.innerHTML = editBtnLabel;
                    targetEditBtn.classList.remove('is-editing', 'btn-success');
                    targetEditBtn.classList.add('btn-outline-warning');

                    // Refresh grid straight from the DB so colors reflect what was actually saved
                    switchHallSeats(window.currentSleHallId);
                } else {
                    alert('Save failed or returned an error from save_layout.php. Edits remain on screen.');
                }
                targetEditBtn.disabled = false;
            } else {
                // Enable Edit Mode
                window.isSeatEditing = true;
                targetEditBtn.innerHTML = 'Done Editing';
                targetEditBtn.classList.remove('btn-outline-warning', 'btn-outline-light');
                targetEditBtn.classList.add('is-editing', 'btn-success');

                // Re-render grid to attach seat click handlers
                switchHallSeats(window.currentSleHallId);
            }
        });
    }

    // Manual Save Layout buttons listener
    document.querySelectorAll('.save-changes-btn, [id*="saveChanges"], [class*="save-layout"]').forEach(saveBtn => {
        saveBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            const success = await saveEntireHallLayout(window.currentSleHallId, 'sle-dynamic-grid');
            if (success) {
                alert('Seat layout successfully saved to database!');
                switchHallSeats(window.currentSleHallId);
            }
        });
    });

    // ---------------------------------------------------------
    // 5. HALL TABS / SELECTOR BUTTONS LISTENER
    // ---------------------------------------------------------
    const hallTabs = document.querySelectorAll('.sle-hall-item-row, .hall-tab, .hall-card, .sle-hall-card, .hall-select-item');
    hallTabs.forEach(tab => {
        tab.addEventListener('click', async function (e) {
            // Ignore clicks originating from seat buttons or edit status buttons
            if (e.target.closest('.sle-p-seat') || e.target.closest('button.btn-edit-status')) return;

            const rawId = this.getAttribute('data-hall-id') || this.getAttribute('data-id') || '';
            const numericId = rawId.replace(/[^0-9]/g, '');

            if (numericId && numericId !== window.currentSleHallId) {
                if (window.isSeatEditing) {
                    await saveEntireHallLayout(window.currentSleHallId, 'sle-dynamic-grid');
                }

                hallTabs.forEach(t => t.classList.remove('sle-status-selected', 'active', 'selected'));
                this.classList.add('sle-status-selected', 'active');

                switchHallSeats(numericId);
            }
        });
    });

    // ---------------------------------------------------------
    // 6. INITIALIZE HALL CARDS SPECS ON PAGE LOAD
    // ---------------------------------------------------------
    const hallCards = document.querySelectorAll('.hall-card, .hall-card-item');
    hallCards.forEach(card => {
        const rawId = card.getAttribute('data-hall-id') || card.getAttribute('data-id') || '';
        const numericId = rawId.replace(/[^0-9]/g, '');

        const capacityElem = card.querySelector('.spec-capacity');
        if (capacityElem) capacityElem.textContent = '144';

        if (numericId) {
            fetch(`/Movie_Booking_System/Admin/Operations/Seat_Layout/get_layout.php?hall_id=${numericId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success || data.status === 'success') {
                        updateHallCardSpecs(numericId, data.capacity || 144, data.occupied || 0, data.unavailable || 0);
                    }
                })
                .catch(() => { });
        }
    });

    updateHallMetrics();

    const initialSelectedHall = document.querySelector('.sle-hall-item-row.sle-status-selected, .hall-tab.active');
    const initialHallId = initialSelectedHall ? initialSelectedHall.getAttribute('data-hall-id') : '1';
    switchHallSeats(initialHallId);
});

/**
 * FORM SUBMISSION: Update hall status
 */
async function handleHallStatusSubmit(event) {
    event.preventDefault();

    const editForm = document.getElementById('editHallStatusForm');
    const modalElement = document.getElementById('editHallStatusModal');
    if (!editForm || !modalElement) return;

    const formData = new FormData(editForm);

    let hallId = formData.get('hall_id') || document.getElementById('editHallId')?.value;
    if (!hallId || hallId === '0') {
        const rawTargetId = modalElement.dataset.activeHallId || '';
        hallId = rawTargetId.replace(/[^0-9]/g, '');
    }

    hallId = String(hallId).replace(/[^0-9]/g, '');
    formData.set('hall_id', hallId);

    const statusSelect = document.getElementById('editHallStatusSelect');
    const selectedStatusId = formData.get('status_id') || formData.get('status') || (statusSelect ? statusSelect.value : '1');
    formData.set('status_id', selectedStatusId);

    if (!hallId || hallId === '0') {
        alert('Error: Could not identify Hall ID.');
        return;
    }

    try {
        const response = await fetch('/Movie_Booking_System/Admin/Operations/Cinema_Halls/update_hall_status.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) return;

        const rawText = await response.text();
        let result;

        try {
            result = JSON.parse(rawText.trim());
        } catch (e) {
            return;
        }

        if (!result.success && result.status !== 'success') {
            alert('Database update failed: ' + (result.message || 'Unknown error'));
            return;
        }

        updateCardUI(hallId, selectedStatusId, result);
        updateHallMetrics();
        syncSeatLayoutHallLock(hallId, selectedStatusId, result);

        if (typeof bootstrap !== 'undefined') {
            const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            if (modalInstance) modalInstance.hide();
        }

        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';

    } catch (error) {
        console.error('Fetch/Network Error:', error);
    }
}

/**
 * Keeps the Seat Layout Editor in sync after a hall status change:
 * - updates that hall's data-status-id / lock badge in the "Select Hall" list
 * - if that hall is currently open in the editor, re-applies the edit lock
 *   and refreshes the grid (maintenance/closed halls have their seats
 *   auto-reset to Available server-side, so counts go back to 0)
 */
function syncSeatLayoutHallLock(numericHallId, selectedStatusId, serverData = null) {
    const hallRow = document.querySelector(`.sle-hall-item-row[data-hall-id="hall-${numericHallId}"]`);
    if (hallRow) {
        hallRow.setAttribute('data-status-id', String(selectedStatusId));

        let badge = hallRow.querySelector('.sle-hall-lock-badge');
        const locked = isHallLocked(selectedStatusId);
        hallRow.classList.toggle('sle-hall-locked', locked);

        if (locked) {
            const statusLabel = (serverData && serverData.status_name) ? serverData.status_name : (selectedStatusId === '2' || selectedStatusId === 2 ? 'Under Maintenance' : 'Closed');
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'sle-hall-lock-badge';
                hallRow.appendChild(badge);
            }
            badge.title = `Editing disabled — hall is ${statusLabel}`;
            badge.textContent = `🔒 ${statusLabel}`;
        } else if (badge) {
            badge.remove();
        }
    }

    // If the hall currently open in the editor was the one changed, refresh it
    // (this both re-applies the lock state and reloads seats, which will now
    // show as Available since the server clears them on maintenance/closed).
    if (String(numericHallId) === String(window.currentSleHallId)) {
        switchHallSeats(numericHallId);
    } else {
        // Not the open hall, but its counts likely changed (reset to 0
        // occupied/unavailable) — refresh just its hall-card spec numbers.
        fetch(`/Movie_Booking_System/Admin/Operations/Seat_Layout/get_layout.php?hall_id=${numericHallId}`)
            .then(res => res.json())
            .then(data => {
                if (data && (data.success || data.status === 'success')) {
                    updateHallCardSpecs(numericHallId, data.capacity || 144, data.occupied || 0, data.unavailable || 0);
                    updateHallMetrics();
                }
            })
            .catch(() => {});
    }
}

/**
 * Updates visual badge and capacity info for hall cards
 */
function updateCardUI(numericHallId, selectedStatusId, serverData = null) {
    const allHallCards = document.querySelectorAll('.hall-card, .hall-card-item');

    allHallCards.forEach(card => {
        const cardAttr = card.getAttribute('data-hall-id') || card.getAttribute('data-id') || '';
        const subtitle = card.querySelector('.card-subtitle') || card.querySelector('.hall-id');
        const subText = subtitle ? subtitle.textContent : '';

        const cardNumericId = (cardAttr || subText).replace(/[^0-9]/g, '');

        if (cardNumericId && cardNumericId === String(numericHallId)) {
            const badge = card.querySelector('.hall-status-badge');
            if (badge) {
                badge.className = 'hall-status-badge';

                if (serverData && serverData.status_name) {
                    badge.textContent = serverData.status_name;
                    if (serverData.badge_class) {
                        const classes = serverData.badge_class.trim().split(/\s+/);
                        classes.forEach(c => { if (c) badge.classList.add(c); });
                    }
                } else {
                    if (selectedStatusId === '1') {
                        badge.textContent = 'OPERATIONAL';
                        badge.classList.add('operational');
                    } else if (selectedStatusId === '2') {
                        badge.textContent = 'UNDER MAINTENANCE';
                        badge.classList.add('maintenance', 'under-maintenance');
                    } else if (selectedStatusId === '3') {
                        badge.textContent = 'CLOSED';
                        badge.classList.add('closed');
                    }
                }
            }
        }
    });
}

/**
 * Recalculates and updates top metric summary cards
 */
function updateHallMetrics() {
    const allHallCards = document.querySelectorAll('.hall-card, .hall-card-item');

    let activeCount = 0;
    let maintenanceCount = 0;
    let closedCount = 0;
    let totalCapacity = 0;

    allHallCards.forEach(card => {
        const badge = card.querySelector('.hall-status-badge');
        let capacity = 144;

        const specBoxes = card.querySelectorAll('.spec-box');
        specBoxes.forEach(box => {
            const label = box.querySelector('.spec-label');
            if (label && label.textContent.trim().toLowerCase().includes('capacity')) {
                const val = box.querySelector('.spec-value');
                if (val) {
                    capacity = parseInt(val.textContent.trim(), 10) || 144;
                }
            }
        });

        totalCapacity += capacity;

        if (badge) {
            const badgeText = badge.textContent.trim().toLowerCase();
            if (badge.classList.contains('operational') || badgeText.includes('operational')) {
                activeCount++;
            } else if (badge.classList.contains('maintenance') || badge.classList.contains('under-maintenance') || badgeText.includes('maintenance')) {
                maintenanceCount++;
            } else if (badge.classList.contains('closed') || badgeText.includes('closed')) {
                closedCount++;
            }
        }
    });

    if (allHallCards.length === 0) totalCapacity = 144 * 4;

    const metricActive = document.getElementById('metricActiveHalls');
    const metricCapacity = document.getElementById('metricTotalCapacity');
    const metricMaint = document.getElementById('metricMaintenanceHalls');
    const metricClosed = document.getElementById('metricClosedHalls');

    if (metricActive) metricActive.textContent = activeCount;
    if (metricCapacity) metricCapacity.textContent = totalCapacity;
    if (metricMaint) metricMaint.textContent = maintenanceCount;
    if (metricClosed) metricClosed.textContent = closedCount;
}

/**
 * A hall is locked for editing while it's Under Maintenance (2) or Closed (3).
 */
function isHallLocked(statusId) {
    const id = String(statusId);
    return id === '2' || id === '3';
}

/**
 * Enables/disables the Edit Layout button and shows/hides the lock notice
 * based on the currently selected hall's status.
 */
function updateEditLockUI() {
    const btn = window.sleEditLayoutBtn || document.getElementById('editLayoutBtn');
    const notice = document.getElementById('sle-lock-notice');
    const locked = isHallLocked(window.currentSleHallStatusId);

    if (locked && window.isSeatEditing) {
        // Hall status changed to maintenance/closed while mid-edit: force exit edit mode.
        window.isSeatEditing = false;
        if (btn) {
            btn.innerHTML = window.sleEditBtnDefaultLabel || 'Edit Layout';
            btn.classList.remove('is-editing', 'btn-success');
            btn.classList.add('btn-outline-warning');
        }
    }

    if (btn) {
        btn.disabled = locked;
        btn.classList.toggle('sle-btn-disabled', locked);
    }
    if (notice) {
        notice.classList.toggle('d-none', !locked);
    }
}

/**
 * Switch hall seat view
 */
function switchHallSeats(rawHallId) {
    const numericId = String(rawHallId).replace(/[^0-9]/g, '') || '1';
    window.currentSleHallId = numericId;

    // Pull the hall's live status from its row in the "Select Hall" list
    const hallRow = document.querySelector(`.sle-hall-item-row[data-hall-id="hall-${numericId}"]`);
    window.currentSleHallStatusId = hallRow ? (hallRow.getAttribute('data-status-id') || '1') : '1';
    updateEditLockUI();

    const titleElem = document.getElementById('sle-current-hall-title');
    if (titleElem) {
        titleElem.textContent = `Cinema Hall ${numericId}`;
    }

    renderSeatGrid(numericId, 'sle-dynamic-grid', {
        available: 'sle-count-available',
        occupied: 'sle-count-occupied',
        unavailable: 'sle-count-unavailable'
    });
}

/**
 * Recalculates legend and hall card specs
 */
function refreshLayoutCounters(containerId, numericHallId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    let avail = 0, occup = 0, unav = 0;

    const seats = container.querySelectorAll('.sle-p-seat');
    seats.forEach(s => {
        if (s.classList.contains('available')) avail++;
        else if (s.classList.contains('occupied')) occup++;
        else if (s.classList.contains('unavailable')) unav++;
    });

    if (containerId === 'sle-dynamic-grid') {
        const avEl = document.getElementById('sle-count-available');
        const ocEl = document.getElementById('sle-count-occupied');
        const unEl = document.getElementById('sle-count-unavailable');
        if (avEl) avEl.textContent = avail;
        if (ocEl) ocEl.textContent = occup;
        if (unEl) unEl.textContent = unav;
    } else if (containerId === 'modal-dynamic-grid') {
        const avEl = document.getElementById('legend-available-count');
        const ocEl = document.getElementById('legend-occupied-count');
        const unEl = document.getElementById('legend-unavailable-count');
        if (avEl) avEl.textContent = avail;
        if (ocEl) ocEl.textContent = occup;
        if (unEl) unEl.textContent = unav;
    }

    const totalCap = avail + occup + unav || 144;
    updateHallCardSpecs(numericHallId, totalCap, occup, unav);
    updateHallMetrics();
}

/**
 * Universal Seat Grid Renderer Connected to `get_layout.php`
 */
async function renderSeatGrid(hallId, containerId, legendIds = {}) {
    const gridContainer = document.getElementById(containerId);
    if (!gridContainer) return;

    let availableCount = 0;
    let occupiedCount = 0;
    let unavailableCount = 0;

    const numericHallId = parseInt(String(hallId).replace(/[^0-9]/g, '') || '1', 10);
    const seatMap = {};

    try {
        const response = await fetch(`/Movie_Booking_System/Admin/Operations/Seat_Layout/get_layout.php?hall_id=${numericHallId}`);
        if (response.ok) {
            const rawText = await response.text();
            let data;
            try { data = JSON.parse(rawText.trim()); } catch (e) { }

            if (data && (data.success || data.status === 'success') && Array.isArray(data.seats)) {
                data.seats.forEach(s => {
                    // Extract numeric status (0 = Occupied, 1 = Available, 2 = Unavailable)
                    const statusVal = s.seat_status !== undefined ? s.seat_status : s.status;
                    const rawStatus = parseInt(statusVal, 10);

                    let statusText = 'available';
                    if (rawStatus === 0) statusText = 'occupied';
                    else if (rawStatus === 2) statusText = 'unavailable';

                    let g = parseInt(s.group_number, 10);
                    let sNum = parseInt(s.seat_number, 10);

                    // If DB returns seat_row (A..L) & seat_number (1..12), translate to group (1..4) & sNum (1..36)
                    if (isNaN(g) || isNaN(sNum) || sNum > 36) {
                        const rowStr = (s.seat_row || '').toUpperCase();
                        const colNum = parseInt(s.seat_number || s.raw_seat_number, 10);

                        if (rowStr && !isNaN(colNum)) {
                            const rowCode = rowStr.charCodeAt(0);
                            let rIdx = 0;
                            let group = 1;

                            if (rowCode >= 65 && rowCode <= 70) {      // Rows A-F
                                rIdx = rowCode - 65;
                                group = colNum <= 6 ? 1 : 2;
                            } else if (rowCode >= 71 && rowCode <= 76) { // Rows G-L
                                rIdx = rowCode - 71;
                                group = colNum <= 6 ? 3 : 4;
                            }

                            const cIdx = colNum <= 6 ? (colNum - 1) : (colNum - 7);
                            g = group;
                            sNum = (rIdx * 6) + cIdx + 1;
                        }
                    }

                    const label = s.seat_label || (s.seat_row ? `${s.seat_row}${s.seat_number || ''}` : '');

                    if (!isNaN(g) && !isNaN(sNum)) {
                        seatMap[`${g}_${sNum}`] = {
                            status: statusText,
                            label: label
                        };
                    }
                });

                updateHallCardSpecs(
                    numericHallId,
                    data.capacity || 144,
                    data.occupied || 0,
                    data.unavailable || 0
                );
            }
        }
    } catch (error) {
        console.warn('Error fetching seat data:', error);
    }

    gridContainer.innerHTML = '';

    for (let currentGroup = 1; currentGroup <= 4; currentGroup++) {
        const groupElement = document.createElement('div');
        groupElement.className = 'sle-page-group';

        for (let seatIndex = 1; seatIndex <= 36; seatIndex++) {
            const seatButton = document.createElement('button');
            const key = `${currentGroup}_${seatIndex}`;

            const seatInfo = seatMap[key];
            const customStatus = seatInfo ? seatInfo.status : 'available';

            let label = seatInfo ? seatInfo.label : '';
            if (!label) {
                const sIdx = seatIndex - 1;
                const rIdx = Math.floor(sIdx / 6);
                const cIdx = sIdx % 6;
                if (currentGroup === 1) label = `${String.fromCharCode(65 + rIdx)}${1 + cIdx}`;
                else if (currentGroup === 2) label = `${String.fromCharCode(65 + rIdx)}${7 + cIdx}`;
                else if (currentGroup === 3) label = `${String.fromCharCode(71 + rIdx)}${1 + cIdx}`;
                else if (currentGroup === 4) label = `${String.fromCharCode(71 + rIdx)}${7 + cIdx}`;
                else label = `S${seatIndex}`;
            }

            if (customStatus === 'available') availableCount++;
            else if (customStatus === 'occupied') occupiedCount++;
            else if (customStatus === 'unavailable') unavailableCount++;

            seatButton.type = 'button';
            seatButton.className = `sle-p-seat ${customStatus}`;
            seatButton.title = `${label} (${customStatus.toUpperCase()})`;
            seatButton.textContent = label;
            seatButton.setAttribute('data-group', currentGroup);
            seatButton.setAttribute('data-seat', seatIndex);

            // Only the Seat Layout Editor grid is ever clickable, and only while edit mode is on.
            // The "View Seats" modal (modal-dynamic-grid) is always read-only.
            const isInteractive = window.isSeatEditing && containerId === 'sle-dynamic-grid';

            if (isInteractive) {
                seatButton.classList.add('is-editable');
                seatButton.addEventListener('click', function (e) {
                    e.stopPropagation();

                    let nextStatus = 'available';
                    if (this.classList.contains('available')) {
                        nextStatus = 'occupied';
                    } else if (this.classList.contains('occupied')) {
                        nextStatus = 'unavailable';
                    } else if (this.classList.contains('unavailable')) {
                        nextStatus = 'available';
                    }

                    this.classList.remove('available', 'occupied', 'unavailable');
                    this.classList.add(nextStatus);
                    this.title = `${label} (${nextStatus.toUpperCase()})`;

                    refreshLayoutCounters(containerId, numericHallId);
                });
            } else {
                seatButton.classList.add('is-view-only');
                seatButton.disabled = (containerId === 'modal-dynamic-grid');
            }

            groupElement.appendChild(seatButton);
        }

        gridContainer.appendChild(groupElement);
    }

    if (legendIds.available) {
        const el = document.getElementById(legendIds.available);
        if (el) el.textContent = availableCount;
    }
    if (legendIds.occupied) {
        const el = document.getElementById(legendIds.occupied);
        if (el) el.textContent = occupiedCount;
    }
    if (legendIds.unavailable) {
        const el = document.getElementById(legendIds.unavailable);
        if (el) el.textContent = unavailableCount;
    }
}
/**
 * Saves current seat layout to `save_layout.php`
 */
async function saveEntireHallLayout(numericHallId, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return false;

    const groupA = new Array(36).fill(1);
    const groupB = new Array(36).fill(1);
    const groupC = new Array(36).fill(1);
    const groupD = new Array(36).fill(1);

    const flatSeats = [];

    const seats = container.querySelectorAll('.sle-p-seat');
    seats.forEach(seat => {
        const groupNum = parseInt(seat.getAttribute('data-group'), 10) || 1;
        const seatNum = parseInt(seat.getAttribute('data-seat'), 10) || 1;

        let statusVal = 1; // 1 = available
        if (seat.classList.contains('occupied')) statusVal = 0;
        else if (seat.classList.contains('unavailable')) statusVal = 2;

        const groupArrays = [groupA, groupB, groupC, groupD];
        if (groupArrays[groupNum - 1] && seatNum >= 1 && seatNum <= 36) {
            groupArrays[groupNum - 1][seatNum - 1] = statusVal;
        }

        flatSeats.push({
            group_number: groupNum,
            seat_number: seatNum,
            status: statusVal
        });
    });

    const payload = {
        hall_id: parseInt(numericHallId, 10),
        layout: {
            groupA: groupA,
            groupB: groupB,
            groupC: groupC,
            groupD: groupD
        },
        seats: flatSeats
    };

    try {
        const response = await fetch('/Movie_Booking_System/Admin/Operations/Seat_Layout/save_layout.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (!response.ok) return false;

        const rawText = await response.text();
        let result;

        try {
            result = JSON.parse(rawText.trim());
        } catch (e) {
            console.error('save_layout.php response is not valid JSON:', rawText);
            return false;
        }

        if (result.status === 'success' || result.success === true) {
            return true;
        } else {
            console.warn('Backend save issue:', result.message);
            return false;
        }
    } catch (err) {
        console.error('Network error during layout save:', err);
        return false;
    }
}

/**
 * Updates spec values in Hall Cards
 */
function updateHallCardSpecs(numericHallId, capacity, occupied, unavailable) {
    const allCards = document.querySelectorAll('.hall-card, .hall-card-item');

    allCards.forEach(card => {
        const rawAttr = card.getAttribute('data-hall-id') || card.getAttribute('data-id') || '';
        const cardNumericId = rawAttr.replace(/[^0-9]/g, '');

        if (cardNumericId === String(numericHallId)) {
            const capacityElem = card.querySelector('.spec-capacity');
            const occupiedElem = card.querySelector('.spec-occupied');
            const unavailableElem = card.querySelector('.spec-unavailable');

            if (capacityElem) capacityElem.textContent = capacity || 144;
            if (occupiedElem) occupiedElem.textContent = occupied;
            if (unavailableElem) unavailableElem.textContent = unavailable;
        }
    });
}