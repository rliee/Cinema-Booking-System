let confirmCallback = null;

document.addEventListener("DOMContentLoaded", () => {
  const confirmButton = document.getElementById(
    "confirmationModalConfirmButton",
  );

  if (confirmButton) {
    confirmButton.addEventListener("click", () => {
      bootstrap.Modal.getInstance(
        document.getElementById("confirmationModal"),
      ).hide();

      if (confirmCallback) {
        confirmCallback();
        confirmCallback = null;
      }
    });
  }
});

function showConfirmationModal(title, message, callback) {
  const titleElement = document.getElementById("confirmationModalTitle");

  titleElement.querySelector("span").textContent = title;

  document.getElementById("confirmationModalMessage").textContent = message;

  const icon = document.getElementById("confirmationModalIcon");

  icon.className = "fa-solid fa-trash";

  confirmCallback = callback;

  new bootstrap.Modal(document.getElementById("confirmationModal")).show();
}

function showMessageModal(title, message) {
  document
    .getElementById("messageModalTitle")
    .querySelector("span").textContent = title;

  document.getElementById("messageModalBody").textContent = message;

  new bootstrap.Modal(document.getElementById("messageModal")).show();
}
