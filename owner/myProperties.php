<?php
require_once '../config.php';
require_once '../classes/property.php';


//get classes and properties
$propertyObj = new Property($pdo);
$properties = $propertyObj->getPropertiesByOwner($_SESSION['user_id']);
include 'includes/header.php';
?>
<div class="tiltleh2">
    <h2>My Properties</h2>
</div>
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
                    <?= htmlspecialchars(substr($prop['description'], 0, 50)); ?>...
                </td>

                <td>
                    <span class="badge-custom"><?= htmlspecialchars($prop['status']); ?></span>
                </td>

                <td class="cell-actions">
                    <a href="viewProperty.php?id=<?= $prop['id_property']; ?>" class="btn-custom btn-view" title="View"><i class="fas fa-eye"></i></a>
                    <a href="editProperty.php?id=<?= $prop['id_property']; ?>" class="btn-custom btn-validate" title="Edit"><i class="fas fa-edit"></i></a>
                    <a href="deleteProperty.php?id=<?= $prop['id_property']; ?>" class="btn-custom btn-delete" title="Delete" onclick="return confirm('Are you sure?');">
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
  include "../includes/footer.php";
?>