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

  const firstNameInput = document.getElementById("register-firstname");
  const lastNameInput = document.getElementById("register-lastname");

  [firstNameInput, lastNameInput].forEach((input) => {
    input.addEventListener("input", () => {
      input.value = formatName(input.value);
    });
  });

  // Reset form whenever the modal is closed
  const registerModal = document.getElementById("registerModal");

  registerModal.addEventListener("hidden.bs.modal", () => {
    registerForm.reset();

    const errorBox = document.getElementById("register-error");
    errorBox.textContent = "";
    errorBox.style.display = "none";
  });
});

function formatName(value) {
  return value
    .toLowerCase()
    .replace(/\s+/g, " ")
    .trimStart()
    .replace(/\b\w/g, (letter) => letter.toUpperCase());
}

async function handleRegister(event) {
  event.preventDefault();

  const form = event.target;

  const errorBox = document.getElementById("register-error");

  errorBox.textContent = "";
  errorBox.style.display = "none";

  const formData = new FormData(form);

  try {
    const response = await fetch("api/auth/register.php", {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    });

    const result = await response.json();

    if (!result.success) {
      errorBox.textContent = result.message;
      errorBox.style.display = "block";

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
