<?php
require_once '../config.php';
require_once '../classes/CalssadminDashboard.php';

// check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
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
    <table class="users-management-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
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
                        <td><?= htmlspecialchars($user['role']); ?></td>
                        <td>
                            <span class="um-badge <?= ($user['status'] == 'active') ? 'um-bg-success' : 'um-bg-danger'; ?>">
                                <?= htmlspecialchars($user['status']); ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <?php if ($user['status'] == 'active'): ?>
                                <a href="actions/blockUser.php?id=<?= $user['id_user']; ?>" class="um-btn-action" title="Block">
                                    <i class="fa-solid fa-ban"></i>
                                </a>
                            <?php else: ?>
                                <a href="actions/unblockUser.php?id=<?= $user['id_user']; ?>" class="um-btn-action" title="Unblock">
                                    <i class="fa-solid fa-check"></i>
                                </a>
                            <?php endif; ?>

                            <a href="actions/toggleRole.php?id=<?= $user['id_user']; ?>" class="um-btn-action" title="change role to admin">
                                <i class="fa-solid fa-user-shield"></i>
                            </a>
                            
                            <a href="actions/deleteUser.php?id=<?= $user['id_user']; ?>" class="um-btn-action um-btn-delete" onclick="return confirm('Are you sure?')" title="Delete"> 
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
<?php
// get footer
include 'includes/footer.php';
?>