const form = document.getElementById('signupForm');
const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const termsCheckbox = document.getElementById('terms');

const nameError = document.getElementById('nameError');
const emailError = document.getElementById('emailError');
const passwordError = document.getElementById('passwordError');
const confirmPasswordError = document.getElementById('confirmPasswordError');
const termsError = document.getElementById('termsError');
const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('confirmPassword');

function validateName(name) {
    const nameRegex = /^[a-zA-Z\s]{2,50}$/;
    if (!name.trim()) {
        return 'Name is required';
    }
    if (name.trim().length < 2) {
        return 'Name must be at least 2 characters';
    }
    if (!nameRegex.test(name.trim())) {
        return 'Name can only contain letters and spaces';
    }
    return '';
}

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
    if (password.length < 8) {
        return 'Password must be at least 8 characters';
    }
    if (!/[A-Z]/.test(password)) {
        return 'Password must contain at least one uppercase letter';
    }
    if (!/[a-z]/.test(password)) {
        return 'Password must contain at least one lowercase letter';
    }
    if (!/[0-9]/.test(password)) {
        return 'Password must contain at least one number';
    }
    return '';
}

function validateConfirmPassword(password, confirmPassword) {
    if (!confirmPassword) {
        return 'Please confirm your password';
    }
    if (password !== confirmPassword) {
        return 'Passwords do not match';
    }
    return '';
}

function validateTerms(checked) {
    if (!checked) {
        return 'You must agree to the Terms & Conditions';
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

nameInput.addEventListener('blur', function() {
    const error = validateName(nameInput.value);
    if (error) {
        showError(nameError, error);
        setFieldError(nameInput, true);
    } else {
        clearError(nameError);
        setFieldError(nameInput, false);
    }
});

nameInput.addEventListener('input', function() {
    if (nameError.textContent) {
        const error = validateName(nameInput.value);
        if (!error) {
            clearError(nameError);
            setFieldError(nameInput, false);
        }
    }
});

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
    if (confirmPasswordInput.value) {
        const confirmError = validateConfirmPassword(passwordInput.value, confirmPasswordInput.value);
        if (confirmError) {
            showError(confirmPasswordError, confirmError);
            setFieldError(confirmPasswordInput, true);
        } else {
            clearError(confirmPasswordError);
            setFieldError(confirmPasswordInput, false);
        }
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
    if (confirmPasswordInput.value) {
        const confirmError = validateConfirmPassword(passwordInput.value, confirmPasswordInput.value);
        if (confirmError) {
            showError(confirmPasswordError, confirmError);
            setFieldError(confirmPasswordInput, true);
        } else {
            clearError(confirmPasswordError);
            setFieldError(confirmPasswordInput, false);
        }
    }
});

confirmPasswordInput.addEventListener('blur', function() {
    const error = validateConfirmPassword(passwordInput.value, confirmPasswordInput.value);
    if (error) {
        showError(confirmPasswordError, error);
        setFieldError(confirmPasswordInput, true);
    } else {
        clearError(confirmPasswordError);
        setFieldError(confirmPasswordInput, false);
    }
});

confirmPasswordInput.addEventListener('input', function() {
    if (confirmPasswordError.textContent) {
        const error = validateConfirmPassword(passwordInput.value, confirmPasswordInput.value);
        if (!error) {
            clearError(confirmPasswordError);
            setFieldError(confirmPasswordInput, false);
        }
    }
});

termsCheckbox.addEventListener('change', function() {
    const error = validateTerms(termsCheckbox.checked);
    if (error) {
        showError(termsError, error);
    } else {
        clearError(termsError);
    }
});

form.addEventListener('submit', function(e) {
    let isValid = true;

    const nameErrorMsg = validateName(nameInput.value);
    if (nameErrorMsg) {
        showError(nameError, nameErrorMsg);
        setFieldError(nameInput, true);
        isValid = false;
    } else {
        clearError(nameError);
        setFieldError(nameInput, false);
    }

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

    const confirmPasswordErrorMsg = validateConfirmPassword(passwordInput.value, confirmPasswordInput.value);
    if (confirmPasswordErrorMsg) {
        showError(confirmPasswordError, confirmPasswordErrorMsg);
        setFieldError(confirmPasswordInput, true);
        isValid = false;
    } else {
        clearError(confirmPasswordError);
        setFieldError(confirmPasswordInput, false);
    }

    const termsErrorMsg = validateTerms(termsCheckbox.checked);
    if (termsErrorMsg) {
        showError(termsError, termsErrorMsg);
        isValid = false;
    } else {
        clearError(termsError);
    }

    if (!isValid) {
        e.preventDefault();
        const firstError = form.querySelector('.error-message[style*="block"]');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});

const returnUrlInput = document.getElementById('returnUrl');
const storedReturnUrl = localStorage.getItem('footcastReturnUrl');
if (storedReturnUrl && returnUrlInput) {
    returnUrlInput.value = storedReturnUrl;
    localStorage.removeItem('footcastReturnUrl');
}