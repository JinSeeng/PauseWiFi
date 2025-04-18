document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire de connexion
    const loginForm = document.querySelector('.auth-form[action="/login.php"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = this.querySelector('input[name="email"]');
            const password = this.querySelector('input[name="password"]');
            let isValid = true;
            
            clearErrors(this);
            
            if (!email.value || !isValidEmail(email.value)) {
                showError(email, 'Veuillez entrer une adresse email valide');
                isValid = false;
            }
            
            if (!password.value) {
                showError(password, 'Le mot de passe est requis');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Validation du formulaire d'inscription
    const registerForm = document.querySelector('.auth-form[action="/register.php"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const username = this.querySelector('input[name="username"]');
            const email = this.querySelector('input[name="email"]');
            const password = this.querySelector('input[name="password"]');
            const confirmPassword = this.querySelector('input[name="confirm_password"]');
            let isValid = true;
            
            clearErrors(this);
            
            if (!username.value || username.value.length < 3) {
                showError(username, 'Le nom d\'utilisateur doit contenir au moins 3 caractères');
                isValid = false;
            }
            
            if (!email.value || !isValidEmail(email.value)) {
                showError(email, 'Veuillez entrer une adresse email valide');
                isValid = false;
            }
            
            if (!password.value || password.value.length < 6) {
                showError(password, 'Le mot de passe doit contenir au moins 6 caractères');
                isValid = false;
            }
            
            if (password.value !== confirmPassword.value) {
                showError(confirmPassword, 'Les mots de passe ne correspondent pas');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});

function showError(input, message) {
    const formGroup = input.closest('.form-group');
    if (!formGroup) return;
    
    let errorElement = formGroup.querySelector('.error-message');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        formGroup.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
    input.classList.add('error');
}

function clearErrors(form) {
    const errorMessages = form.querySelectorAll('.error-message');
    errorMessages.forEach(el => el.remove());
    
    const errorInputs = form.querySelectorAll('.error');
    errorInputs.forEach(input => input.classList.remove('error'));
}

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}