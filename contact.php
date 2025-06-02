<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // PHPMailer via Composer install

// Database connection details
$servername = "103.21.58.4:3306";
$username   = "Chameza_admin";
$password   = "Chamez@_admin";
$dbname     = "contact_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data safely
    $name    = $conn->real_escape_string($_POST['name']);
    $email   = $conn->real_escape_string($_POST['email']);
    $phone   = $conn->real_escape_string($_POST['phone']);
    $message = $conn->real_escape_string($_POST['message']);

    // Simple validation
    if (empty($name) || empty($email) || empty($phone) || empty($message)) {
        die("All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO landingpagedata (name, email, phone, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $message);

    if ($stmt->execute()) {
        // Send email to admin via PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'bhin-pp-wb5.webhostbox.net';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'inquiry@chameza.in';
            $mail->Password   = 'CG_Chameza.in';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Sender & Recipient
            $mail->setFrom('inquiry@chameza.in', 'Website Inquiry');
            $mail->addAddress('inquiry@chameza.in'); // Admin email

            // Email Content
            $mail->Subject = 'New Contact Form Submission  by landing page';
            $mail->Body    = "Name: $name\nEmail: $email\nPhone: $phone\nMessage: $message";

            $mail->send();
            header("Location: thankyou.html");
            exit();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        die("Database Error: " . $conn->error);
    }

    $stmt->close();
}
$conn->close();
?>
