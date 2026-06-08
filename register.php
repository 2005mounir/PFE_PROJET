<?php
require_once 'config.php';
require_once 'classes/users.php';





$errors = [];



if (!isset($_SESSION['register_attempt'])) {
    $_SESSION['register_attempt'] = [];
}

$_SESSION['register_attempt'] = array_filter(
    $_SESSION['register_attempt'],
    fn($t) => $t > time() - 60
);

if (count($_SESSION['register_attempt']) >= 5) {
    die("Too many attempts. wait 60 seconds");
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {

      if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF Invalid");
    }


    $_SESSION['register_attempt'][] = time();


        $name = trim($_POST['name'] ?? ''); 
        $email = trim($_POST['email'] ?? ''); 
        $password = $_POST['password'] ?? ''; 
        $phone = trim($_POST['phone'] ?? '');
        $whatsapp = trim($_POST['whatsapp'] ?? '');
        $role = $_POST['role'] ?? 'tenant'; 
 
     //name validation
      if (empty($name))
         { $errors[] = "Full name is required."; } 
      elseif (strlen($name) < 3 || strlen($name) > 50) 
        { $errors[] = "Name must be between 3 and 50 characters."; }
       elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/u", $name))
         { $errors[] = "Name contains invalid characters."; } 


    //  email validation
        if (empty($email))
        { $errors[] = "Email is required."; } 
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        { $errors[] = "Invalid email format."; }
        elseif (strlen($email) > 150)
        { $errors[] = "Email is too long."; } 


    // password validation
         if (empty($password)) 
            { $errors[] = "Password is required."; } 
         elseif (strlen($password) < 8)
             { $errors[] = "Password must be at least 8 characters."; } 
         elseif (!preg_match('/[A-Z]/', $password))
             { $errors[] = "Password must contain at least one uppercase letter."; } 
         elseif (!preg_match('/[a-z]/', $password))
             { $errors[] = "Password must contain at least one lowercase letter."; } 
         elseif (!preg_match('/[0-9]/', $password)) 
            { $errors[] = "Password must contain at least one number."; } 
         elseif (!preg_match('/[\W]/', $password)) 
            { $errors[] = "Password must contain at least one special character."; }


          // phone validation 
           if (empty($phone))
             { $errors[] = "Phone number is required."; }
          elseif (!preg_match('/^[0-9+\s]{8,20}$/', $phone)){
             $errors[] = "Invalid phone number.";
             } 


            // watsab validation
           if (!empty($whatsapp))
            { if (!preg_match('/^[0-9+\s]{8,20}$/', $whatsapp)){
                 $errors[] = "Invalid WhatsApp number."; } 
            } 


             // role validation
            $allowed_roles = ['tenant', 'owner']; 
            if (!in_array($role, $allowed_roles)) { $errors[] = "Invalid role selected."; 
            }



    if (empty($errors)) {


         $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
            $email = filter_var($email, FILTER_SANITIZE_EMAIL); 
            $phone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
            $whatsapp = htmlspecialchars($whatsapp, ENT_QUOTES, 'UTF-8');

            //hashed password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT); 
           
           // sin up object;
            $SinupuserObj = new Sinupuser($pdo);

            // check if email in database
            if($SinupuserObj->emailExists($email)){
                 $errors[] = "Email already exists.";
            }else{
               $user_id = $SinupuserObj->registre($name, $email, $hashed_password , $phone, $whatsapp, $role);

               if($user_id){

               //Securing the Session
                session_regenerate_id(true);

              //Create a session for this user
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $name; 
                $_SESSION['user_role'] = $role;  
                
               //User redirection to home page(index.html)
                header("Location: index.php");
                exit();

             }
             else{
                $errors[] = "Something went wrong. Please try again.";
             }
            }
                
}
}





?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/main.css">
</head>
<body class="registerBody">
    
<?php
require_once 'includes/header.php';
?>

<div class="register1-wrapper">
    <div class="registreImages1">
       <img src="../assets/images/contactImage.png" alt="Registration Side Image" class="side-image">
   </div>  

   <div class="register-container1">
        <h2>Create Account</h2>

        <!-- ERRORS -->
        <?php if (!empty($errors)) : ?>
            <div style="color:red;">
                <?php foreach ($errors as $error) : ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- SUCCESS -->
        <?php if (!empty($success)) : ?>
            <div style="color:green;">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

            
        <form method="POST" autocomplete="off">

            <!-- CSRF TOKEN -->
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <!-- NAME -->
            <input type="text" name="name" placeholder="Full Name" >
            

            <!-- EMAIL -->
            <input type="email" name="email" placeholder="Email" >
           

            <!-- PASSWORD -->
            <input type="password" name="password" placeholder="Password" >
            

            <!-- PHONE -->
            <input type="text" name="phone" placeholder="Phone Number" >
            

            <!-- WHATSAPP -->
            <input type="text" name="whatsapp" placeholder="WhatsApp (optional)">
            

            <!-- ROLE -->
            <select name="role">
                <option value="tenant">Tenant</option>
                <option value="owner">Owner</option>
            </select>

          

            <button type="submit" name="register">Sign Up</button>

        </form>
   </div>
 </div>
 <?php
require_once 'includes/footer.php';
?>  
</body>