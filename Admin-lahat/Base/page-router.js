document.addEventListener("DOMContentLoaded", () => {
    const menuLinks = document.querySelectorAll(".sidebar-content a");
    const pageSections = document.querySelectorAll(".page-section");

    // Helper function to handle switching/displaying a page
    function showPage(link) {
        const targetPage = link.getAttribute("data-page");
        if (!targetPage) return;

        // 1. Switch Active Class on Sidebar Links
        menuLinks.forEach(item => item.classList.remove("active"));
        link.classList.add("active");
        
        // 3. Hide all page sections, show only the target section
        pageSections.forEach(section => {
            if (section.id === `page-${targetPage}`) {
                section.classList.remove("d-none"); // Show section
            } else {
                section.classList.add("d-none");    // Hide section
            }
        });

        // Save state so page reloads don't reset view
        localStorage.setItem("activeAdminPage", targetPage);
    }

    // Attach click events
    menuLinks.forEach(link => {
        link.addEventListener("click", (e) => {
            const targetPage = link.getAttribute("data-page");
            if (targetPage) {
                e.preventDefault(); // Stop page reload for dynamic links
                showPage(link);
            }
        });
    });

    // --- INITIAL LOAD HANDLER (Fixes the blank screen) ---
    const savedPage = localStorage.getItem("activeAdminPage");
    let initialLink = savedPage ? document.querySelector(`.sidebar-content a[data-page="${savedPage}"]`) : null;

    // Fallback to the first menu link if no saved state exists
    if (!initialLink && menuLinks.length > 0) {
        initialLink = menuLinks[0];
    }

    // Trigger display on load
    if (initialLink) {
        showPage(initialLink);
    }
});