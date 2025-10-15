<?php
header('Content-Type: application/json');

// Include PHPMailer classes with your custom path
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'assets/PHPMailer/src/Exception.php';
require 'assets/PHPMailer/src/PHPMailer.php';
require 'assets/PHPMailer/src/SMTP.php';

// Function to sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    // Basic validation
    $errors = [];
    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    if (empty($email) || !isValidEmail($email)) {
        $errors[] = 'Valid email is required.';
    }
    if (empty($subject)) {
        $errors[] = 'Subject is required.';
    }
    if (empty($message)) {
        $errors[] = 'Message is required.';
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(' ', $errors)
        ]);
        exit;
    }

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  // Gmail SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sunandtwork@gmail.com';  // Your Gmail address
        $mail->Password   = 'mqjt sdez vmne tked';     // Your App Password (not regular password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;  // TCP port (or 465 for SSL)

        // Recipients
        $mail->setFrom('sunandtwork@gmail.com', 'Website Contact Form');  // Sender (your Gmail)
        $mail->addAddress('sunandtwork@gmail.com', 'Website Owner');      // Recipient (your Gmail)
        $mail->addReplyTo($email, $name);  // Reply-to (user's email)

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = "
            <html>
            <body>
                <h2>New Contact Form Submission</h2>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Subject:</strong> $subject</p>
                <p><strong>Message:</strong></p>
                <p>$message</p>
            </body>
            </html>
        ";
        $mail->AltBody = "New Contact Form Submission\n\nName: $name\nEmail: $email\nSubject: $subject\nMessage: $message";  // Plain text fallback

        $mail->send();
        echo json_encode([
            'success' => true,
            'message' => 'Thank you! Your message has been sent successfully.'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Sorry, there was an error sending your message: ' . $mail->ErrorInfo
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>