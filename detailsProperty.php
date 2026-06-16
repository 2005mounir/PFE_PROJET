<?php
require_once 'config.php';
require_once 'classes/property.php';


// 1. جلب البيانات
$propertyObj = new property($pdo);
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: ../index.php'); exit; }

$property = $propertyObj->getPropertyDetailsForAdmin($id);
$images = $propertyObj->getImagesByProperty($id);

// 2. المنطق الديناميكي لتحديد الـ Header والـ Sidebar
$role = $_SESSION['user_role'] ?? 'guest'; // admin, owner, أو guest

// تحديد المسارات (استخدمي مسارات صحيحة حسب تنظيم ملفاتك)
if ($role === 'admin') {
    include 'admin/includes/header.php';
    include 'admin/includes/sidebar.php';
} elseif ($role === 'owner') {
    include 'owner/includes/header.php';
    
} else {
    include 'includes/header.php'; // Header للمستخدم العادي
}

// 3. تحديد الصلاحيات
$isAdmin = ($role === 'admin');
$isOwner = (isset($_SESSION['user_id']) && $property['id_user'] == $_SESSION['user_id']);
?>

<div class="property-view">
    <h1 class="property-title"><?= htmlspecialchars($property['title']); ?></h1>

    <div class="gallery-container">
        <div class="main-image">
            <img id="current-img" src="../<?= htmlspecialchars($images[0]['image_path'] ?? 'default.jpg'); ?>" alt="Main Property">
        </div>
        <div class="thumbnails">
            <?php foreach ($images as $img): ?>
                <img src="../<?= htmlspecialchars($img['image_path']); ?>" onclick="changeMainImage(this.src)" class="thumb">
            <?php endforeach; ?>
        </div>
    </div>

    <div class="info-grid">
        <div class="property-data">
            <h3>Property Details</h3>
            <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($property['discription'])); ?></p>
            <p><strong>Price:</strong> <?= number_format($property['price'], 2); ?> DH</p>
            <p><strong>Type:</strong> <?= htmlspecialchars($property['type']); ?></p>
            <p><strong>Rooms:</strong> <?= htmlspecialchars($property['rooms']); ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($property['city_name']); ?></p>
        </div>

        <div class="right-column">
            <div class="owner-data">
                <h3>Contact Owner</h3>
                <p><strong>Owner:</strong> <?= htmlspecialchars($property['owner_name']); ?></p>
                 <p><strong>Phone:</strong> <?= htmlspecialchars($property['owner_phone']); ?></p>


                     <?php
                        // Normalize phone number to international format
                        $phone = preg_replace('/[^0-9]/', '', $property['owner_whatsapp']);
                        $cleanPhone = (strpos($phone, '0') === 0) ? '212' . substr($phone, 1) : $phone;
                        

                    $encodedId = base64_encode($property['id_property']);
                    $propertyUrl = "http://localhost/project_pfe/viewProperty.php?id=" . $encodedId;


                        // Advanced formatting (icons and bold styling)
                        $message = " Notification de la plateforme immobilière Rentora   \n\n" .
                                "━━━━━━━━━━━━━━━━━\n" .
                                " Bien immobilier : " . $property['title'] . "\n" .
                                " Prix de location : " . number_format($property['price'], 0, ',', ' ') . " DH\n" .
                                " L’emplacement : " . $property['city_name'] . "\n" .
                                "━━━━━━━━━━━━━━━━━\n\n" .
                            " Bonjour " . $property['owner_name'] . "، Je suis intéressé par l’immobilier que vous proposez" . "\n\n".
                                'Lien de mon bien immobilier '." ".$propertyUrl;
                            ;

                            
                        // Data encoding
                        $encodedMsg = urlencode($message);
                        $whatsappUrl = "https://wa.me/" . $cleanPhone . "?text=" . $encodedMsg           
                 ?>

                   
                 <a href="<?= $whatsappUrl; ?>" target="_blank" class="whatsapp-btn">
                  Contactez-moi
             </a>




            </div> 
        </div>
    </div>

    <div id="map-container" data-lat="<?= $property['latitude'] ?>" data-lng="<?= $property['longitude'] ?>">
        <div id="map"></div>
    </div>
</div>

<script>
function changeMainImage(src) {
    const mainImg = document.getElementById('current-img');
    if (mainImg) {
        mainImg.style.opacity = '0.5';
        mainImg.src = src;
        setTimeout(() => {
            mainImg.style.opacity = '1';
        }, 100);
    }
}
</script>
<?php
if ($role === 'admin') {
    include 'admin/includes/footer.php';
    
} else {
    
  include 'includes/footer.php';
}
?>