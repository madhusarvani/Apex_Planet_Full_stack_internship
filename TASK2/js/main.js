// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth' });
    });
});

// Password toggle
document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', function() {
        const input = this.previousElementSibling;
        if (input.type === 'password') {
            input.type = 'text';
            this.classList.remove('bi-eye-slash');
            this.classList.add('bi-eye');
        } else {
            input.type = 'password';
            this.classList.remove('bi-eye');
            this.classList.add('bi-eye-slash');
        }
    });
});

// Register form handling
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirmPassword');

    // AJAX check for username/email
    async function checkExists(type, value) {
        if (!value) return false;
        const res = await fetch(`php/check_user.php?type=${type}&value=${encodeURIComponent(value)}`);
        const data = await res.json();
        return data.exists;
    }

    usernameInput.addEventListener('blur', async () => {
        const exists = await checkExists('username', usernameInput.value);
        const status = document.getElementById('usernameStatus');
        if (exists) {
            usernameInput.classList.add('is-invalid');
            status.innerHTML = '<i class="bi bi-x-circle"></i> Username taken';
            status.style.color = '#dc3545';
        } else {
            usernameInput.classList.remove('is-invalid');
            status.innerHTML = '<i class="bi bi-check-circle"></i> Available';
            status.style.color = '#198754';
        }
    });

    emailInput.addEventListener('blur', async () => {
        const exists = await checkExists('email', emailInput.value);
        const status = document.getElementById('emailStatus');
        if (exists) {
            emailInput.classList.add('is-invalid');
            status.innerHTML = '<i class="bi bi-x-circle"></i> Email already registered';
            status.style.color = '#dc3545';
        } else {
            emailInput.classList.remove('is-invalid');
            status.innerHTML = '<i class="bi bi-check-circle"></i> Valid';
            status.style.color = '#198754';
        }
    });

    function validatePasswordMatch() {
        const match = passwordInput.value === confirmInput.value;
        const matchError = document.getElementById('matchError');
        if (!match) {
            confirmInput.classList.add('is-invalid');
            matchError.innerText = 'Passwords do not match';
        } else {
            confirmInput.classList.remove('is-invalid');
            matchError.innerText = '';
        }
        return match;
    }

    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!validatePasswordMatch()) return;
        
        const formData = new FormData();
        formData.append('username', usernameInput.value);
        formData.append('email', emailInput.value);
        formData.append('password', passwordInput.value);

        const res = await fetch('php/register.php', { method: 'POST', body: formData });
        const result = await res.text();
        if (result === 'success') {
            alert('Registration successful! Please login.');
            window.location.href = 'login.html';
        } else if (result === 'exists') {
            alert('Username or email already taken.');
        } else {
            alert('Registration failed. Try again.');
        }
    });
}

// Login form handling
// Login form handler
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const login = document.getElementById('loginInput').value;
        const password = document.getElementById('loginPassword').value;
        
        const formData = new FormData();
        formData.append('login', login);
        formData.append('password', password);
        
        const response = await fetch('php/login.php', { method: 'POST', body: formData });
        const result = await response.text();
        
        if (result === 'success') {
            window.location.href = 'dashboard.html';
        } else if (result === 'invalid') {
            alert('Wrong password');
        } else if (result === 'not_found') {
            alert('User not found');
        } else {
            alert('Login error. Try again.');
        }
    });
}