document.querySelector('#togglePassword').addEventListener('click', function() {
    const passwordField = document.querySelector('#password');
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);

    // Toggle between eye and eye-slash
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
});
