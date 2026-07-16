// AJAX HELPER
// Handles all communication with the PHP backend.

async function request(url, options = {}) {
    try {
        const response = await fetch(url, options);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || "Server request failed.");
        }
        return data;
    } catch (error) {
        console.error(error);
        return {
            success: false,
            message: error.message || "An unexpected error occurred."
        };
    }
}



// const weekContainer = document.getElementById("weekContainer");
// const today = new Date();

// function loadSchedules() {
//     fetch("ajax/getSchedules.php")
//         .then(response => response.json())
//         .then(data => {
//             let html = "";
//             let tickets = 0;
//             data.forEach(schedule => {
//                 tickets += parseInt(schedule.sold);
//                 html += `
//                     <div class="schedule-card">
//                         <div class="schedule-left">
//                             <div class="show-time">${schedule.start_time}</div>
//                         </div>
//                         <div>
//                             <div class="movie-title">${schedule.title}</div>
//                         </div>    
//                         <div>
//                             ${schedule.hall_name}
//                         </div>
//                         <div>
//                             ₱${schedule.ticket_price}
//                         </div>
//                     </div>
//                     <div>
//                         <div class="progress">
//                             <div class="progress-bar bg-warning" style="width:${schedule.percent}%"></div>
//                         </div>
//                         <div>
//                             ${schedule.sold}/${schedule.total_seats}
//                         </div>
//                         <span class="status ${schedule.class}">${schedule.status}</span>
//                         <button class="btn btn-sm btn-outline-light editBtn" data-id="${schedule.schedule_id}">
//                             <i class="bi bi-pencil"></i>
//                         </button>
//                         <button class="btn btn-sm btn-outline-danger deleteBtn" data-id="${schedule.schedule_id}">
//                             <i class="bi bi-trash"></i>
//                         </button>
//                     </div>
//                 `;
//             });
//             document.getElementById("scheduleList").innerHTML = html;
//             document.getElementById("showCount").innerHTML = data.length;
//             document.getElementById("ticketsSold").innerHTML = tickets;
//         }
//         );

// }

// function generateWeek() {
//     weekContainer.innerHTML = "";
//     const start = new Date(today);
//     start.setDate(today.getDate() - today.getDay() + 1);

//     for (let i = 0; i < 7; i++) {
//         let d = new Date(start);
//         d.setDate(start.getDate() + i);
//         let card = document.createElement("div");
//         card.className = "col";
//         card.innerHTML = `
//             <div class="day-card ${i == 2 ? 'active-day' : ''}">
//                 <div>
//                     ${d.toLocaleDateString('en-US', { weekday: 'short' })}
//                 </div>
//                 <h3>${d.getDate()}</h3>
//             </div>
//         `;
//         weekContainer.appendChild(card);
//     }
// }

// generateWeek();
// loadSchedules();

// const movie = document.getElementById("movie");
// const start = document.getElementById("startTime");
// const end = document.getElementById("endTime");
// function calculateEndTime() {
//     if (movie.value == "" || start.value == "")
//         return;
//     let duration = parseInt(
//         movie.options[
//             movie.selectedIndex
//         ].dataset.duration
//     );
//     let t = start.value.split(":");
//     let date = new Date();
//     date.setHours(t[0]);
//     date.setMinutes(t[1]);
//     date.setMinutes(date.getMinutes() + duration);
//     let hh = String(date.getHours()).padStart(2, "0");
//     let mm = String(date.getMinutes()).padStart(2, "0");
//     end.value = hh + ":" + mm;
// }

// movie.onchange = calculateEndTime;
// start.onchange = calculateEndTime;
// document.getElementById("addScheduleForm").addEventListener("submit", function (e) {
//     e.preventDefault();
//     fetch("ajax/insertSchedule.php", {method: "POST", body: new FormData(this)})
//     .then(res => res.json())
//     .then(response => {
//         if (response.success) {
//             bootstrap.Modal.getInstance(document.getElementById("addScheduleModal")).hide();
//             this.reset();
//             loadSchedules();
//             alert("Schedule Created");
//         } else {
//             alert(response.message);
//         }
//     });
// });