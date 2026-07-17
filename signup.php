<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Account</title>
  <style>
    :root {
      --panel: rgba(16, 16, 16, 0.95);
      --gold: #d4af37;
      --text: #f7f3e8;
      --muted: #b5b0a1;
      --border: rgba(212, 175, 55, 0.3);
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      background:
        radial-gradient(circle at top left, rgba(212, 175, 55, 0.25), transparent 25%),
        linear-gradient(135deg, #000, #111 70%, #050505);
      color: var(--text);
    }

    .modal-overlay {
      position: fixed;
      inset: 0;
      display: grid;
      place-items: center;
      padding: 24px;
      background: rgba(0, 0, 0, 0.78);
      backdrop-filter: blur(6px);
      z-index: 10;
      overflow-y: auto;
    }

    .auth-card {
      width: min(100%, 900px);
      display: flex;
      position: relative;
      border: 1px solid var(--border);
      border-radius: 24px;
      background: var(--panel);
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.45);
      backdrop-filter: blur(8px);
      overflow: hidden;
      min-height: 560px;
      max-width: 100%;
    }

    .close-btn {
      position: absolute;
      top: 12px;
      right: 12px;
      width: 36px;
      height: 36px;
      border: 0;
      border-radius: 50%;
      background: rgba(255,255,255,0.08);
      color: var(--text);
      font-size: 1.1rem;
      cursor: pointer;
      z-index: 2;
    }

    .brand-side {
      flex: 1 1 45%;
      background: linear-gradient(rgba(0, 0, 0, 0.72), rgba(0, 0, 0, 0.72)),
        linear-gradient(135deg, #1a1a1a, #000);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px;
      text-align: center;
      border-right: 1px solid var(--border);
    }

    .logo-box {
      width: 170px;
      height: 170px;
      display: grid;
      place-items: center;
      margin-bottom: 24px;
      background: rgba(255, 255, 255, 0.03);
      color: var(--gold);
      font-weight: 700;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      font-size: 0.95rem;
    }

    .brand-side h2 {
      margin: 0 0 10px;
      color: var(--gold);
      font-size: 1.8rem;
    }

    .brand-side p {
      margin: 0;
      color: var(--muted);
      font-size: 1rem;
      line-height: 1.7;
      max-width: 280px;
    }

    .form-side {
      flex: 1 1 55%;
      padding: 28px 40px 40px;
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }

    .form-panel {
      width: 100%;
      max-width: 380px;
      margin: 12px auto 0;
    }

    @media (max-width: 800px) {
      body {
        padding: 12px;
      }

      .modal-overlay {
        padding: 12px;
        align-items: start;
      }

      .auth-card {
        flex-direction: column;
        min-height: auto;
      }

      .brand-side {
        border-right: 0;
        border-bottom: 1px solid var(--border);
        padding: 24px 20px;
      }

      .logo-box {
        width: 120px;
        height: 120px;
        
      }

      .brand-side h2 {
        font-size: 1.5rem;
      }

      .form-side {
        padding: 24px 20px 28px;
      }
    }

    @media (max-width: 480px) {
      .modal-overlay {
        padding: 8px;
      }

      .brand-side {
        padding: 20px 16px;
      }

      .form-side {
        padding: 20px 16px 24px;
      }

      h1 {
        font-size: 1.5rem;
      }

      .form-group input,
      .btn {
        padding: 12px 13px;
      }
    }

    h1 {
      margin: 0 0 6px;
      font-size: 1.8rem;
      transform: translateY(-4px);
      padding:3%;
    }

    .subtitle {
      margin: 0 0 24px;
      color: var(--muted);
    }

    .form-group {
      margin-bottom: 16px;
    }

    .form-group label {
      display:block;
      font-size: 0.9rem;
      margin-bottom: 8px;
      color: var(--muted);
    }

    .form-group input {
      width: 100%;
      padding: 13px 14px;
      border-radius: 12px;
      border: 1px solid var(--border);
      background: rgba(255,255,255,0.04);
      color: var(--text);
      outline: none;
    }

    .form-group input:focus {
      border-color: var(--gold);
      box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.18);
    }

    .btn {
      width: 100%;
      padding: 13px 16px;
      border: 0;
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      color: #0b0b0b;
      background: linear-gradient(135deg, var(--gold), #f2d570);
    }

    .alt-text {
      text-align: center;
      color: var(--muted);
      margin-top: 16px;
      font-size: 0.94rem;
    }

    .alt-text a {
      color: #ffd86b;
      text-decoration: none;
    }


  </style>
</head>
<body>
  <div class="modal-overlay">
    <div class="auth-card">
      <button class="close-btn" type="button" aria-label="Close">×</button>
      <div class="brand-side">
        <img src="Black logo.png" alt="Logo" class="logo-box" />
        <h2>CINEMA ROYALE</h2>
      </div>
      <div class="form-side">
        <div class="form-panel">
          <h1>Create Account</h1>

          <form id="signup-form" action="signup.php" method="POST">
            <div class="form-group">
    <label for="signup-name">Full Name</label>
    <input
        id="signup-name"
        name="fullname"
        type="text"
        placeholder="Enter your full name"
        required>
</div>

<div class="form-group">
    <label for="signup-email">Email</label>
    <input
        id="signup-email"
        name="email"
        type="email"
        placeholder="Enter your email"
        required>
</div>

<div class="form-group">
    <label for="signup-password">Password</label>
    <input
        id="signup-password"
        name="password"
        type="password"
        placeholder="Create a password"
        required>
</div>
            <button class="btn" type="submit">Create Account</button>
          </form>

          <p class="alt-text">Already have an account? <a href="login.php">Sign in</a></p>
        </div>
      </div>
    </div>
  </div>
  <script>
    const modal = document.querySelector('.modal-overlay');
    const closeBtn = document.querySelector('.close-btn');
    const card = document.querySelector('.auth-card');
    const signupForm = document.getElementById('signup-form');

    function closeModal() {
      modal.style.display = 'none';
    }

    if (closeBtn) {
      closeBtn.addEventListener('click', closeModal);
    }

    if (modal) {
      modal.addEventListener('click', (event) => {
        if (event.target === modal) {
          closeModal();
        }
      });
    }

    if (card) {
      card.addEventListener('click', (event) => {
        event.stopPropagation();
      });
    }

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        closeModal();
      }
    });

    
  </script>
</body>
</html>