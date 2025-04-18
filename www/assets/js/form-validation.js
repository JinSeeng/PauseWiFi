document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire de connexion
    const loginForm = document.querySelector('.auth-form[action="/login.php"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = this.querySelector('input[name="email"]');
            const password = this.querySelector('input[name="password"]');
            let isValid = true;
            
            clearErrors(this); // Nettoie les erreurs précédentes
            
            // Validation de l'email
            if (!email.value || !isValidEmail(email.value)) {
                showError(email, 'Veuillez entrer une adresse email valide');
                isValid = false;
            }
            
            // Validation du mot de passe
            if (!password.value) {
                showError(password, 'Le mot de passe est requis');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault(); // Empêche l'envoi si invalide
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
            
            // Validation du nom d'utilisateur
            if (!username.value || username.value.length < 3) {
                showError(username, 'Le nom d\'utilisateur doit contenir au moins 3 caractères');
                isValid = false;
            }
            
            // Validation de l'email
            if (!email.value || !isValidEmail(email.value)) {
                showError(email, 'Veuillez entrer une adresse email valide');
                isValid = false;
            }
            
            // Validation du mot de passe
            if (!password.value || password.value.length < 6) {
                showError(password, 'Le mot de passe doit contenir au moins 6 caractères');
                isValid = false;
            }
            
            // Vérification de la correspondance des mots de passe
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

// Affiche un message d'erreur pour un champ
function showError(input, message) {
    const formGroup = input.closest('.form-group');
    if (!formGroup) return;
    
    // Crée ou réutilise l'élément d'erreur
    let errorElement = formGroup.querySelector('.error-message');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        formGroup.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
    input.classList.add('error');
}

// Nettoie tous les messages d'erreur
function clearErrors(form) {
    const errorMessages = form.querySelectorAll('.error-message');
    errorMessages.forEach(el => el.remove());
    
    const errorInputs = form.querySelectorAll('.error');
    errorInputs.forEach(input => input.classList.remove('error'));
}

// Vérifie si un email est valide
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}