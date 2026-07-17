document.addEventListener("DOMContentLoaded", () => {
    const menuLinks = document.querySelectorAll(".sidebar-content a");
    const pageSections = document.querySelectorAll(".page-section");
    const headerTitle = document.querySelector(".page-header h3");

    menuLinks.forEach(link => {
        link.addEventListener("click", (e) => {
            // Find the target page ID from data-page attribute
            const targetPage = link.getAttribute("data-page");

            if (targetPage) {
                e.preventDefault(); // Stop page reload for dynamic links

                // 1. Switch Active Class on Sidebar Links
                menuLinks.forEach(item => item.classList.remove("active"));
                link.classList.add("active");

                // 2. Dynamic Title updates in the Top Header
                if (headerTitle) {
                    headerTitle.textContent = link.textContent.trim();
                }

                // 3. Hide all page sections, show only the target section
                pageSections.forEach(section => {
                    if (section.id === `page-${targetPage}`) {
                        section.classList.remove("d-none"); // Show section
                    } else {
                        section.classList.add("d-none");    // Hide section
                    }
                });
            }
        });
    });
});