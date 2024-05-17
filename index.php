<?php
include('smtp/PHPMailerAutoload.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $to = filter_var($_POST['fname'], FILTER_SANITIZE_EMAIL);
  $cc = filter_var($_POST['cc'], FILTER_SANITIZE_EMAIL);
  $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
  $message = filter_var($_POST['w3review'], FILTER_SANITIZE_STRING);


  if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid "To" email address.';
  }

  if ($cc && !filter_var($cc, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid "Cc" email address.';
  }


  function smtp_mailer($to, $cc, $subject, $message) {
    $mail = new PHPMailer(true); 

    try {
      $mail->isSMTP();
      $mail->SMTPAuth = true;
      $mail->SMTPSecure = 'tls';
      $mail->Host = 'smtp.gmail.com';
      $mail->Port = 587;
      $mail->isHTML(true);
      $mail->CharSet = 'UTF-8';

      $mail->Username = ''; // Replace with your actual username
      $mail->Password = ''; // Replace app passwords 

      $mail->setFrom('', 'Your Name'); // Replace with your actual email address
      $mail->Subject = $subject;
      $mail->Body = $message;

      $mail->addAddress($to);
      if ($cc) {
        $mail->addCC($cc); 
      }

      $mail->send();
      echo 'Email sent successfully!';
    } catch (Exception $e) {
      error_log('Mail Error: ' . $mail->ErrorInfo);
      echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
  }


  if (empty($errors)) {
    smtp_mailer($to, $cc, $subject, $message);
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Compose Email</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f1f3f4;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .compose-container {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
      width: 600px;
      max-width: 100%;
      padding: 20px;
      box-sizing: border-box;
    }

    .compose-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #e0e0e0;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    .compose-header h2 {
      margin: 0;
      font-size: 20px;
      color: #202124;
    }

    .compose-header button {
      background-color: transparent;
      border: none;
      font-size: 24px;
      color: #1a73e8;
      cursor: pointer;
    }

    .compose-body label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #202124;
    }

    .compose-body input,
    .compose-body textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #e0e0e0;
      border-radius: 4px;
      box-sizing: border-box;
      font-size: 14px;
    }

    .compose-body textarea {
      resize: vertical;
      height: 150px;
    }

    .compose-footer {
      display: flex;
      justify-content: flex-end;
      align-items: center;
    }

    .compose-footer button {
      background-color: #1a73e8;
      border: none;
      color: #fff;
      padding: 10px 20px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }

    .compose-footer button:hover {
      background-color: #155db2;
    }

    .error-message {
      color: red;
    }
  </style>
</head>
<body>
  <div class="compose-container">
    <div class="compose-header">
      <h2>New Message</h2>
      <button>&times;</button>
    </div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="compose-body">
        <?php
        if (!empty($errors)) {
          echo '<div class="error-message"><ul>';
          foreach ($errors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
          }
          echo '</ul></div>';
        }
        ?>
        <label for="fname">To</label>
        <input type="email" id="fname" name="fname" required>

        <label for="cc">Cc (optional)</label>
        <input type="email" id="cc" name="cc">

        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" required>

        <label for="w3review">Message</label>
        <textarea id="w3review" name="w3review" rows="4" cols="50" required></textarea>
      </div>
      <div class="compose-footer">
        <button type="submit">Send</button>
      </div>
    </form>
  </div>
</body>
</html>

