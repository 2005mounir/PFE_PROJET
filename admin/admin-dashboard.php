<?php
  session_start();

// Include security gates and layout components
include 'includes/header.php';
include 'includes/sidebar.php';



require_once '../classes/database.php';        
require_once '../classes/CalssadminDashboard.php';

//check if ueser is admin;
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$connect = new Database();
$pdo = $connect->connect();

$dashboard = new AdminDashboard($pdo);

//get totale numbers from class
$totalUsers       = $dashboard->getTotalUsers();
$totalProperties  = $dashboard->getTotalProperties(); 
$approvedProps    = $dashboard->getApprovedProperties();
$pendingProps     = $dashboard->getPendingProperties();
$getTotaleMessages = $dashboard->getTotaleMessages();
$bannedUsers      = $dashboard->blockedUsers();
$rejectedProps    = $dashboard->getRejectedProperties();
$recentUnread     = $dashboard->getUnreadRecentMessages();
$tottalUnreadMessages = $dashboard-> getUnreadMessagesCount();
$recentUsers = $dashboard->getRecentUsers(5);
?>




<div class="content-body">

    <div class="stats-grid">
        
        <div class="stat-card card-blue">
            <div class="card-icon"><i class="fa-solid fa-users"></i></div>
            <div class="card-info">
                <h3><?= htmlspecialchars($totalUsers) ?></h3>
                <p>Total Users</p>
                <span class="card-sub">Registered accounts</span>
            </div>
        </div>



        <div class="stat-card card-banned">
            <div class="card-icon"><i class="fa-solid fa-user-slash"></i></div>
            <div class="card-info">
                <h3><?= htmlspecialchars($bannedUsers) ?></h3>
                <p>Blocked Users</p>
                <span class="card-sub">Restricted accounts</span>
            </div>
        </div>



        <div class="stat-card card-purple">
            <div class="card-icon"><i class="fa-solid fa-building"></i></div>
            <div class="card-info">
                <h3><?= htmlspecialchars($totalProperties) ?></h3>
                <p>Total Properties</p>
                <span class="card-sub">All listings</span>
            </div>
        </div>

        <div class="stat-card card-green">
            <div class="card-icon"><i class="fa-solid fa-house-circle-check"></i></div>
            <div class="card-info">
                <h3><?= htmlspecialchars($approvedProps) ?></h3>
                <p>Approved Properties</p>
                <span class="card-sub">Active listings</span>
            </div>
        </div>

       <div class="stat-card card-rejected">
            <div class="card-icon"><i class="fa-solid fa-house-circle-xmark"></i></div>
            <div class="card-info">
                <h3><?= htmlspecialchars($rejectedProps) ?></h3>
                <p>Rejected Houses</p>
                <span class="card-sub">Non-approved listings</span>
            </div>
        </div>

        <div class="stat-card card-orange">
            <div class="card-icon"><i class="fa-solid fa-hourglass-half"></i></div>
            <div class="card-info">
                <h3><?= htmlspecialchars($pendingProps) ?></h3>
                <p>Pending Review</p>
                <span class="card-sub">Awaiting approval</span>
            </div>
        </div>

        <div class="stat-card card-red">
            <div class="card-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
            <div class="card-info">
                <h3><?= htmlspecialchars($getTotaleMessages) ?></h3>
                <p>Total messages</p>
                <span class="card-sub">All messages</span>
            </div>
        </div>

        <div class="stat-card card-unread">
            <div class="card-icon"><i class="fa-solid fa-envelope"></i></div>
            <div class="card-info">
                <h3><?= htmlspecialchars($tottalUnreadMessages) ?></h3>
                <p>Unread Messages</p>
                <span class="card-sub">New tickets to review</span>
            </div>
        </div>

       
        
    </div>
     <div class="dashboard-section">
        <div class="section-header">
            <h2>
                <i class="fa-solid fa-bell icon-notification"></i>
               Live Notifications</h2>
            <a href="admin-notifications.php" class="view-all-btn">View All</a>
        </div>
        
        <div class="activity-list">
            <div class="activity-item">
                <span class="activity-time"></span>
                <p></p>
                <span class="status-dot"></span>
            </div>
        </div>
    </div>

    <div class="tables-dual-grid">
        <div class="dashboard-section">
            <div class="section-header">
                <h2><i class="fa-solid fa-users icon-recent-users"></i> Recent Users</h2>
                <a href="admin-users.php" class="view-all-btn">View All</a>
            </div>
            <div class="table-responsive">



                   <!-- show message succsses pe erreur here  -->
                    <?php if (isset($_SESSION['user_success'])): ?>
                        <div class="alert-success">
                            <?= $_SESSION['user_success']; ?>
                        </div>
                       <?php unset($_SESSION['user_success']); ?>
                 <?php endif; ?>

                <?php if (isset($_SESSION['user_error'])): ?>
                        <div class="alert-error">
                            <?= $_SESSION['user_error']; ?>
                        </div>
                        <?php unset($_SESSION['user_error']); ?>
                <?php endif; ?>




                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentUsers as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['name']); ?></td>
                                <td><?= htmlspecialchars($user['role']); ?></td>
                                <td>
                                    <span class="badge <?= ($user['status'] == 'active') ? 'badge-success' : 'badge-danger'; ?>">
                                        <?= $user['status']; ?>
                                    </span>
                                </td>
                                <td>
                                     <a href="deleteUser.php?id=<?= $user['id_user']; ?>" class="btn btn-delete">Delete</a>
                                    <a href="blockUser.php?id=<?= $user['id_user']; ?>" class="btn btn-ban">Block</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                 </tbody>
                </table>
            </div>
        </div>
<div class="dashboard-section">
            <div class="section-header">
                <h2><i class="fa-solid fa-envelope-open-text icon-recent-messages"></i> Recent Messages</h2>
                <a href="managmentMessages.php" class="view-all-btn">View All</a>
            </div>
            <div class="table-responsive">


       <!-- deleted message-->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>


                        <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Sender</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
              </thead>
                      <tbody>
                                <?php if (empty($recentUnread)): ?>
                                    <tr><td colspan="4" style="text-align:center;">No new unread messages.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentUnread as $msg): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                            
                                            <td>
                                                <?php echo htmlspecialchars(mb_strimwidth($msg['subject'], 0, 23,"...")); ?>
                                            </td>
                                            
                                            <td>
                                                <?php 
                                                $isRead = ($msg['status'] === 'read');
                                                $colorClass = $isRead ? 'green' : 'badge-unread';
                                                ?>
                                                <span class="badge  <?php echo $colorClass; ?>">
                                                    <?php echo ucfirst(htmlspecialchars($msg['status'])); ?>
                                                </span>
                                            </td>
                                            
                                           <td>

                                                <a href="setReadMessages.php?id=<?php echo $msg['id_message']; ?>" class="  btn btn-icon" title="Mark as Read">
                                                       Read
                                                </a>
                                        

                                                <a href="deleteMessages.php?id=<?php echo $msg['id_message']; ?>" 
                                                         class="btn btn-delete" 
                                                          title="Delete"
                                                        onclick="return confirm('are you sure to delete this message ?');">
                                                     Delete
                                                 </a> 
                                          </td>
                                     </tr>
                                  <?php endforeach; ?>
                             <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>

    </div>
</div>

</div>
 </div> 

<?php 
include 'includes/footer.php'; 
?>

</body>
</html>