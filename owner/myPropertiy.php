<?php
require_once '../config.php';
require_once '../classes/property.php';


//get classes and properties
$propertyObj = new Property($pdo);
$properties = $propertyObj->getPropertiesByOwner($_SESSION['user_id']);
include 'includes/header.php';
?>
<h2 class="tiltleh2">
    <span>
         My Properties
    </span>
   

    </i> 
</h2>



<div>     
<?php if (isset($_SESSION['message'])): ?>
        <div class="alert-msg <?= $_SESSION['msg_type'] === 'success' ? 'alert-success' : 'alert-error'; ?>">
            <?= htmlspecialchars($_SESSION['message']); ?>
        </div>
       <?php 
        unset($_SESSION['message']); 
        unset($_SESSION['msg_type']);
       ?>
        <?php endif; ?>

<div class="containerprp1">


<div class="containerMyproperty">
    <table class="my-custom-table">
        <thead class="table-header-custom">
            <tr>
                <th>Image</th>
                <th>price</th>
                <th>Title</th>
                <th>Details</th>
                <th>Location</th>
                <th>Description</th> 
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($properties as $prop): ?>
            <tr>
                <td class="cell-image">
                    <img src="../<?= htmlspecialchars($prop['first_image'] ?? 'default.jpg'); ?>"">
                </td>

        
               <td>
                    <span class="price-text"><?= number_format($prop['price'], 2); ?> DH</span>

                </td>

                <td>
                    <strong><?= htmlspecialchars($prop['title']); ?></strong>
                </td>


                <td class="cell-details">
                    Type: <?= htmlspecialchars($prop['type']); ?>
                    Rooms: <?= htmlspecialchars($prop['rooms']); ?><br>
                    <strong>Bath: <?= htmlspecialchars($prop['bathrooms']); ?></strong>
                </td>

                <td><?= htmlspecialchars($prop['city_name']); ?></td>

                <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <?= htmlspecialchars(substr($prop['discription'], 0, 50)); ?>...
                </td>

                <td>
                    <span class="badge-custom status"><?= htmlspecialchars($prop['status']); ?></span>
                </td>

                <td class="cell-actions">
                    <a href="viewPropertyOwner.php?id=<?= $prop['id_property']; ?>" class="btn-custom btn-view" title="View"><i class="fas fa-eye"></i></a>
                    <a href="editeProperties.php?id=<?= $prop['id_property']; ?>" class="btn-custom btn-validate" title="Edit"><i class="fas fa-edit"></i></a>
                    <a href="deletePrpOwner.php?id=<?= $prop['id_property']; ?>" class="btn-custom btn-delete" title="Delete" onclick="return confirm('Are you sure?');">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
   
</div>
</div>
</div>  
<?php  
  include "../includes/footer.php";
?>