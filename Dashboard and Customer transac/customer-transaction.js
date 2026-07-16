// Reference search and filter controls
const search=document.getElementById("search");
const dateFilter=document.getElementById("dateFilter");
const statusFilter=document.getElementById("statusFilter");

// Tab buttons and panel references
const tabs=document.querySelectorAll(".tab-btn");
const tableControls=document.getElementById("tableControls");
const weeklyPanel=document.getElementById("weeklyPanel");
const weeklyTotal=document.getElementById("weeklyTotal");
const weeklyDays=document.getElementById("weeklyDays");

// Current active tab state
let activeTab="completed";
const dayNames=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
const chartDays=["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];

/**
 * Compute the weekly revenue totals from the transaction table.
 * Groups the current rows by weekday and updates the weekly summary panel.
 */
function computeWeeklyRevenue(){
  const rows=document.querySelectorAll("#transactionTable tbody tr");
  const totals={
    Monday:0,
    Tuesday:0,
    Wednesday:0,
    Thursday:0,
    Friday:0,
    Saturday:0,
    Sunday:0
  };
  let weeklySum=0;

  rows.forEach(row=>{
    const amount=Number(row.cells[3].innerText.replace(/[^0-9]/g, ""));
    const dateText=row.cells[4].innerText;
    const date=new Date(dateText);
    const weekday=dayNames[date.getDay()];
    totals[weekday]+=amount;
    weeklySum += amount;
  });

  const maximumValue=20000;
  weeklyTotal.textContent=`₱${weeklySum.toLocaleString()}`;
  weeklyDays.innerHTML=chartDays.map(day=>{
    const barWidth=totals[day]===0 ? 0 : Math.min(100, Math.max(3, Math.round((totals[day]/maximumValue)*100)));
    return `<div class="bar-group">
      <span class="bar-label">${day}</span>
      <div class="bar-track">
        <div class="bar-fill" style="width:${barWidth}%"></div>
      </div>
      <span class="bar-value">₱${totals[day].toLocaleString()}</span>
    </div>`;
  }).join("");
}

/**
 * Switches between the transaction table view and the weekly revenue view.
 * Shows table controls when browsing table data, and displays weekly totals when the weekly tab is active.
 */
function updateView(){
  const showTable=activeTab!=="weekly";
  const hideStatusFilter=activeTab==="completed"||activeTab==="pending";
  tableControls.style.display=showTable?"flex":"none";
  statusFilter.style.display=hideStatusFilter?"none":"inline-block";
  document.getElementById("transactionTable").style.display=showTable?"table":"none";
  weeklyPanel.style.display=showTable?"none":"block";

  if(activeTab==="weekly"){
    computeWeeklyRevenue();
  } else {
    filterRows();
  }
}

/**
 * Filters transaction rows based on search text, date, status, and active tab.
 * Hides rows that do not match the current filter criteria.
 */
function filterRows(){
  if(activeTab==="weekly"){
    updateView();
    return;
  }

  const filter=search.value.toLowerCase();
  const selectedDate=dateFilter.value;
  const selectedStatus=statusFilter.value.toLowerCase();
  const rows=document.querySelectorAll("#transactionTable tbody tr");

  rows.forEach(row=>{
    const text=row.innerText.toLowerCase();
    const date=row.cells[4].innerText;
    const status=row.cells[5].innerText.trim().toLowerCase();
    const matchesSearch=text.includes(filter);
    const matchesDate=selectedDate===""||date===selectedDate;
    const matchesStatus=selectedStatus===""||status===selectedStatus;
    const matchesTab=activeTab==="all"||status===activeTab;

    row.style.display=(matchesSearch && matchesDate && matchesStatus && matchesTab)?"":"none";
  });
}

// Wire search and filter inputs to the row filter function.
search.addEventListener("keyup", filterRows);
dateFilter.addEventListener("change", filterRows);
statusFilter.addEventListener("change", filterRows);

// Attach tab click behavior to update the displayed panel.
tabs.forEach(tab=>{
  tab.addEventListener("click", ()=>{
    activeTab=tab.dataset.tab;
    tabs.forEach(button=>button.classList.toggle("active", button===tab));
    updateView();
  });
});

// Initialize the page with the default active tab.
updateView();

/**
 * Opens the booking detail modal and injects transaction details.
 */
function details(id,name,movie,amount,seats,tickets,status){
  document.getElementById("bookingInfo").innerHTML=`
    <p><strong>Transaction:</strong> ${id}</p>
    <p><strong>Customer:</strong> ${name}</p>
    <p><strong>Movie:</strong> ${movie}</p>
    <p><strong>Amount:</strong> ${amount}</p>
    <p><strong>Seats:</strong> ${seats}</p>
    <p><strong>Tickets:</strong> ${tickets}</p>
    <p><strong>Status:</strong> ${status}</p>
  `;
  document.getElementById("modal").style.display="flex";
}

/**
 * Closes the modal overlay and hides booking details.
 */
function closeModal(){
  document.getElementById("modal").style.display="none";
}

// Close the modal when clicking outside of its content.
window.onclick=function(e){
  const modal=document.getElementById("modal");
  if(e.target==modal){
    modal.style.display="none";
  }
}
