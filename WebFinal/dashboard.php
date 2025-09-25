<?php
include "connect.php";
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}
$username=$_SESSION["username"];
$email=$_SESSION["email"];

// Get user profile picture
$stmt = $conn->prepare("SELECT profile_pic FROM userinfo WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$profile_pic = $user_data['profile_pic'] ?? null;
$stmt->close();

?>



<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="dark-theme.css" rel="stylesheet">
  </head>
  <body>
    <!-- Dark Theme Toggle Button -->
    <button class="theme-toggle" id="themeToggle" title="Toggle Dark Mode">
        <i class="bi bi-moon-fill" id="themeIcon"></i>
    </button>

    <div style="max-width: 600px; margin: auto; margin-top: 100px; text-align: center;" >
        <!-- Profile Picture -->
        <div class="mb-4">
            <?php if ($profile_pic && file_exists('uploads/' . $profile_pic)): ?>
                <img src="uploads/<?php echo htmlspecialchars($profile_pic); ?>" 
                     alt="Profile Picture" 
                     class="rounded-circle border border-3" 
                     style="width: 150px; height: 150px; object-fit: cover;">
            <?php else: ?>
                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center border border-3" 
                     style="width: 150px; height: 150px;">
                    <i class="bi bi-person-fill text-white" style="font-size: 4rem;"></i>
                </div>
            <?php endif; ?>
        </div>
        
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <p>Your email: <?php echo htmlspecialchars($email); ?></p>
        <a href="login_history.php" class="btn btn-info">Login History</a>
        <a href="reset_pass.php" class="btn btn-primary">Reset Password</a>
        <a href="del.php" class="btn btn-warning" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone!')">Delete Account</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- <h1>Hello, world!</h1> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="dark-theme.js"></script>
  </body>
</html>

