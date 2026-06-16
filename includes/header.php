<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!--  Rentora Main Header -->
 <header class="main-header">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- ⬅ Logo (Always on the Left) -->
     <a href="index.php" class="logo-container">
              <i class="fa-solid fa-building-shield brand-icon"></i> 
        <span class="logo-text">Rentora</span>
    </a>

<!--  Mobile Menu Button (Visible Only on Mobile, Right Side) -->    
  <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fa-solid fa-bars"></i>
    </button>

 
    <div class="header-right-side" id="headerRightSide">
        
          <!-- Navigation Links (Navbar) -->        <nav class="navbar">
            <a href="index.php" class="nav-link">Home</a>
            <a href="houses.php" class="nav-link">All Houses</a>
            <a href="add.php" class="nav-link add-house"><i class="fa-solid fa-circle-plus"></i> Add House</a>
            <a href="contact.php" class="nav-link">Contact</a>
            

        </nav>

        
          <!-- Dynamic User Account Section -->        <div class="auth-container">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php 
                $profile_url = 'my-profile.php'; 
                if ($_SESSION['user_role'] === 'admin') {
                    $profile_url = 'admin/admin-dashboard.php';
                } elseif ($_SESSION['user_role'] === 'owner') {
                    $profile_url = 'owner/mypropertiy.php';
                }
                ?>
                <a href="<?php echo $profile_url; ?>" class="btn-account">
                    <i class="fa-solid fa-user-gear"></i> My Account
                </a>
                
            <?php else: ?>
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-signup">Sign Up</a>
            <?php endif; ?>

        </div>
    </div>
</header>


<style>
  /* Reset */
* { margin: 0; 
  padding: 0; 
  box-sizing: border-box;
   font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
}

/* Header */
.main-header {
    display: flex; 
    justify-content: space-between; 
    align-items: center;
    padding: 15px 8%;
     background-color: #fcffff;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    width: 100%;
    position: relative;
     z-index: 1000;
}
.logo-container {
     display: flex;
     align-items: center;
     gap: 10px;
     text-decoration: none; 
    }

.logo-container i { 
    font-size: 1.6rem; 
    color: #2563eb;
 }

.logo-text { 
    font-size: 1.4rem; 
    font-weight: 700; 
    color: #1e3a8a; 
    letter-spacing: -0.5px;
 }

.header-right-side {
     display: flex; 
     align-items: center;
      gap: 35px; 
 }

.navbar { 
    display: flex; 
    align-items: center; 
    gap: 25px;
 }

.nav-link { 
    text-decoration: none; 
    color: #4b5563;
    font-size: 0.95rem; 
    font-weight: 500; 
    transition: color 0.2s;
}

.nav-link:hover { 
    color: #2563eb; 
}

.nav-link.add-house {
     background-color: #2563eb; 
     color: #fff !important;
      padding: 8px 16px; 
      border-radius: 6px; 
      display: flex;
      align-items: center; 
      gap: 6px;
       font-weight: 600; 
}

.auth-container { 
    display: flex;
     align-items: center;
      gap: 15px;
}

.btn-login { 
    text-decoration: none; 
    color: #4b5563;
    font-size: 0.95rem;
    font-weight: 500; 
}
.btn-signup { 
    text-decoration: none; 
    background-color: #e5e7eb; 
    color: #1f2937;
    padding: 8px 18px;
    border-radius: 6px;
    font-size: 0.95rem; 
}
.btn-account { 
    text-decoration: none;
     background-color: #f3f4f6;
    color: #1f2937;
    padding: 8px 16px;
    border-radius: 6px;
    border: 1px solid #e5e7eb; 
    font-weight: 600; 
    display: flex;
    align-items: center;
    gap: 8px; 
}

.btn-logout {

    text-decoration: none;
    color: #dc2626;
    font-size: 0.95rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    transition: all 0.2s;
   
}

.btn-logout:hover {
    color: #b91c1c;
    background-color: #fef2f2;
    border-radius: 6px;
}

.mobile-menu-btn { 
    display: none;
    background: none;
    border: none; 
    font-size: 1.5rem; 
    cursor: pointer;
 }

/* Responsive Header */
@media (max-width: 900px) {
    .mobile-menu-btn { 
        display: block; 
    }

    .header-right-side { 
        position: fixed; 
        top: 0; 
        right: 0; 
        width: 280px;
         height: 100vh; 
         background: #fff;
        box-shadow: -4px 0 15px rgba(0,0,0,0.1); 
        padding: 80px 30px; 
        flex-direction: column; 
        transform: translateX(100%);
        transition: 0.3s;
        z-index: 1001; 
    }
    .header-right-side.active {
        transform: translateX(0);
     }

    .navbar { 
        flex-direction: column; 
        width: 100%; 
        align-items: flex-start;
    }
}

</style>




<script>
    document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.getElementById('mobileMenuBtn');
    const rightSide = document.getElementById('headerRightSide');

// Protection: The code will only run if the button and the menu exist on the page.
    if (menuBtn && rightSide) {
        
        // open and close sidebar on click;
        menuBtn.addEventListener('click', function(event) {
            rightSide.classList.toggle('active');
            event.stopPropagation(); 
        });

// Close the menu when the user clicks outside the menu
        document.addEventListener('click', function(event) {
            if (rightSide.classList.contains('active')) {
                if (!rightSide.contains(event.target) && !menuBtn.contains(event.target)) {
                    rightSide.classList.remove('active');
                }
            }
        });
    }
});
</script>
