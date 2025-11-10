<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

// --- Recolección de datos del formulario ---
$name    = isset($_POST['name']) ? trim($_POST['name']) : '';
$email   = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

header('Content-Type: application/json');

// --- Validaciones ---
if ($name === '') {
    echo json_encode('El nombre no puede estar vacío');
    exit();
}
if ($email === '') {
    echo json_encode('El email no puede estar vacío');
    exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode('Formato de email inválido');
    exit();
}
if ($subject === '') {
    echo json_encode('El asunto no puede estar vacío');
    exit();
}
if ($message === '') {
    echo json_encode('El mensaje no puede estar vacío');
    exit();
}

// --- Envío de correo ---
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.hostinger.com.ar';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'formulario-contacto@destapaciones-cañerias.com.ar';
    $mail->Password   = 'Destapaciones2021';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';
    $mail->Encoding   = 'base64';

    // Remitente y destinatario
    $mail->setFrom('formulario-contacto@destapaciones-cañerias.com.ar', 'Formulario de contacto - Destapaciones');
    $mail->addAddress('servicios.destapaciones@gmail.com');

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = '
        <h3>Nuevo mensaje desde el sitio web</h3>
        <p><b>Nombre:</b> ' . htmlspecialchars($name) . '</p>
        <p><b>Email:</b> ' . htmlspecialchars($email) . '</p>
        <p><b>Asunto:</b> ' . htmlspecialchars($subject) . '</p>
        <p><b>Mensaje:</b><br>' . nl2br(htmlspecialchars($message)) . '</p>
    ';

    $mail->AltBody =
        "Nuevo mensaje desde el sitio web\n\n" .
        "Nombre: $name\n" .
        "Email: $email\n" .
        "Asunto: $subject\n" .
        "Mensaje:\n$message";

    $mail->send();
    echo json_encode('OK');
} catch (MailerException $e) {
    echo json_encode('No se pudo enviar el mensaje: ' . $e->getMessage());
}
exit();
