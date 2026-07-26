// Reference search and filter controls
const salesSearch = document.getElementById("salesSearch");
const salesDateFilter = document.getElementById("salesDateFilter");
const salesStatusFilter = document.getElementById("salesStatusFilter");

// Tab buttons and panel references (scoped to the Customer Transactions page)
const salesTabs = document.querySelectorAll("#page-sales .tab-btn");
const salesTableControls = document.getElementById("salesTableControls");
const salesWeeklyPanel = document.getElementById("salesWeeklyPanel");
const salesWeeklyTotal = document.getElementById("salesWeeklyTotal");
const salesWeeklyDays = document.getElementById("salesWeeklyDays");

// Current active tab state
let salesActiveTab = "completed";
const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
const chartDays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

/**
 * Compute the weekly revenue totals from the transaction table.
 * Groups the current rows by weekday and updates the weekly summary panel.
 */
function computeWeeklyRevenue() {
  const rows = document.querySelectorAll("#salesTransactionTable tbody tr");
  const totals = {
    Monday: 0,
    Tuesday: 0,
    Wednesday: 0,
    Thursday: 0,
    Friday: 0,
    Saturday: 0,
    Sunday: 0
  };
  let weeklySum = 0;

  rows.forEach(row => {
    if (!row.cells || row.cells.length < 7) return;

    // Only Completed transactions count toward weekly revenue —
    // Pending and Cancelled bookings haven't actually been paid out
    // (or were refunded), so they're excluded from the total.
    const status = row.cells[6].innerText.trim().toLowerCase();
    if (status !== "completed") return;

    const amount = Number(
      (row.cells[4].dataset.amount || row.cells[4].innerText)
        .replace(/[₱,\s]/g, "")
    ) || 0;

    const dateText = row.cells[5].innerText;
    const date = new Date(dateText);
    if (isNaN(date)) return;
    const weekday = dayNames[date.getDay()];
    totals[weekday] += amount;
    weeklySum += amount;
  });

  const maximumValue = Math.max(...Object.values(totals), 1);
  salesWeeklyTotal.textContent = `₱${weeklySum.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  salesWeeklyDays.innerHTML = chartDays.map(day => {
    const barWidth = totals[day] === 0 ? 0 : (totals[day] / maximumValue) * 100;

    return `<div class="bar-group">
      <span class="bar-label">${day}</span>
      <div class="bar-track">
        <div class="bar-fill" style="width:${barWidth}%"></div>
      </div>
      <span class="bar-value">₱${totals[day].toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
    </div>`;
  }).join("");
}

/**
 * Switches between the transaction table view and the weekly revenue view.
 */
function updateSalesView() {
  const showTable = salesActiveTab !== "weekly";
  const hideStatusFilter = salesActiveTab === "completed" || salesActiveTab === "pending" || salesActiveTab === "cancelled";
  salesTableControls.style.display = showTable ? "flex" : "none";
  salesStatusFilter.style.display = hideStatusFilter ? "none" : "inline-block";
  document.getElementById("salesTransactionTable").style.display = showTable ? "table" : "none";
  salesWeeklyPanel.style.display = showTable ? "none" : "block";

  if (salesActiveTab === "weekly") {
    computeWeeklyRevenue();
  } else {
    filterSalesRows();
  }
}

/**
 * Filters transaction rows based on search text, date, status, and active tab.
 */
function filterSalesRows() {
  if (salesActiveTab === "weekly") {
    updateSalesView();
    return;
  }

  const filter = salesSearch.value.toLowerCase();
  const selectedDate = salesDateFilter.value;
  const selectedStatus = salesStatusFilter.value.toLowerCase();
  const rows = document.querySelectorAll("#salesTransactionTable tbody tr");

  rows.forEach(row => {
    if (!row.cells || row.cells.length < 7) return;

    const text = row.innerText.toLowerCase();
    const date = row.cells[5].innerText;
    const status = row.cells[6].innerText.trim().toLowerCase();
    const matchesSearch = text.includes(filter);
    const matchesDate = selectedDate === "" || date === selectedDate;
    const matchesStatus = selectedStatus === "" || status === selectedStatus;
    const matchesTab = salesActiveTab === "all" || status === salesActiveTab;

    row.style.display = (matchesSearch && matchesDate && matchesStatus && matchesTab) ? "" : "none";
  });
}

if (salesSearch && salesDateFilter && salesStatusFilter) {
  salesSearch.addEventListener("keyup", filterSalesRows);
  salesDateFilter.addEventListener("change", filterSalesRows);
  salesStatusFilter.addEventListener("change", filterSalesRows);

  salesTabs.forEach(tab => {
    tab.addEventListener("click", () => {
      salesActiveTab = tab.dataset.tab;
      salesTabs.forEach(button => button.classList.toggle("active", button === tab));
      updateSalesView();
    });
  });

  updateSalesView();
}

/**
 * Opens the booking detail modal and injects transaction details.
 */
let currentTransactionCode = "";
let currentTransactionAmount = 0;

// Convenience fee deducted from the refund total when a booking is cancelled.
const CONVENIENCE_FEE = 20;

function salesDetails(transactionCode, name, customerNumber, movie, amount, seats, tickets, status) {
  currentTransactionCode = transactionCode;

  // Strip the peso sign/commas so we can do math on it later (refund calc).
  currentTransactionAmount = Number(String(amount).replace(/[₱,\s]/g, "")) || 0;

  const normalizedStatus = status.trim().toLowerCase();
  const refundAmount = Math.max(currentTransactionAmount - CONVENIENCE_FEE, 0);
  const formattedRefund = `₱${refundAmount.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

  // Only show the refund breakdown for bookings that can still be
  // cancelled — it's irrelevant once a booking is Completed or already Cancelled.
  const refundRow = normalizedStatus === "pending"
    ? `<p><strong>Refund if Cancelled:</strong> ${formattedRefund} <span class="fee-note">(₱${CONVENIENCE_FEE}.00 convenience fee deducted)</span></p>`
    : "";

  document.getElementById("salesBookingInfo").innerHTML = `
    <p><strong>Transaction:</strong> ${transactionCode}</p>
    <p><strong>Customer:</strong> ${name}</p>
    <p><strong>Customer Number:</strong> ${customerNumber}</p>
    <p><strong>Movie:</strong> ${movie}</p>
    <p><strong>Amount:</strong> ${amount}</p>
    <p><strong>Seats:</strong> ${seats}</p>
    <p><strong>Tickets:</strong> ${tickets}</p>
    <p><strong>Status:</strong> ${status}</p>
    ${refundRow}
  `;

  const completeBtn = document.getElementById("salesCompleteBookingBtn");
  const cancelBtn = document.getElementById("salesCancelBookingBtn");

  // Only a Pending booking can still be marked Completed or Cancelled.
  // A booking that's already Completed or Cancelled is a closed record,
  // so both action buttons are hidden.
  if (normalizedStatus === "pending") {
    completeBtn.style.display = "block";
    cancelBtn.style.display = "block";
  } else {
    completeBtn.style.display = "none";
    cancelBtn.style.display = "none";
  }

  document.getElementById("salesModal").style.display = "flex";
}

/**
 * Marks a pending booking as Cancelled using transaction_status.php.
 * A ₱20 convenience fee is deducted from the refund total shown to the
 * admin; the original transaction amount in the database is left as-is
 * since it's the historical record of what the customer paid.
 * Cancelled bookings are kept in the table (Cancelled tab / status
 * filter) as a refund reference rather than being deleted.
 */
function cancelSalesBooking() {
  if (currentTransactionCode === "") {
    alert("No transaction selected.");
    return;
  }

  const refundAmount = Math.max(currentTransactionAmount - CONVENIENCE_FEE, 0);
  const formattedRefund = `₱${refundAmount.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

  if (!confirm(`Cancel this booking?\n\nRefund amount after ₱${CONVENIENCE_FEE}.00 convenience fee: ${formattedRefund}`)) {
    return;
  }

  fetch("Sales_Management/Transaction/transaction_status.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "transaction_code=" + encodeURIComponent(currentTransactionCode) + "&status=Cancelled"
  })
    .then(response => response.text())
    .then(data => {
      if (data.trim() === "success") {
        alert(`Booking marked as Cancelled.\nRefund amount: ${formattedRefund}`);
        closeSalesModal();
        location.reload();
      } else {
        alert(data);
      }
    })
    .catch(error => {
      console.error(error);
      alert("An error occurred while updating status.");
    });
}

/**
 * Closes the modal overlay.
 */
function closeSalesModal() {
  document.getElementById("salesModal").style.display = "none";
}

// Close modal when clicking outside content area
window.addEventListener("click", function (e) {
  const modal = document.getElementById("salesModal");
  if (modal && e.target === modal) {
    modal.style.display = "none";
  }
});

/**
 * Marks a pending booking as Completed using update_status.php
 */
function completeSalesBooking() {
  if (currentTransactionCode === "") {
    alert("No transaction selected.");
    return;
  }

  if (!confirm("Mark this booking as Completed?")) {
    return;
  }

  fetch("Sales_Management/Transaction/transaction_status.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "transaction_code=" + encodeURIComponent(currentTransactionCode) + "&status=Completed"
  })
    .then(response => response.text())
    .then(data => {
      if (data.trim() === "success") {
        alert("Booking marked as Completed!");
        closeSalesModal();
        location.reload();
      } else {
        alert(data);
      }
    })
    .catch(error => {
      console.error(error);
      alert("An error occurred while updating status.");
    });
}