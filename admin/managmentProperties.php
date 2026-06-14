<?php

require_once '../config.php';
require_once '../classes/property.php';//get class 

//check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$dashboard = new Property($pdo);
$properties = $dashboard->getAllPropertiesWithDetails();




//get footer ana header
include 'includes/header.php'; 
include 'includes/sidebar.php';
?>


<div class="properties-management-container">
 <h2>Management Properties</h2>
    <div class="table-container">

    
<?php

// check if session message isset
if (isset($_SESSION['message'])): ?>
    <div class="alert alert-msg alert-success" 
        <?php echo ($_SESSION['msg_type'] == 'success') ? 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;' : 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'; ?>">
        <?= $_SESSION['message']; ?>
    </div>
    
<?php 
//delete message from session
    unset($_SESSION['message']);
    unset($_SESSION['msg_type']);
endif; 
?>


<table class="my-custom-table">
    <thead class="table-header-custom">
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Title</th>
            <th>Property Details</th>
            <th>Owner Info</th>
            <th>Location</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($properties as $prop): ?>
        <tr>
            <!-- id of properties -->
            <td><?= $prop['id_property']; ?></td>

            <!-- image -->
            <td class="cell-image">
                <img src="../<?= htmlspecialchars($prop['first_image'] ?? 'default.jpg'); ?>" width="70">
            </td>
            
            <!-- title -->
            <td class="cell-title">
                <strong><?= htmlspecialchars($prop['title']); ?></strong>
                <span class="price-text"><?= number_format($prop['price'], 2); ?> DH</span>
            </td>

             <!-- bathrooms and rooms -->
            <td class="cell-details">
                Type: <?= htmlspecialchars($prop['type']); ?>
                Rooms: <?= htmlspecialchars($prop['rooms']); ?>
                <strong>Bath: <?= htmlspecialchars($prop['bathrooms']); ?></strong> 
         </td>

            <!-- ounser name and email -->
            <td class="cell-owner">
                <?= htmlspecialchars($prop['owner_name']); ?>
                <small class="email-text"><?= htmlspecialchars($prop['owner_email']); ?></small>
            </td>

             <!--location information  -->
           <td class="cell-location">
                <?= htmlspecialchars($prop['city_name']); ?> / <?= htmlspecialchars($prop['country_name']); ?>
                <small class="text-muted">
                    Lat: <?= htmlspecialchars($prop['latitude']); ?>
                    Long: <?= htmlspecialchars($prop['longitude']); ?>
                </small>
            </td>

            <!-- properties status -->
            <td class="cell-status">
                <span class="badge-custom"><?= htmlspecialchars($prop['status']); ?></span>
            </td>

            <td class="cell-actions">
                <a href="viewProperty.php?id=<?= $prop['id_property']; ?>" class="btn-custom btn-view" title="View">
                    <i class="fas fa-eye"></i>
                </a>


                    <!-- reject button -->
                    <?php if ($prop['status'] == 'pending' || $prop['status'] == 'approved'): ?>
                            <a href="rejectedProperty.php?id=<?= $prop['id_property']; ?>" 
                                class="btn-custom btn-reject" title="Reject"
                              onclick="return confirm('Are you sure you want to reject this property?');">
                            <i class="fas fa-times-circle"></i>
                    </a>
                    <?php endif; ?>

                      <!--validate button  -->
                    <?php if ($prop['status'] == 'pending'): ?>
                            <a href="validateProperty.php?id=<?= $prop['id_property']; ?>" 
                            class="btn-custom btn-validate" title="Validate" >
                           <i class="fas fa-check-circle"></i>
                    </a>
                <?php endif; ?>
                <?php if ($prop['status'] == 'rejected'): ?>
                            <a href="validateProperty.php?id=<?= $prop['id_property']; ?>" 
                            class="btn-custom btn-validate" title="Validate" >
                           <i class="fas fa-check-circle"></i>
                    </a>
                <?php endif; ?>
                 

                <a href="deleteProperty.php?id=<?= $prop['id_property']; ?>" 
                   class="btn-custom btn-delete" title="Delete" 
                   onclick="return confirm('Are you sure you want to delete this property?');">
                   <i class="fas fa-trash-alt"></i>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
  </table>
 </div>
</div>

<?php
// get footer
include 'includes/footer.php';
?>






