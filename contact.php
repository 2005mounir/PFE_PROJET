<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>contact</title>
    
<link rel="stylesheet" href="css/main.css">    
<!-- Font Awesome Icon Library Required for the Header and Page -->    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        <div class="contact-form-side">
            <form action="send_message.php" method="POST" class="contact-form">
                
                <div class="form-group">
                    <label for="name" class='contactlable'>Name</label>
                    <input type="text" id="name" name="name" placeholder="Name" required>
                </div>

                <div class="form-group">
                    <label for="email" class='contactlable'>Email</label>
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>

                <div class="form-group">
                    <label for="subject" class='contactlable'>Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="Subject" required>
                </div>

                <div class="form-group">
                    <label for="message" class='contactlable'>Message</label>
                    <textarea id="message" name="message" rows="5" placeholder="Message" required></textarea>
                </div>

                <button type="submit" name="submit_message" class="btn-submit-msg">
                    Send Message
                </button>
                
            </form>
        </div>

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
