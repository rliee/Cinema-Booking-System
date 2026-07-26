/**
 * ==========================================================
 * Customer App
 * ----------------------------------------------------------
 * PURPOSE:
 * Handles client-side interactions shared across pages.
 *
 * RESPONSIBILITIES:
 * - Booking button handling
 * - Session checking
 * - Customer type synchronization
 * ==========================================================
 */

document.addEventListener("DOMContentLoaded", () => {
  /*
    |--------------------------------------------------------------------------
    | Booking Buttons
    |--------------------------------------------------------------------------
    */

  document.body.addEventListener("click", async (event) => {
    const button = event.target.closest(".btn-book-ticket, .btn-book-now");

    if (!button) {
      return;
    }

    const movieId =
      button.dataset.movieId || button.getAttribute("data-movie-id");

    if (!movieId) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();

    try {
      if (await Auth.isLoggedIn()) {
        window.location.href =
          "booking.php?movie_id=" + encodeURIComponent(movieId);

        return;
      }

      /*
    |--------------------------------------------------------------------------
    | Not logged in
    |--------------------------------------------------------------------------
    */

      sessionStorage.setItem("pendingMovieId", movieId);

      const loginModalElement = document.getElementById("loginModal");

      if (loginModalElement) {
        bootstrap.Modal.getOrCreateInstance(loginModalElement).show();

        return;
      }

      alert("Please log in first.");
    } catch (error) {
      console.error(error);

      alert("Unable to verify your login session.");
    }
  });

  /*
    |--------------------------------------------------------------------------
    | Customer Type Synchronization
    |--------------------------------------------------------------------------
    */

  function initializeCustomerType(select) {
    const savedType = localStorage.getItem("customerType") || "Regular";

    select.value = savedType;

    if (select.dataset.initialized) {
      return;
    }

    select.addEventListener("change", () => {
      localStorage.setItem("customerType", select.value);

      window.dispatchEvent(
        new CustomEvent("customerTypeChanged", {
          detail: select.value,
        }),
      );
    });

    select.dataset.initialized = "true";
  }

  document
    .querySelectorAll("#customerTypeSelect")
    .forEach(initializeCustomerType);

  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (!node.querySelectorAll) {
          return;
        }

        node
          .querySelectorAll("#customerTypeSelect")
          .forEach(initializeCustomerType);
      });
    });
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });
});
