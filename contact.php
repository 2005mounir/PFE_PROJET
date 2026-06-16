

<?php

require_once 'config.php'; // get pdo and session
require_once 'classes/ContactManager.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. verefy CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security Error: Invalid Token.");
    }

    $errors = [];
    $data = [];

    //(Validation & Sanitization)
    
    //validation name
    $name = trim($_POST['name']);
    if (empty($name) || strlen($name) < 3) {
        $errors['name'] = "Name must be at least 3 characters.";
    } else {
        $data['name'] = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    }

   //validation email
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    } else {
        $data['email'] = $email;
    }

//validation email
    $subject = trim($_POST['subject']);
    if (empty($subject)) {
        $errors['subject'] = "Subject is required.";
    } else {
        $data['subject'] = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
    }

    // validation subject;
    $message = trim($_POST['message']);
    if (empty($message) || strlen($message) < 10) {
        $errors['message'] = "Message must be at least 10 characters.";
    } else {
        $data['message'] = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    }

    //check if erreurs is not emty
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST; 
        header("Location: contact.php");
        exit();
    }

    //  send data to database  using ContactManager
    if (empty($errors)){
    $contact = new ContactManager($pdo);
    if ($contact->sendMessage($data['name'], $data['email'], $data['subject'], $data['message'])) {
        $_SESSION['success'] = "Message sent successfully!";
        header("Location: contact.php");
        exit();
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
    }
} 
}
?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>contact</title>
    
<link rel="stylesheet" href="css/main.css">    
<!-- Font Awesome Icon Library Required for the Header and Page -->  
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="contactBody">

<?php
$page_title = "Contact Us - Rentora";
require_once 'includes/header.php';
?>

<div class="main-content-wrapper">
    
    <div class="contact-header-section">
        <h2>Contact Us</h2>
        <p>Have a question or property inquiry? Send us a message.</p>
    </div>

    <img src="assets/images/contactImage.png" alt="Cozy Interior" class="contact-hero-image">

    <div class="contact-grid">
        
<?php if (isset($_SESSION['success'])): ?>
                <div style="background: green; color: white; padding: 10px; margin-bottom: 10px;">
                    <?php echo $_SESSION['success']; ?>
                </div>
                 <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
        </div>

        <div class="contact-form-side">
            <form action="contact.php" method="POST" class="contact-form">
                
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">   

                <div class="form-group">
                    <label for="name" class='contactlable'>Name</label>
                    <input type="text" id="name" name="name" value="<?php echo $_SESSION['old_input']['name'] ?? ''; ?>"  placeholder="Name..." >
                    <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                           <?php echo $_SESSION['errors']['name'] ?? ''; ?>
                     </div>
                </div>


                <div class="form-group">
                    <label for="email" class='contactlable'>Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $_SESSION['old_input']['email'] ?? ''; ?>"  placeholder="Email...">
                     <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                           <?php echo $_SESSION['errors']['email'] ?? ''; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="subject" class='contactlable'>Subject</label>
                    <input type="text" id="subject" name="subject" value="<?php echo $_SESSION['old_input']['subject'] ?? ''; ?>"  placeholder="Subject..." >
                     <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                          <?php echo $_SESSION['errors']['subject'] ?? ''; ?>
                     </div>
                </div>

                <div class="form-group">
                    <label for="message" class='contactlable'>Message</label>
                    <textarea id="message" name="message" rows="5" placeholder="Message..."><?php echo $_SESSION['old_input']['message'] ?? ''; ?></textarea>
                     <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                        <?php echo $_SESSION['errors']['message'] ?? ''; ?>
                    </div>
                </div>

                <button type="submit" name="submit_message" class="btn-submit-msg">
                    Send Message
                </button>
                
            </form>
            <?php
                unset($_SESSION['errors']);
                unset($_SESSION['old_input']);
            ?>




        <div class="contact-info-sidebar">
            
            <div class="info-block">
                <h3>Other Ways to Reach Us</h3>
                
                <div class="info-item">
                    <i class="fa-solid fa-envelope"></i>
                    <span>rentoraHouses@gmail.com</span>
                </div>
                
                <div class="info-item">
                    <i class="fa-solid fa-phone"></i>
                    <span>+212 0650957336</span>
                </div>
                
                <div class="info-item">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>City Center, Tangier, Morocco</span>
                </div>

                <div class="info-item instagram-item">
                     <i class="fa-brands fa-instagram"></i>
               <span>Rentora_houses</span>
        </div>
            </div>

        </div>

    </div> 
</div> 

<?php include 'includes/footer.php'; ?>


</body>
</html>
