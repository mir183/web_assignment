<?php
include "connect.php";
session_start();

// Check if user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

$email=$_SESSION["email"];
$username=$_SESSION["username"];

// Get all session data for the current user
$stmt=$conn->prepare("SELECT name, email, sessionids FROM userinfo WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result=$stmt->get_result();
$user_data=$result->fetch_assoc();
$stmt->close();

// Parse session IDs from JSON
$session_history=[];
if (!empty($user_data['sessionids'])) {
    $session_history=json_decode($user_data['sessionids'],true)?:[];
}

// Sort by login time (newest first)
usort($session_history,function($a,$b){
    return strtotime($b['login_time'])-strtotime($a['login_time']);
});
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login History - Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="dark-theme.css" rel="stylesheet">
  </head>
  <body>
    <!-- Dark Theme Toggle Button -->
    <button class="theme-toggle" id="themeToggle" title="Toggle Dark Mode">
        <i class="bi bi-moon-fill" id="themeIcon"></i>
    </button>

    <div class="container" style="max-width: 800px; margin: auto; margin-top: 50px;">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Login History</h1>
                
                <!-- User Info Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">User Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user_data['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
                    </div>
                </div>

                <!-- Session Info Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Current Session Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Current Session ID:</strong> 
                            <code class="text-break"><?php echo htmlspecialchars(session_id()); ?></code>
                        </p>
                        <p><strong>Total Login Sessions:</strong> 
                            <span class="badge bg-info"><?php echo count($session_history); ?></span>
                        </p>
                    </div>
                </div>

                <!-- Session History Table -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Login History (Last 10 Sessions)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($session_history)): ?>
                            <p class="text-muted">No login history found.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Session ID</th>
                                            <th>Login Time</th>
                                            <th>IP Address</th>
                                            <th>Logout Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($session_history as $index => $session): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <code class="text-break small">
                                                        <?php echo htmlspecialchars(substr($session['session_id'], 0, 15)) . '...'; ?>
                                                    </code>
                                                </td>
                                                <td>
                                                    <small>
                                                        <?php echo date('M j, Y g:i A', strtotime($session['login_time'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small><?php echo htmlspecialchars($session['ip_address']); ?></small>
                                                </td>
                                                <td>
                                                    <small>
                                                        <?php 
                                                        if (isset($session['logout_time'])) {
                                                            echo date('M j, Y g:i A', strtotime($session['logout_time']));
                                                        } else {
                                                            echo '<span class="text-muted">-</span>';
                                                        }
                                                        ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if ($session['session_id'] === session_id()) {
                                                        echo '<span class="badge bg-success">Current</span>';
                                                    } elseif (isset($session['logout_time'])) {
                                                        echo '<span class="badge bg-danger">Logged Out</span>';
                                                    } else {
                                                        echo '<span class="badge bg-warning">Expired</span>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center">
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="bi bi-house-fill"></i> Back to Dashboard
                    </a>
                    <a href="logout.php" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="dark-theme.js"></script>
  </body>
</html>