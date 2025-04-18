<?php 
require_once __DIR__ . '/partials/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    $errors = [];

    // Validation
    if (empty($name)) {
        $errors[] = "Le nom est requis";
    }

    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }

    if (empty($message)) {
        $errors[] = "Le message est requis";
    }

    if (empty($errors)) {
        // Envoyer l'email (à configurer selon votre environnement)
        $to = 'admin@pause-wifi.fr';
        $subject = 'Nouveau message de contact - Pause WiFi';
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        $emailBody = "Nom: $name\n";
        $emailBody .= "Email: $email\n\n";
        $emailBody .= "Message:\n$message";

        if (mail($to, $subject, $emailBody, $headers)) {
            $_SESSION['contact_success'] = "Votre message a bien été envoyé !";
            header('Location: /?page=contact');
            exit;
        } else {
            $errors[] = "Une erreur est survenue lors de l'envoi du message";
        }
    }
}
?>

<div class="contact">
    <h1 class="contact__title">Contactez-nous</h1>
    
    <?php if (isset($_SESSION['contact_success'])): ?>
        <div class="contact__alert contact__alert--success">
            <?= htmlspecialchars($_SESSION['contact_success']) ?>
            <?php unset($_SESSION['contact_success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="contact__alert contact__alert--error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form action="/?page=contact" method="POST" class="contact__form">
        <div class="contact__form-group">
            <label for="name" class="contact__label">Votre nom</label>
            <input type="text" id="name" name="name" class="contact__input" required 
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>
        
        <div class="contact__form-group">
            <label for="email" class="contact__label">Votre email</label>
            <input type="email" id="email" name="email" class="contact__input" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        
        <div class="contact__form-group">
            <label for="message" class="contact__label">Votre message</label>
            <textarea id="message" name="message" rows="6" class="contact__textarea" required><?= 
                htmlspecialchars($_POST['message'] ?? '') 
            ?></textarea>
        </div>
        
        <button type="submit" class="contact__submit">Envoyer le message</button>
    </form>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>