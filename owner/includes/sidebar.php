<?php
// Protection: Ensuring only the property owner can access
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'owner') {
    exit('Access Denied');
}
?>

<div class="sidebar">
    <div class="logo">
        <h2>
            Rentora 
               <i class="fa-solid fa-building-shield brand-icon"></i> 
        </h2>
        <small>Owner Dashboard</small>
    </div>
    
    <ul class="nav-links">
        <li>
            <a href="myProperties.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'myProperties.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-house-chimney"></i> My Properties
            </a>
        </li>

        <li>
            <a href="../add.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'add.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-circle-plus"></i> Add Property
            </a>
        </li>

        <li>
            <a href="../contact.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-envelope"></i> Contact Support
            </a>
        </li>

        <hr class="sidebar-divider">

        <li>
            <a href="../index.php" target="_blank" class="link-site">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Visit Site
            </a>
        </li>

        <li>
            <a href="../logout.php" class="link-logout"> 
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </li>
    </ul>
</div>


<style>
    /* Sidebar Styles */
.sidebar {
    width: 280px;
    background-color: #0f172a;
    color: #ffffff;
    min-height: 100vh;
    padding: 25px 20px;
    display: flex;
    flex-direction: column;
    box-shadow: -3px 0 15px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 0;
}
.sidebar .logo { 
    text-align: center;
     padding-bottom: 25px; 
     border-bottom: 1px solid #1e293b;
      margin-bottom: 25px; 
    }

.sidebar .logo h2 { 
    font-size: 24px; 
    color: #38bdf8;
    font-weight: 700;
    letter-spacing: 0.5px; 
}

.sidebar .logo small { 
    color: #64748b; 
    font-size: 12px; 
    display: block;
     margin-top: 5px;
}

.sidebar .nav-links { 
    list-style: none; 
    display: flex; 
    flex-direction: column;
     gap: 8px; 
}

.sidebar .nav-links li a { 
    display: flex; 
    align-items: center; 
    justify-content: flex-start; 
    flex-direction: row-reverse; 
    gap: 12px; 
    padding: 14px 18px; 
    color: #94a3b8; 
    text-decoration: none; 
    border-radius: 8px; 
    font-size: 15px; 
    font-weight: 500; 
    transition: all 0.2s ease; 
}
.sidebar .nav-links li a:hover {
     background-color: #1e293b; 
     color: #38bdf8; 
     padding-right: 22px; 
}

.sidebar .nav-links li a.active {
     background-color: #2563eb; 
     color: #ffffff; 
}

.sidebar-divider { 
    border: 0; 
    height: 1px;
    background-color: #1e293b;
    margin: 20px 0;
 }
.sidebar .nav-links li a.link-site { 
    color: #38bdf8; 
}
.sidebar .nav-links li a.link-logout {
     color: #f87171;
 }
.sidebar .nav-links li a.link-logout:hover { 
    background-color: #991b1b; 
    color: #ffffff;
 }

.main-panel {
    flex: 1; 
    display: flex;
    flex-direction: column;
    min-width: 0; 
}


/* Header Styles */
.main-header {
    background-color: #ffffff;
    height: 70px;
    padding: 0 40px;
    display: flex;
    justify-content: space-between; 
    align-items: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    border-bottom: 1px solid #e2e8f0;
    flex-direction: row-reverse;
}


.header-title h3 {
     font-size: 1.2rem;
     color: #1e3a8a;
     font-weight: 600;
 }

.header-profile { 
    display: flex; 
    align-items: center; 
    gap: 25px; 
    flex-direction: row-reverse;
 }

.notifications-icon { 
    position: relative; 
    font-size: 20px;
    cursor: pointer; 
    color: #64748b; 
}

.notifications-icon .badge {
     position: absolute;
     top: -5px; 
     right: -5px;
     background-color: #ef4444; 
     color: white; 
     font-size: 10px; 
     padding: 2px 6px; 
     border-radius: 50%; 
}
.user-info { 
   display: flex; 
   align-items: center; 
   gap: 10px; 
   font-size: 14px; 
   color: #64748b;
 }

.user-info strong { 
    color: #1e3a8a; 
    font-weight: 600; 
}
.mobile-toggle-btn {
     display: none; 
    }




/* 1. Header Responsive Styles */
@media (max-width: 768px) {
    /* Header Fixed in Mobile */
    .main-header {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 70px !important;
        background-color: #0f172a !important; 
        display: flex !important;
        flex-direction: row-reverse !important; 
        justify-content: space-between !important;
        align-items: center !important;
        padding: 0 15px !important;
        z-index: 9999 !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05) !important;
        border-bottom: none !important;
    }
   
    .header-title {
        display: flex !important;
        align-items: center !important;
        margin: 0 !important;
        padding: 0 !important;
        gap: 15px;
    }

    .header-title h3 {
        font-size: 1rem !important;
        color: #fff !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important; 
        white-space: nowrap !important;
    }

    .header-profile {
        display: flex !important;
        flex-direction: row-reverse !important;
        align-items: center !important;
        gap: 12px !important; 
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Mobile Toggle Button */
    .mobile-toggle-btn {
        display: flex !important;
        position: static !important; 
        align-items: center !important;
        justify-content: center !important;
        background: #1e293b !important;
        color: #fff !important;
        border: 1px solid #334155 !important;
        padding: 8px 12px !important;
        font-size: 1rem !important;
        border-radius: 6px !important;
        cursor: pointer !important;
    }

    .notifications-icon {
        position: relative !important;
        display: block !important;
        font-size: 1.1rem !important;
        color: #fff !important;
    }

    .user-info span:first-child { display: none !important; }

    .role-badge {
        display: inline-block !important;
        font-size: 0.75rem !important;
        color: #94a3b8 !important;
        background: transparent !important;
        padding: 0 !important;
        margin: 0 !important;
        white-space: nowrap !important;
    }
}

/* 2. Sidebar Responsive Styles */
@media (max-width: 768px) {
    .wrapper { flex-direction: column; }

    .sidebar {
        position: fixed !important;
        top: 0 !important;
        right: auto !important; 
        left: -260px !important; 
        width: 260px !important;
        height: 100vh !important;
        z-index: 10000 !important;
        transition: all 0.3s ease-in-out !important;
    }

    .sidebar.active {
        left: 0 !important;  
         right: auto !important;
    }

    .content-body {
        padding-top: 100px !important;
        margin-left: 0 !important;
        width: 100% !important;
    }
}

/* 3. Global Hidden Button */
@media (min-width: 769px) {
    .mobile-toggle-btn { display: none !important; }
}

</style>




<script>
    // sidebar of dashbord awner 

document.addEventListener("DOMContentLoaded", function() {
    const menuBtn = document.getElementById("mobile-menu-btn");
    const sidebar = document.querySelector(".sidebar"); 

    if (menuBtn && sidebar) {
        menuBtn.addEventListener("click", function(e) {
            e.stopPropagation();
            sidebar.classList.toggle("active");
            
            const icon = menuBtn.querySelector("i");
            if (sidebar.classList.contains("active")) {
                icon.className = "fa-solid fa-xmark";
            } else {
                icon.className = "fa-solid fa-bars";
            }
        });

        
       //coleses menu when user click 
        document.addEventListener("click", function(e) {
            if (!sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
                sidebar.classList.remove("active");
                const icon = menuBtn.querySelector("i");
                if (icon) icon.className = "fa-solid fa-bars";
            }
        });
    }
});
</script>