const passwordToggle = document.getElementById('passwordToggle');
const passwordInput = document.getElementById('password');
const eyeIcon = document.getElementById('eyeIcon');

passwordToggle.addEventListener('click', function() {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    
    if (type === 'text') {
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
});

const form = document.getElementById('loginForm');
const emailInput = document.getElementById('email');
const emailError = document.getElementById('emailError');
const passwordError = document.getElementById('passwordError');

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.trim()) {
        return 'Email is required';
    }
    if (!emailRegex.test(email)) {
        return 'Please enter a valid email address';
    }
    return '';
}

function validatePassword(password) {
    if (!password) {
        return 'Password is required';
    }
    if (password.length < 6) {
        return 'Password must be at least 6 characters';
    }
    return '';
}

function showError(errorElement, message) {
    errorElement.textContent = message;
    errorElement.style.display = 'block';
}

function clearError(errorElement) {
    errorElement.textContent = '';
    errorElement.style.display = 'none';
}

function setFieldError(input, hasError) {
    if (hasError) {
        input.style.borderColor = '#ef4444';
        input.style.backgroundColor = '#fef2f2';
    } else {
        input.style.borderColor = '#e5e7eb';
        input.style.backgroundColor = '#f3f4f6';
    }
}

emailInput.addEventListener('blur', function() {
    const error = validateEmail(emailInput.value);
    if (error) {
        showError(emailError, error);
        setFieldError(emailInput, true);
    } else {
        clearError(emailError);
        setFieldError(emailInput, false);
    }
});

emailInput.addEventListener('input', function() {
    if (emailError.textContent) {
        const error = validateEmail(emailInput.value);
        if (!error) {
            clearError(emailError);
            setFieldError(emailInput, false);
        }
    }
});

passwordInput.addEventListener('blur', function() {
    const error = validatePassword(passwordInput.value);
    if (error) {
        showError(passwordError, error);
        setFieldError(passwordInput, true);
    } else {
        clearError(passwordError);
        setFieldError(passwordInput, false);
    }
});

passwordInput.addEventListener('input', function() {
    if (passwordError.textContent) {
        const error = validatePassword(passwordInput.value);
        if (!error) {
            clearError(passwordError);
            setFieldError(passwordInput, false);
        }
    }
});

form.addEventListener('submit', function(e) {
    
    let isValid = true;

    const emailErrorMsg = validateEmail(emailInput.value);
    if (emailErrorMsg) {
        showError(emailError, emailErrorMsg);
        setFieldError(emailInput, true);
        isValid = false;
    } else {
        clearError(emailError);
        setFieldError(emailInput, false);
    }

    const passwordErrorMsg = validatePassword(passwordInput.value);
    if (passwordErrorMsg) {
        showError(passwordError, passwordErrorMsg);
        setFieldError(passwordInput, true);
        isValid = false;
    } else {
        clearError(passwordError);
        setFieldError(passwordInput, false);
    }

    if (!isValid) {
        e.preventDefault();
        const firstError = form.querySelector('.error-message[style*="block"]');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
    }
});

const returnUrlInput = document.getElementById('returnUrl');
const storedReturnUrl = localStorage.getItem('footcastReturnUrl');
if (storedReturnUrl && returnUrlInput) {
    returnUrlInput.value = storedReturnUrl;
    localStorage.removeItem('footcastReturnUrl');
}