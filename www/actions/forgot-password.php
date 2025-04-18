<?php
// Inclusion des fichiers nécessaires
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ActivityLog.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Pour PHPMailer

// Démarrage de la session
session_start();

// Initialisation des objets
$db = Database::getInstance();
$userModel = new User($db);
$activityLog = new ActivityLog($db);

$errors = [];
$success = false;

// Vérification si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Phase 1 : Demande de réinitialisation (pas de token dans l'URL)
    if (!isset($_GET['token'])) {
        $email = trim($_POST['email']);
        
        // Validation de l'email
        if (empty($email)) {
            $errors[] = "L'email est requis";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide";
        } else {
            // Recherche de l'utilisateur
            $user = $userModel->getUserByEmail($email);
            
            if ($user) {
                // Génération d'un token sécurisé
                $token = bin2hex(random_bytes(32));
                $expires = time() + 3600; // 1 heure d'expiration
                
                // Stockage du token en base de données
                $query = "UPDATE users SET reset_token = :token, reset_expires = :expires WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':token', $token);
                $stmt->bindParam(':expires', $expires);
                $stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
                
                if ($stmt->execute()) {
                    // Construction du lien de réinitialisation
                    $resetLink = "https://" . $_SERVER['HTTP_HOST'] . "/?page=forgot-password&token=" . $token;
                    
                    // Configuration de l'email avec PHPMailer
                    $mailConfig = require __DIR__ . '/../config/mail.php';

                    $mail = new PHPMailer\PHPMailer\PHPMailer();
                    $mail->isSMTP();
                    $mail->Host = $mailConfig['host'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $mailConfig['username'];
                    $mail->Password = $mailConfig['password'];
                    $mail->Port = $mailConfig['port'];
                    if (!empty($mailConfig['encryption'])) {
                        $mail->SMTPSecure = $mailConfig['encryption'];
                    }
                    $mail->setFrom($mailConfig['from'], $mailConfig['from_name']);
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Réinitialisation de votre mot de passe';
                    $mail->Body = "
                        <h1>Réinitialisation de mot de passe</h1>
                        <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le lien ci-dessous :</p>
                        <p><a href='$resetLink'>$resetLink</a></p>
                        <p>Ce lien expirera dans 1 heure.</p>
                        <p>Si vous n'avez pas fait cette demande, ignorez simplement cet email.</p>
                    ";
                    
                    // Envoi de l'email
                    if ($mail->send()) {
                        // Journalisation de la demande
                        $activityLog->logAction(
                            $user['id'],
                            'password_reset_request',
                            'Demande de réinitialisation envoyée',
                            $_SERVER['REMOTE_ADDR']
                        );
                        $success = true;
                    } else {
                        $errors[] = "Erreur lors de l'envoi de l'email";
                    }
                } else {
                    $errors[] = "Erreur lors de la génération du token";
                }
            } else {
                $errors[] = "Aucun compte trouvé avec cet email";
            }
        }
    } else {
        // Phase 2 : Réinitialisation (traitée dans reset-password.php)
        header('Location: /actions/reset-password.php?token=' . urlencode($_GET['token']));
        exit;
    }
}

// Stockage des erreurs/succès et redirection
$_SESSION['forgot_errors'] = $errors;
$_SESSION['forgot_success'] = $success;
header('Location: /?page=forgot-password');
exit;