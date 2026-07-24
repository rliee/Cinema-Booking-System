/**
 * ==========================================================
 * Login
 * ----------------------------------------------------------
 * PURPOSE:
 * Handle customer login.
 *
 * RESPONSIBILITIES:
 * - Submit login form
 * - Validate server response
 * - Close login modal
 * - Redirect to pending booking
 *
 * DOES NOT:
 * - Store login state
 * - Register users
 * - Logout users
 * ==========================================================
 */

document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("login-form");

  if (!loginForm) {
    return;
  }

  loginForm.addEventListener("submit", handleLogin);
});

async function handleLogin(event) {
  event.preventDefault();

  const form = event.target;

  const formData = new FormData(form);

  try {
    const response = await fetch("api/auth/login.php", {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    });

    const result = await response.json();

    if (!result.success) {
      alert(result.message);

      return;
    }

    Auth.hideLoginModal();

    form.reset();

    const pendingMovie = sessionStorage.getItem("pendingMovie");

    if (pendingMovie) {
      sessionStorage.removeItem("pendingMovie");

      window.location.href =
        "booking.php?movie=" + encodeURIComponent(pendingMovie);

      return;
    }

    window.location.href = "index.php";
  } catch (error) {
    console.error(error);

    alert("Unable to connect to the server.");
  }
}
