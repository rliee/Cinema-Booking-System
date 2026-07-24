/**
 * ==========================================================
 * Authentication Utilities
 * ----------------------------------------------------------
 * PURPOSE:
 * Centralized authentication helper functions.
 *
 * RESPONSIBILITIES:
 * - Get current session
 * - Check login status
 * - Logout
 * - Show/Hide authentication modals
 * ==========================================================
 */

const Auth = (() => {
  let cachedUser = null;

  async function getSession() {
    try {
      const response = await fetch("api/auth/session.php", {
        credentials: "same-origin",
      });

      const result = await response.json();

      cachedUser = result.user ?? null;

      return result;
    } catch (error) {
      console.error(error);

      return {
        success: false,
        user: null,
      };
    }
  }

  async function isLoggedIn() {
    const session = await getSession();

    return session.success;
  }

  async function currentUser() {
    if (cachedUser) {
      return cachedUser;
    }

    const session = await getSession();
    return session.user;
  }

  function clearUserCache() {
    cachedUser = null;
  }

  function showLoginModal() {
    const modal = bootstrap.Modal.getOrCreateInstance(
      document.getElementById("loginModal"),
    );

    modal.show();
  }

  function hideLoginModal() {
    const modal = bootstrap.Modal.getOrCreateInstance(
      document.getElementById("loginModal"),
    );

    modal.hide();
  }

  function showRegisterModal() {
    const modal = bootstrap.Modal.getOrCreateInstance(
      document.getElementById("registerModal"),
    );

    modal.show();
  }

  function hideRegisterModal() {
    const modal = bootstrap.Modal.getOrCreateInstance(
      document.getElementById("registerModal"),
    );

    modal.hide();
  }

  return {
    getSession,
    isLoggedIn,
    currentUser,
    clearUserCache,

    showLoginModal,
    hideLoginModal,

    showRegisterModal,
    hideRegisterModal,
  };
})();
