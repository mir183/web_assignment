<?php

    include "connect.php";
    session_start();
    
    if(isset($_SESSION["email"])){
        header("Location: dashboard.php");
        exit();
    }


    // Include PHPMailer
    require_once 'phpmailer/PHPMailer.php';
    require_once 'phpmailer/SMTP.php';
    require_once 'phpmailer/Exception.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    // Debug: Test if we can reach PHP processing
    if(isset($_POST['test_connection'])){
        echo json_encode(['success'=>true,'message'=>'PHP connection working']);
        exit();
    }

    // Function to send verification email using PHPMailer SMTP
    function sendVerificationEmail($to_email,$user_name,$verification_code){
        $mail=new PHPMailer(true);

        try{
            // Server settings
            $mail->isSMTP();
            $mail->Host='smtp.gmail.com';
            $mail->SMTPAuth=true;
            $mail->Username='ahmedemon183@gmail.com';      // Your Gmail address
            $mail->Password='****************';             // Your Gmail app password
            $mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port=587;

            // Recipients
            $mail->setFrom('ahmedemon183@gmail.com','AmarApp');
            $mail->addAddress($to_email, $user_name);

            // Content
            $mail->isHTML(true);
            $mail->Subject='Email Verification Code';
            $mail->Body="
            <html>
            <head>
                <title>Email Verification</title>
            </head>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f5f5f5; margin: 0; padding: 20px;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #007bff;'>Hello $user_name!</h2>
                    <p>Thank you for registering with us. Please use the following verification code to complete your registration:</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <div style='background-color: #ffffff; border: 2px solid #007bff; border-radius: 10px; padding: 20px; display: inline-block;'>
                            <h1 style='color: #007bff; font-size: 36px; margin: 0; letter-spacing: 5px;'>$verification_code</h1>
                        </div>
                    </div>
                    
                    <p><strong>Important:</strong></p>
                    <ul>
                        <li>This code will expire in 10 minutes</li>
                        <li>Enter this code exactly as shown</li>
                        <li>Do not share this code with anyone</li>
                    </ul>
                    
                    <p>If you didn't request this verification, please ignore this email.</p>
                    
                    <hr style='border: 1px solid #ccc; margin: 30px 0;'>
                    <p style='color: #666; font-size: 14px;'>
                        Best regards,<br>
                        <strong>AmarApp Team</strong>
                    </p>
                </div>
            </body>
            </html>
            ";

            $mail->AltBody="Hello $user_name! Your verification code is: $verification_code. This code will expire in 10 minutes.";

            $mail->send();
            return true;
            
        }catch(Exception $e){
            error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    $name="";
    $email="";
    $password="";
    $err=null;

    if ($_SERVER["REQUEST_METHOD"]=="POST") {
        if (isset($_POST['step']) && $_POST['step'] === 'verify') {
            // Handle email verification - check code and insert into database
            if (isset($_SESSION['temp_email']) && isset($_SESSION['temp_name']) && isset($_SESSION['temp_password']) && isset($_SESSION['verification_code'])) {
                $entered_code = $_POST['verification_code'];
                $stored_code = $_SESSION['verification_code'];
                
                if($entered_code != $stored_code) {
                    echo json_encode(['success' => false, 'message' => 'Invalid verification code']);
                    exit();
                }
                
                $email = $_SESSION['temp_email'];
                $name = $_SESSION['temp_name'];
                $password = $_SESSION['temp_password'];
                $profile_pic_filename = $_SESSION['temp_profile_pic'] ?? null;
                $temp_file_path = $_SESSION['temp_file_path'] ?? null;
                
                // Check if email already exists
                $checkStmt = $conn->prepare("SELECT email FROM userinfo WHERE email = ?");
                $checkStmt->bind_param("s", $email);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                
                if ($result->num_rows > 0) {
                    // Clean up temporary file if exists
                    if ($temp_file_path && file_exists($temp_file_path)) {
                        unlink($temp_file_path);
                    }
                    echo json_encode(['success' => false, 'message' => 'Email already exists. Please use a different email.']);
                    exit();
                } else {
                    // Move temporary file to final location if exists
                    if ($profile_pic_filename && $temp_file_path && file_exists($temp_file_path)) {
                        $final_path = 'uploads/' . $profile_pic_filename;
                        if (rename($temp_file_path, $final_path)) {
                            error_log("File moved from temp to final: " . $final_path);
                        } else {
                            error_log("Failed to move file from temp to final");
                            $profile_pic_filename = null; // Don't save filename if file move failed
                        }
                    }
                    
                    // Insert into database after successful verification
                    $stmt=$conn->prepare("INSERT INTO userinfo (name, email, pass, profile_pic) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss",$name,$email,$password,$profile_pic_filename);
                    
                    if($stmt->execute()){
                        // Create session for the newly registered user
                        $_SESSION["username"] = $name;
                        $_SESSION["email"] = $email;
                        
                        // Store session ID in database (same as login.php)
                        session_regenerate_id(true);
                        $session_id = session_id();
                        $current_time = date('Y-m-d H:i:s');
                        
                        // Create session history entry
                        $session_array = [[
                            'session_id' => $session_id,
                            'login_time' => $current_time,
                            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                        ]];
                        
                        $update_stmt = $conn->prepare("UPDATE userinfo SET sessionids = ? WHERE email = ?");
                        $json_sessions = json_encode($session_array);
                        $update_stmt->bind_param("ss", $json_sessions, $email);
                        $update_stmt->execute();
                        $update_stmt->close();
                        
                        // Clear temporary session data
                        unset($_SESSION['temp_email']);
                        unset($_SESSION['temp_name']);
                        unset($_SESSION['temp_password']);
                        unset($_SESSION['temp_profile_pic']);
                        unset($_SESSION['temp_file_path']);
                        unset($_SESSION['verification_code']);
                        
                        echo json_encode(['success' => true, 'message' => 'Registration successful', 'redirect' => 'dashboard.php']);
                        exit();
                    }else{
                        echo json_encode(['success' => false, 'message' => 'Error in registration. Please try again.']);
                        exit();
                    }
                    $stmt->close();
                }
                $checkStmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Session expired. Please start registration again.']);
                exit();
            }
        } elseif (isset($_POST['step']) && $_POST['step'] === 'register') {
            // Handle initial form submission - store in session and send email
            try {
                $name = $_POST["name"];
                $email = $_POST["email"];
                $password = $_POST["password"];
                $confirm_password = $_POST["confirm_password"];
                
                // Debug logging
                error_log("Registration attempt for email: " . $email);
                
                if($password != $confirm_password){
                    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
                    exit();
                } 
                
                if(empty($name) || empty($email) || empty($password)) {
                    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
                    exit();
                }
                
                // Generate verification code
                $verification_code = rand(100000, 999999);
                error_log("Generated verification code: " . $verification_code);
                
                // Handle profile picture upload
                $profile_pic_filename = null;
                $temp_file_path = null;
                if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
                    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    $file_type = $_FILES['profile_pic']['type'];
                    $file_size = $_FILES['profile_pic']['size'];
                    
                    // Validate file type
                    if (!in_array($file_type, $allowed_types)) {
                        echo json_encode(['success' => false, 'message' => 'Invalid file type. Please upload JPG, PNG, or GIF images only.']);
                        exit();
                    }
                    
                    // Validate file size (5MB max)
                    if ($file_size > 5 * 1024 * 1024) {
                        echo json_encode(['success' => false, 'message' => 'File too large. Please upload an image smaller than 5MB.']);
                        exit();
                    }
                    
                    // Generate filename based on email
                    $email_part = explode('@', $email)[0]; // Get part before @
                    $file_extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
                    $profile_pic_filename = $email_part . 'PIC.' . $file_extension;
                    
                    // Save file temporarily with session ID to avoid conflicts
                    $temp_file_path = 'uploads/temp_' . session_id() . '_' . $profile_pic_filename;
                    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $temp_file_path)) {
                        error_log("Temporary file saved: " . $temp_file_path);
                    } else {
                        error_log("Failed to save temporary file: " . $temp_file_path);
                        $profile_pic_filename = null;
                        $temp_file_path = null;
                    }
                }
                
                // Store in session temporarily for verification
                $_SESSION['temp_name'] = $name;
                $_SESSION['temp_email'] = $email;
                $_SESSION['temp_password'] = $password;
                $_SESSION['temp_profile_pic'] = $profile_pic_filename;
                $_SESSION['temp_file_path'] = $temp_file_path;
                $_SESSION['verification_code'] = $verification_code;
                
                // Send verification email
                error_log("Attempting to send email to: " . $email);
                $email_sent = sendVerificationEmail($email, $name, $verification_code);
                error_log("Email sent result: " . ($email_sent ? 'true' : 'false'));
                
                if($email_sent) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Verification email sent successfully'
                        // Removed verification_code from response - will be sent via email
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to send verification email. Please try again later.']);
                }
                exit();
            } catch (Exception $e) {
                error_log("Registration error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
                exit();
            }
        } else {
            // Original form submission (fallback for non-AJAX)
            $name=$_POST["name"];
            $email=$_POST["email"];
            $password=$_POST["password"];
            
            // Handle profile picture upload for fallback
            $profile_pic_filename = null;
            if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $file_type = $_FILES['profile_pic']['type'];
                $file_size = $_FILES['profile_pic']['size'];
                
                if (in_array($file_type, $allowed_types) && $file_size <= 5 * 1024 * 1024) {
                    $email_part = explode('@', $email)[0];
                    $file_extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
                    $profile_pic_filename = $email_part . 'PIC.' . $file_extension;
                    
                    $upload_path = 'uploads/' . $profile_pic_filename;
                    if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_path)) {
                        $profile_pic_filename = null;
                    }
                }
            }

            $stmt=$conn->prepare("INSERT INTO userinfo (name, email, pass, profile_pic) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss",$name,$email,$password,$profile_pic_filename);
            
            if($stmt->execute()){
                // Create session for the newly registered user
                $_SESSION["username"] = $name;
                $_SESSION["email"] = $email;
                
                // Store session ID in database (same as login.php)
                session_regenerate_id(true);
                $session_id = session_id();
                $current_time = date('Y-m-d H:i:s');
                
                // Create session history entry
                $session_array = [[
                    'session_id' => $session_id,
                    'login_time' => $current_time,
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]];
                
                $update_stmt = $conn->prepare("UPDATE userinfo SET sessionids = ? WHERE email = ?");
                $json_sessions = json_encode($session_array);
                $update_stmt->bind_param("ss", $json_sessions, $email);
                $update_stmt->execute();
                $update_stmt->close();
                
                header("Location: dashboard.php");
                exit();
            }else{
                $err="Error in registration. Please try again.";
            }

            $stmt->close();
        }
    }


    ?>



    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Registration page</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
        <style>
            .error-message { color: #dc3545; font-size: 14px; margin-top: 5px; }
            .success-message { color: #28a745; font-size: 14px; margin-top: 5px; }
            #verification-section { display: none; }
            
            /* Dark Theme Variables */
            :root {
                --bg-color: #ffffff;
                --text-color: #212529;
                --card-bg: #ffffff;
                --input-bg: #ffffff;
                --input-border: #ced4da;
                --btn-primary: #0d6efd;
                --btn-outline: #6c757d;
                --link-color: #0d6efd;
                --border-color: #dee2e6;
            }

            [data-theme="dark"] {
                --bg-color: #121212;
                --text-color: #ffffff;
                --card-bg: #1e1e1e;
                --input-bg: #2d2d2d;
                --input-border: #404040;
                --btn-primary: #0d6efd;
                --btn-outline: #6c757d;
                --link-color: #66b3ff;
                --border-color: #404040;
            }

            /* Apply theme variables */
            body {
                background-color: var(--bg-color);
                color: var(--text-color);
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            .form-control {
                background-color: var(--input-bg);
                border-color: var(--input-border);
                color: var(--text-color);
            }

            .form-control:focus {
                background-color: var(--input-bg);
                border-color: var(--btn-primary);
                color: var(--text-color);
                box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            }

            .form-control::placeholder {
                color: var(--text-color);
                opacity: 0.6;
            }

            a {
                color: var(--link-color);
            }

            a:hover {
                color: var(--link-color);
                opacity: 0.8;
            }

            /* Dark theme toggle button */
            .theme-toggle {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1000;
                border: none;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: var(--card-bg);
                color: var(--text-color);
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .theme-toggle:hover {
                transform: scale(1.1);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }

            [data-theme="dark"] .theme-toggle {
                box-shadow: 0 2px 10px rgba(255, 255, 255, 0.1);
            }

            [data-theme="dark"] .theme-toggle:hover {
                box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
            }

            /* Dark theme for error/success messages */
            [data-theme="dark"] .error-message {
                color: #ff6b6b;
            }

            [data-theme="dark"] .success-message {
                color: #51cf66;
            }

            /* Dark theme for form sections */
            .form-section {
                background-color: var(--card-bg);
                border-radius: 10px;
                padding: 30px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                border: 1px solid var(--border-color);
            }

            [data-theme="dark"] .form-section {
                box-shadow: 0 4px 15px rgba(255, 255, 255, 0.05);
            }
        </style>
    </head>
    <body>
        <!-- Dark Theme Toggle Button -->
        <button class="theme-toggle" id="themeToggle" title="Toggle Dark Mode">
            <i class="bi bi-moon-fill" id="themeIcon"></i>
        </button>

        <!-- <h1>Hello, world!</h1> -->

        <!-- Registration Form -->
        <div id="registration-section">
            <div class="form-section" style="max-width: 500px; margin: 100px auto 0;">
                <form id="registrationForm" method="post" enctype="multipart/form-data">
                    <h2 class="text-center mb-4">Create Account</h2>
                    
                    <div class="form-group">
                        <label for="name" class="mb-3">Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter name" name="name" required>
                        <div id="nameError" class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="mb-3">Email address</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" required>
                        <div id="emailError" class="error-message"></div>
                        <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
                    </div>
                    <div class="form-group mt-3">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
                        <div id="passwordError" class="error-message"></div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" placeholder="Confirm Password" name="confirm_password" required>
                        <div id="confirmPasswordError" class="error-message"></div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="profile_pic">Profile Picture</label>
                        <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*">
                        <small class="form-text text-muted">Choose a profile picture (optional). Supported formats: JPG, PNG, GIF</small>
                        <div id="profilePicError" class="error-message"></div>
                    </div>
                    <!-- <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">Check me out</label>
                    </div> -->
                    
                    <?php
                    if($err!==null){
                        echo "<p class='text-danger mt-3'>$err</p>";
                    }
                    ?>  
                    
                    <div id="registrationMessage" class="mt-3"></div>
                    
                    <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>

                    <button type="submit" class="btn btn-primary mt-3" id="submitBtn">Submit</button>
                </form>
            </div>
        </div>

        <!-- Email Verification Section -->
        <div id="verification-section">
            <div class="form-section" style="max-width: 500px; margin: 100px auto 0;">
                <h2 class="text-center mb-4">Verify Your Email</h2>
                <p class="text-center">We've sent a verification code to your email address. Please enter it below:</p>
                
                <form id="verificationForm">
                    <div class="form-group mt-3">
                        <label for="verification_code">Verification Code</label>
                        <input type="text" class="form-control" id="verification_code" placeholder="Enter 6-digit code" maxlength="6" required>
                        <div id="verificationError" class="error-message"></div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary mt-3 w-100" id="verifyBtn">Verify Email</button>
                    <button type="button" class="btn btn-outline-secondary mt-2 w-100" id="resendBtn">Resend Code</button>
                    
                    <div id="verificationMessage" class="mt-3"></div>
                </form>
            </div>
        </div>
            



            <script>
                // Dark Theme Toggle Functionality
                const themeToggle = document.getElementById('themeToggle');
                const themeIcon = document.getElementById('themeIcon');
                const body = document.body;

                // Load saved theme or default to light
                const savedTheme = localStorage.getItem('theme') || 'light';
                setTheme(savedTheme);

                // Theme toggle event listener
                themeToggle.addEventListener('click', () => {
                    const currentTheme = body.getAttribute('data-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    setTheme(newTheme);
                    localStorage.setItem('theme', newTheme);
                });

                function setTheme(theme) {
                    body.setAttribute('data-theme', theme);
                    
                    if (theme === 'dark') {
                        themeIcon.className = 'bi bi-sun-fill';
                        themeToggle.title = 'Switch to Light Mode';
                    } else {
                        themeIcon.className = 'bi bi-moon-fill';
                        themeToggle.title = 'Switch to Dark Mode';
                    }
                }

                // Email verification functionality
                document.addEventListener('DOMContentLoaded', function() {
                    const registrationForm = document.getElementById('registrationForm');
                    const verificationForm = document.getElementById('verificationForm');
                    const submitBtn = document.getElementById('submitBtn');
                    const verifyBtn = document.getElementById('verifyBtn');
                    const resendBtn = document.getElementById('resendBtn');

                    // Handle registration form submission
                    registrationForm.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        clearErrors();
                        
                        const name = document.getElementById('name').value;
                        const email = document.getElementById('email').value;
                        const password = document.getElementById('password').value;
                        const confirmPassword = document.getElementById('confirm_password').value;

                        // Basic validation
                        if (!name.trim()) {
                            document.getElementById('nameError').textContent = 'Name is required';
                            return;
                        }

                        if (!email.trim()) {
                            document.getElementById('emailError').textContent = 'Email is required';
                            return;
                        }

                        if (password.length < 6) {
                            document.getElementById('passwordError').textContent = 'Password must be at least 6 characters';
                            return;
                        }

                        if (password !== confirmPassword) {
                            document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
                            return;
                        }

                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Sending verification email...';

                        try {
                            // Submit the form data to PHP
                            const formData = new FormData();
                            formData.append('name', name);
                            formData.append('email', email);
                            formData.append('password', password);
                            formData.append('confirm_password', confirmPassword);
                            formData.append('step', 'register');
                            
                            // Add profile picture file if selected
                            const profilePicInput = document.getElementById('profile_pic');
                            if (profilePicInput.files.length > 0) {
                                formData.append('profile_pic', profilePicInput.files[0]);
                            }
                            
                            console.log('Sending registration data:', {
                                name: name,
                                email: email,
                                password: '***',
                                step: 'register',
                                profile_pic: profilePicInput.files.length > 0 ? 'File selected' : 'No file'
                            });
                            
                            const response = await fetch('reg.php', {
                                method: 'POST',
                                body: formData
                            });

                            console.log('Response status:', response.status);
                            console.log('Response headers:', response.headers);
                            
                            const responseText = await response.text();
                            console.log('Raw response:', responseText);
                            
                            let result;
                            try {
                                result = JSON.parse(responseText);
                            } catch (parseError) {
                                console.error('JSON parse error:', parseError);
                                console.error('Response was not JSON:', responseText);
                                throw new Error('Server returned invalid response: ' + responseText.substring(0, 100));
                            }
                            
                            console.log('Parsed result:', result);

                            if (result.success) {
                                // Show verification section
                                document.getElementById('registration-section').style.display = 'none';
                                document.getElementById('verification-section').style.display = 'block';
                                showMessage('verificationMessage', 'Verification code sent to your email! Please check your inbox and spam folder.', 'success');
                            } else {
                                showMessage('registrationMessage', result.message || 'Failed to send verification email. Please try again.', 'error');
                            }

                        } catch (error) {
                            console.error('Error processing registration:', error);
                            showMessage('registrationMessage', 'Error: ' + error.message, 'error');
                        } finally {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Submit';
                        }
                    });

                    // Handle verification form submission
                    verificationForm.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        
                        const enteredCode = document.getElementById('verification_code').value;
                        
                        if (!enteredCode.trim()) {
                            document.getElementById('verificationError').textContent = 'Please enter the verification code';
                            return;
                        }

                        verifyBtn.disabled = true;
                        verifyBtn.textContent = 'Verifying...';

                        try {
                            // Submit verification code to PHP
                            const formData = new FormData();
                            formData.append('step', 'verify');
                            formData.append('verification_code', enteredCode);
                            
                            const response = await fetch('reg.php', {
                                method: 'POST',
                                body: formData
                            });

                            const result = await response.json();
                            console.log('Verification response:', result);

                            if (result.success) {
                                showMessage('verificationMessage', 'Email verified! Account created successfully. Redirecting to dashboard...', 'success');
                                setTimeout(() => {
                                    if (result.redirect) {
                                        window.location.href = result.redirect;
                                    } else {
                                        window.location.href = 'login.php';
                                    }
                                }, 2000);
                            } else {
                                document.getElementById('verificationError').textContent = result.message || 'Invalid verification code';
                            }
                        } catch (error) {
                            console.error('Verification error:', error);
                            document.getElementById('verificationError').textContent = 'Verification failed. Please try again.';
                        } finally {
                            verifyBtn.disabled = false;
                            verifyBtn.textContent = 'Verify Email';
                        }
                    });

                    // Handle resend code (placeholder - you can implement this)
                    resendBtn.addEventListener('click', function() {
                        showMessage('verificationMessage', 'Please try registering again for a new code.', 'error');
                    });

                    function clearErrors() {
                        document.getElementById('nameError').textContent = '';
                        document.getElementById('emailError').textContent = '';
                        document.getElementById('passwordError').textContent = '';
                        document.getElementById('confirmPasswordError').textContent = '';
                        document.getElementById('verificationError').textContent = '';
                        document.getElementById('registrationMessage').innerHTML = '';
                    }

                    function showMessage(elementId, message, type) {
                        const element = document.getElementById(elementId);
                        element.innerHTML = `<div class="${type}-message">${message}</div>`;
                    }
                });
            </script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </body>
    </html>



