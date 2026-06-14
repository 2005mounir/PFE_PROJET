<footer class="main-footer">
    <script src="js/main.js"></script>
    <div class="footer-container">
        
        <div class="footer-column footer-about">
            <a href="index.php" class="footer-logo">
                    <span>Rentora</span>
                 <i class="fa-solid fa-building-shield brand-icon"></i> 
               
            </a>
            <p>Your premium global platform to find and rent the best properties with ease, transparency, and high security.</p>
        </div>

        <div class="footer-column footer-links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="houses.php">All Houses</a></li>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="terms.php" class="terms-link">Terms & Conditions</a></li>
            </ul>
        </div>

        <div class="footer-column footer-contact">
            <h3>Stay Connected</h3>
            <p>Have questions or inquiries? Reach out to us anytime.</p>
            <div class="footer-socials">
                <a href="https://instagram.com/Rentora_houses" target="_blank" class="footer-insta">
                    <i class="fa-brands fa-instagram"></i> Rentora_houses
                </a>
            </div>
        </div>

    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Rentora. All rights reserved.</p>
    </div>
</footer>

<style>

    /* Footer Styles */
.main-footer { 
    background-color: #fcffff; 
    border-top: 1px solid #e2e8f0; 
    padding: 60px 0 20px 0; 
    width: 100%; 
    margin-top: auto; 
}

.footer-container { 
    max-width: 1280px; 
    margin: 0 auto; 
    padding: 0 5%; 
    display: grid; 
    grid-template-columns: 1.4fr 0.8fr 0.8fr; 
    gap: 60px; 
}

.footer-column h3 { 
    color: #2563eb; 
    font-size: 1.15rem; 
    font-weight: 600; 
    margin-bottom: 20px; 
}

.footer-logo { 
    display: inline-flex; 
    align-items: center; 
    gap: 10px; 
    color: #2563eb; 
    font-size: 1.4rem; 
    font-weight: 700; 
    text-decoration: none; 
    margin-bottom: 15px; 
}

.footer-about p { 
    color: #64748b; 
    font-size: 0.95rem; 
    line-height: 1.6; 
    max-width: 340px; 
}

.footer-links ul { 
    list-style: none; 
    padding: 0; 
    margin: 0; 
}

.footer-links ul li { 
    margin-bottom: 12px; 
}

.footer-links ul li a { 
    color: #475569; 
    text-decoration: none; 
    font-size: 0.95rem; 
    transition: all 0.2s ease; 
    display: inline-block; 
}

.footer-links ul li a:hover { 
    color: #2563eb; 
    transform: translateX(4px); 
}

.footer-links ul li a.terms-link { 
    color: #64748b; 
    font-weight: 500; 
}

.footer-links ul li a.terms-link:hover { 
    color: #1e3a8a; 
}

.footer-contact p { 
    color: #64748b; 
    font-size: 0.95rem; 
    margin-bottom: 18px; 
    line-height: 1.5; 
}

.footer-insta { 
    display: inline-flex; 
    align-items: center; 
    gap: 8px; 
    color: #334155; 
    text-decoration: none; 
    font-weight: 500; 
    font-size: 0.95rem; 
    background-color: #f8fafc; 
    padding: 10px 16px; 
    border-radius: 6px; 
    border: 1px solid #e2e8f0; 
    transition: all 0.2s ease; 
}

.footer-insta:hover { 
    background-color: #fff1f2; 
    color: #e1306c; 
    border-color: #fda4af; 
    transform: translateY(-2px); 
}

.footer-bottom { 
    max-width: 1280px; 
    margin: 50px auto 0 auto; 
    padding: 20px 5% 0 5%; 
    border-top: 1px solid #f1f5f9; 
    text-align: center; 
}

.footer-bottom p { 
    color: #94a3b8; 
    font-size: 0.88rem; 
}

/* Footer Responsive */
@media (max-width: 900px) {
    .footer-container { 
        grid-template-columns: 1fr; 
        gap: 40px; 
        text-align: center; 
    }
    .footer-about p { margin: 0 auto; }
}
</style>