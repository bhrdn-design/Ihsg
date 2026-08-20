/**
 * IHSG Screener - Main JavaScript
 * Handle semua interaksi front-end
 */

// Configuration
const API_BASE = 'http://localhost/ihsg-screener/'; // Sesuaikan dengan path Anda
const API_LOGIN = API_BASE + 'api_login.php';
const API_REGISTER = API_BASE + 'api_register.php';
const API_LOGOUT = API_BASE + 'api_logout.php';
const API_AUTH_CHECK = API_BASE + 'api_auth_check.php';

// DOM Elements
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const forgotForm = document.getElementById('forgotForm');
const switchFormBtns = document.querySelectorAll('.switch-form');
const togglePasswordBtns = document.querySelectorAll('.toggle-password');
const loadingSpinner = document.getElementById('loading-spinner');
const successModal = document.getElementById('success-modal');
const successMessage = document.getElementById('success-message');
const successClose = document.getElementById('success-close');

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    initializeEventListeners();
    checkAuthStatus();
});

/**
 * Initialize Event Listeners
 */
function initializeEventListeners() {
    // Form submissions
    loginForm.addEventListener('submit', handleLoginSubmit);
    registerForm.addEventListener('submit', handleRegisterSubmit);
    forgotForm.addEventListener('submit', handleForgotSubmit);

    // Form switching
    switchFormBtns.forEach(btn => {
        btn.addEventListener('click', switchForm);
    });

    // Password toggle
    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', togglePasswordVisibility);
    });

    // Modal close
    successClose.addEventListener('click', closeSuccessModal);

    // Hamburger menu
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    if (hamburger) {
        hamburger.addEventListener('click', () => {
            navMenu.style.display = navMenu.style.display === 'flex' ? 'none' : 'flex';
        });
    }

    // Close mobile menu on link click
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            navMenu.style.display = 'none';
        });
    });
}

/**
 * Handle Login Form Submission
 */
async function handleLoginSubmit(e) {
    e.preventDefault();
    
    // Clear previous errors
    clearErrors('login');

    // Get form data
    const email = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;
    const rememberMe = document.getElementById('remember-me').checked;

    // Validate inputs
    if (!email || !password) {
        showError('login-email', 'Email dan password harus diisi');
        return;
    }

    if (!isValidEmail(email)) {
        showError('login-email', 'Format email tidak valid');
        return;
    }

    try {
        showLoading(true);

        const response = await fetch(API_LOGIN, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email: email,
                password: password,
                remember_me: rememberMe
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Login gagal');
        }

        // Save user data to localStorage
        localStorage.setItem('user', JSON.stringify({
            user_id: data.data.user_id,
            username: data.data.username,
            email: data.data.email,
            full_name: data.data.full_name,
            profile_picture: data.data.profile_picture
        }));

        // Save token if remember me
        if (rememberMe) {
            localStorage.setItem('remember_me', 'true');
        }

        showSuccessMessage('Login berhasil! Mengarahkan ke dashboard...');
        
        setTimeout(() => {
            window.location.href = API_BASE + 'dashboard.html';
        }, 2000);

    } catch (error) {
        console.error('Login error:', error);
        showError('login-error', error.message || 'Terjadi kesalahan saat login');
    } finally {
        showLoading(false);
    }
}

/**
 * Handle Register Form Submission
 */
async function handleRegisterSubmit(e) {
    e.preventDefault();

    // Clear previous errors
    clearErrors('register');

    // Get form data
    const username = document.getElementById('register-username').value.trim();
    const email = document.getElementById('register-email').value.trim();
    const password = document.getElementById('register-password').value;
    const confirmPassword = document.getElementById('register-confirm').value;
    const agreeTerms = document.querySelector('input[name="terms"]').checked;

    // Validation
    const errors = [];

    if (!username || username.length < 3) {
        errors.push('register-username');
    }

    if (!email || !isValidEmail(email)) {
        errors.push('register-email');
    }

    if (!password || password.length < 8) {
        errors.push('register-password');
    }

    if (password !== confirmPassword) {
        errors.push('register-confirm');
    }

    if (!agreeTerms) {
        showError('register-error', 'Anda harus menyetujui Syarat & Ketentuan');
        return;
    }

    if (errors.length > 0) {
        errors.forEach(field => {
            const input = document.getElementById(field);
            if (input) {
                showError(field, 'Data tidak valid');
            }
        });
        return;
    }

    try {
        showLoading(true);

        const response = await fetch(API_REGISTER, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                username: username,
                email: email,
                password: password,
                confirm_password: confirmPassword
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Pendaftaran gagal');
        }

        showSuccessMessage('Pendaftaran berhasil! Silakan login untuk melanjutkan');
        
        setTimeout(() => {
            switchForm({ target: { dataset: { form: 'login' } } });
            registerForm.reset();
        }, 2000);

    } catch (error) {
        console.error('Register error:', error);
        showError('register-error', error.message || 'Terjadi kesalahan saat mendaftar');
    } finally {
        showLoading(false);
    }
}

