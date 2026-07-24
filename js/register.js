/**
 * ==========================================================
 * Register
 * ----------------------------------------------------------
 * PURPOSE:
 * Handle customer registration.
 *
 * RESPONSIBILITIES:
 * - Submit registration form
 * - Display validation errors
 * - Close registration modal
 * - Redirect after registration
 *
 * DOES NOT:
 * - Store login state
 * - Login users manually
 * - Logout users
 * ==========================================================
 */

document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.getElementById("register-form");

  if (!registerForm) {
    return;
  }

  registerForm.addEventListener("submit", handleRegister);
});

async function handleRegister(event) {
  event.preventDefault();

  const form = event.target;

  const formData = new FormData(form);

  try {
    const response = await fetch("api/auth/register.php", {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    });

    const result = await response.json();

    if (!result.success) {
      alert(result.message);

      return;
    }

    Auth.hideRegisterModal();

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
