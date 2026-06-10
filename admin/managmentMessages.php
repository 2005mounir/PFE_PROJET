<?php
// get config and class;
require_once '../config.php';
require_once '../classes/CalssadminDashboard.php';


//check if ueser is admin;
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

//get class and getallMessages method
$dashboard = new AdminDashboard($pdo);
$messages = $dashboard->getAllMessages();

//get footer ana header
include 'includes/header.php'; 
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="content-body">
    <div class ="messagesh2">
        <h2>Messages Management</h2>
    </div> 
    
     <!-- deleted message or readed-->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

 <div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th >ID</th>
                <th>Sender</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Message</th> <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            
        <?php foreach($messages as $msg): ?>
        <tr>
            <td class="data-label"><?= $msg['id_message'] ?></td>
            <td class="data-label"><?= htmlspecialchars($msg['name']) ?></td>
            <td class="data-label"><?= htmlspecialchars($msg['email']) ?></td>
            <td class="data-label"><?= htmlspecialchars($msg['subject']) ?></td>
            <td class="data-label" style="max-width: 300px; word-wrap: break-word;"><?= htmlspecialchars($msg['message']) ?></td>
            <td class="data-label"><?= date('Y-m-d H:i', strtotime($msg['created_at'])) ?></td>
           <td class="data-label">
              <span class="badge <?= ($msg['status'] == 'unread') ? 'badge-unread' : 'badge-read' ?>">
                    <?= ucfirst($msg['status']) ?>
            </span>
          </td>
           
             <td td class="data-label" data-label="Actions">
              <div class="actions-wrapper">
                    <a href="setReadMessages.php?id=<?= $msg['id_message'] ?>" class=" btn-read btn-action"  title="Read">
                        <i class="fas fa-check-circle"></i>
                    </a>
                    
                    <a href="setUnreadMessages.php?id=<?= $msg['id_message'] ?>" class=" btn-unread btn-action" title="Unraed">
                        <i class="fas fa-envelope-open"></i>
                    </a>
                    
                    <a href="deleteMessages.php?id=<?= $msg['id_message'] ?>" class="btn-action btn-delete" onclick="return confirm('are you sure to delete this message ?')" title="delete">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </div>
            </td>

        </tr>
        <?php endforeach; ?>
      </tbody>
   </table>
</div>
    </div>
</div>

<?php
// get footer
include 'includes/footer.php';
?>