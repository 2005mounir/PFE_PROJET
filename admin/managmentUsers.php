<?php

require_once '../config.php';
require_once '../classes/CalssadminDashboard.php';

// check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

//get class and getAllUsers method
$dashboard = new AdminDashboard($pdo);
$users = $dashboard->getAllUsers();


//get footer ana header
include 'includes/header.php'; 
include 'includes/sidebar.php';
?>

<div class="users-management-container">

     <!-- deleted message or readed-->
     <?php if (isset($_SESSION['user_success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['user_success']; ?>
        </div>
        <?php unset($_SESSION['user_success']); ?>
   <?php endif; ?>
   
      <?php if (isset($_SESSION['user_error'])): ?>
                        <div class="alert-error">
                            <?= $_SESSION['user_error']; ?>
                        </div>
                        <?php unset($_SESSION['user_error']); ?>
    <?php endif; ?>


   <div class="table-responsive">
    <table class="users-management-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>WhatsApp</th>
                <th>Role</th>
                <th>Status</th>
                <th>Registered</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id_user']; ?></td>
                        <td><?= htmlspecialchars($user['name']); ?></td>
                        <td><?= htmlspecialchars($user['email']); ?></td>
                        <td><?= htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($user['whatsapp'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($user['role']); ?></td>
                        <td>
                            <span class="um-badge <?= ($user['status'] == 'active') ? 'um-bg-success' : 'um-bg-danger'; ?>">
                                <?= htmlspecialchars($user['status']); ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <?php if ($user['status'] == 'active'): ?>
                                <a href="blockUser.php?id=<?= $user['id_user']; ?>" class="um-btn-action" title="Block">
                                    <i class="fa-solid fa-ban"></i>
                                </a>
                            <?php else: ?>
                                <a href="unblockUser.php?id=<?= $user['id_user']; ?>" class="um-btn-action" title="Unblock">
                                    <i class="fa-solid fa-check"></i>
                                </a>
                            <?php endif; ?>

                            <a href="toggleRole.php?id=<?= $user['id_user']; ?>" class="um-btn-action" title="change role to admin">
                                <i class="fa-solid fa-user-shield"></i>
                            </a>
                            
                            <a href="deleteUser.php?id=<?= $user['id_user']; ?>" class="um-btn-action um-btn-delete" onclick="return confirm('are you sure to delete this user ?')"> 
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
 </div>
</div>
<?php
// get footer
include 'includes/footer.php';
?>