/**
 * Handle Forgot Password Form Submission
 */
async function handleForgotSubmit(e) {
    e.preventDefault();

    clearErrors('forgot');

    const email = document.getElementById('forgot-email').value.trim();

    if (!email || !isValidEmail(email)) {
        showError('forgot-email', 'Email tidak valid');
        return;
    }

    try {
        showLoading(true);

        // TODO: Implementasi API forgot password
        // const response = await fetch(API_BASE + 'api_forgot_password.php', {...});

        showSuccessMessage('Link reset password telah dikirim ke email Anda');
        
        setTimeout(() => {
            switchForm({ target: { dataset: { form: 'login' } } });
            forgotForm.reset();
        }, 2000);

    } catch (error) {
        showError('forgot-error', error.message);
    } finally {
        showLoading(false);
    }
}

/**
 * Switch Between Forms
 */
function switchForm(e) {
    const formName = e.target.dataset.form;
    const allForms = document.querySelectorAll('.form-container');
    
    allForms.forEach(form => {
        form.classList.remove('active');
    });

    const targetForm = document.getElementById(formName + '-form');
    if (targetForm) {
        targetForm.classList.add('active');
    }
}

/**
 * Toggle Password Visibility
 */
function togglePasswordVisibility(e) {
    e.preventDefault();
    
    const targetId = e.target.dataset.target;
    const input = document.getElementById(targetId);
    
    if (input.type === 'password') {
        input.type = 'text';
        e.target.textContent = '🙈';
    } else {
        input.type = 'password';
        e.target.textContent = '👁️';
    }
}

/**
 * Show Loading Spinner
 */
function showLoading(show = true) {
    if (show) {
        loadingSpinner.classList.add('show');
    } else {
        loadingSpinner.classList.remove('show');
    }
}

/**
 * Show Error Message
 */
function showError(fieldId, message) {
    const errorElement = document.getElementById(fieldId + '-error');
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.add('show');
        
        // Auto remove error after 5 seconds
        setTimeout(() => {
            errorElement.classList.remove('show');
        }, 5000);
    }
}

/**
 * Clear All Errors
 */
function clearErrors(formType) {
    const errorElements = document.querySelectorAll(`#${formType}-form .error-message`);
    errorElements.forEach(el => {
        el.classList.remove('show');
        el.textContent = '';
    });
}

/**
 * Show Success Message Modal
 */
function showSuccessMessage(message) {
    successMessage.textContent = message;
    successModal.classList.add('show');
}

/**
 * Close Success Modal
 */
function closeSuccessModal() {
    successModal.classList.remove('show');
}

/**
 * Validate Email Format
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Check Auth Status
 */
async function checkAuthStatus() {
    try {
        const response = await fetch(API_AUTH_CHECK);
        const data = await response.json();

        if (response.ok && data.status === 'success') {
            // User already logged in, redirect to dashboard
            window.location.href = API_BASE + 'dashboard.html';
        }
    } catch (error) {
        // User not logged in, stay on login page
        console.log('User not authenticated');
    }
}

/**
 * Logout Function
 */
async function logout() {
    try {
        showLoading(true);

        const response = await fetch(API_LOGOUT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        const data = await response.json();

        if (response.ok) {
            localStorage.removeItem('user');
            localStorage.removeItem('remember_me');
            window.location.href = API_BASE + 'index.html';
        }
    } catch (error) {
        console.error('Logout error:', error);
        alert('Gagal logout');
    } finally {
        showLoading(false);
    }
}

/**
 * Get User Data from LocalStorage
 */
function getUserData() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

/**
 * Format Date
 */
function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Export functions for use in other files
window.logout = logout;
window.getUserData = getUserData;
window.formatDate = formatDate;
