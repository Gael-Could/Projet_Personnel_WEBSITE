<?php
/**
 * newsletter_subscribe.php
 * Traite les inscriptions à la newsletter scientifique RIPAC
 * depuis le formulaire dans le footer (index.php, about.php, etc.)
 */
require_once 'db_connect.php';


header('Content-Type: application/json; charset=utf-8');


// Accepte uniquement les POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => true, 'message' => 'Inscription réussie !']);
    exit;
}


$email = trim($_POST['email'] ?? '');


if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Adresse email invalide.']);
    exit;
}


try {
    // Créer la table si elle n'existe pas
    $conn->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        email       VARCHAR(255) NOT NULL UNIQUE,
        actif       TINYINT(1) DEFAULT 1,
        subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");


    // Vérifier si déjà inscrit
    $check = $conn->prepare("SELECT id, actif FROM newsletter_subscribers WHERE email = ? LIMIT 1");
    $check->execute([$email]);
    $existing = $check->fetch(PDO::FETCH_ASSOC);


    if ($existing) {
        if ($existing['actif']) {
            echo json_encode(['success' => true, 'message' => 'Vous êtes déjà inscrit(e) à la newsletter.']);
        } else {
            // Réactiver l'abonnement
            $conn->prepare("UPDATE newsletter_subscribers SET actif = 1 WHERE email = ?")
                 ->execute([$email]);
            echo json_encode(['success' => true, 'message' => 'Votre abonnement a été réactivé.']);
        }
        exit;
    }


    // Nouvelle inscription
    $conn->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)")
         ->execute([$email]);


    // Email de confirmation (optionnel, nécessite SMTP configuré)
    $sujet  = '[RIPAC] Confirmation d\'inscription à la newsletter';
    $corps  = "Bonjour,\n\nVous êtes maintenant inscrit(e) à la newsletter scientifique de la Revue Internationale de Philosophie Antique et Contemporaine (RIPAC).\n\nVous recevrez les annonces de parution, appels à contributions et actualités de la revue.\n\nCordialement,\nL'équipe RIPAC";
    $headers = "From: noreply@ripac-libreville.org\r\nContent-Type: text/plain; charset=UTF-8";
    @mail($email, $sujet, $corps, $headers);


// Dans newsletter_subscribe.php, remplacer @mail(...) par :
//use PHPMailer\PHPMailer\PHPMailer;
//$mail = new PHPMailer();
//$mail->isSMTP();
//$mail->Host       = 'smtp.gmail.com';
//$mail->SMTPAuth   = true;
//$mail->Username   = 'ton.email@gmail.com';
//$mail->Password   = 'ton_mot_de_passe_app';
//$mail->SMTPSecure = 'tls';
//$mail->Port       = 587;
//$mail->setFrom('ton.email@gmail.com', 'RIPAC Libreville');
//$mail->addAddress($email);
//$mail->Subject = '[RPL] Confirmation newsletter';
//$mail->Body    = 'Bonjour, votre inscription est confirmée.';
//$mail->send();


    echo json_encode(['success' => true, 'message' => 'Inscription réussie ! Vous recevrez nos prochaines actualités.']);


} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue. Veuillez réessayer.']);
}