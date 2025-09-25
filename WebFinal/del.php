<?php
include "connect.php";
session_start();

// Check if user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION["email"];
$username = $_SESSION["username"];

// Get user profile picture for cleanup
$stmt = $conn->prepare("SELECT profile_pic FROM userinfo WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$profile_pic = $user_data['profile_pic'] ?? null;
$stmt->close();

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes') {
        try {
            // Start transaction for safe deletion
            $conn->autocommit(FALSE);
            
            // Delete user from database
            $delete_stmt = $conn->prepare("DELETE FROM userinfo WHERE email = ?");
            $delete_stmt->bind_param("s", $email);
            
            if ($delete_stmt->execute()) {
                // Delete profile picture if exists
                if ($profile_pic && file_exists('uploads/' . $profile_pic)) {
                    unlink('uploads/' . $profile_pic);
                    error_log("Profile picture deleted: " . $profile_pic);
                }
                
                // Clean up any temporary files
                $temp_files = glob('uploads/temp_*' . $profile_pic);
                foreach ($temp_files as $temp_file) {
                    if (file_exists($temp_file)) {
                        unlink($temp_file);
                        error_log("Temporary file deleted: " . $temp_file);
                    }
                }
                
                // Commit transaction
                $conn->commit();
                
                // Destroy session
                session_unset();
                session_destroy();
                
                // Redirect to confirmation page
                header("Location: del.php?deleted=success");
                exit();
            } else {
                // Rollback transaction
                $conn->rollback();
                $error = "Failed to delete account. Please try again.";
            }
            
            $delete_stmt->close();
            $conn->autocommit(TRUE);
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $conn->autocommit(TRUE);
            error_log("Account deletion error: " . $e->getMessage());
            $error = "An error occurred while deleting your account. Please try again.";
        }
    }
}

// Check if account was successfully deleted
$account_deleted = isset($_GET['deleted']) && $_GET['deleted'] === 'success';
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Delete Account - Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="dark-theme.css" rel="stylesheet">
  </head>
  <body>
    <!-- Dark Theme Toggle Button -->
    <button class="theme-toggle" id="themeToggle" title="Toggle Dark Mode">
        <i class="bi bi-moon-fill" id="themeIcon"></i>
    </button>

    <div class="container" style="max-width: 600px; margin: auto; margin-top: 100px;">
        <?php if ($account_deleted): ?>
            <!-- Account Successfully Deleted -->
            <div class="text-center">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h1 class="text-success mb-4">Account Deleted Successfully</h1>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Your account has been permanently deleted</h5>
                        <p class="card-text">
                            All your data, including profile information and login history, has been removed from our system.
                        </p>
                        <hr>
                        <p class="text-muted small">
                            <strong>What was deleted:</strong><br>
                            • Your user account and profile<br>
                            • Profile picture and uploaded files<br>
                            • Login history and session data<br>
                            • All associated personal information
                        </p>
                        <div class="mt-4">
                            <a href="reg.php" class="btn btn-primary">Create New Account</a>
                            <a href="login.php" class="btn btn-outline-secondary">Login with Different Account</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Account Deletion Form -->
            <div class="text-center mb-4">
                <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 4rem;"></i>
                <h1 class="text-warning mt-3">Delete Your Account</h1>
            </div>

            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">⚠️ Permanent Account Deletion</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger" role="alert">
                        <strong>Warning:</strong> This action cannot be undone! Once you delete your account, all your data will be permanently removed.
                    </div>

                    <h6>What will be deleted:</h6>
                    <ul class="mb-4">
                        <li>Your user account and profile information</li>
                        <li>Profile picture and all uploaded files</li>
                        <li>Login history and session data</li>
                        <li>All personal data associated with your account</li>
                    </ul>

                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" id="deleteForm">
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="understand_warning" required>
                            <label class="form-check-label" for="understand_warning">
                                I understand that this action is permanent and cannot be undone
                            </label>
                        </div>

                        <input type="hidden" name="confirm_delete" value="yes">
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger" id="deleteBtn">
                                <i class="bi bi-trash-fill"></i> Delete My Account Permanently
                            </button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel and Go Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="dark-theme.js"></script>
    
    <script>
        // Show loading state on form submission
        document.getElementById('deleteForm')?.addEventListener('submit', function(e) {
            // Show loading state
            const deleteBtn = document.getElementById('deleteBtn');
            deleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Deleting Account...';
            deleteBtn.disabled = true;
        });
    </script>
  </body>
</html>