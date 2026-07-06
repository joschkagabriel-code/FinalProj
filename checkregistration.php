<?php
session_start();
require_once('db.php');

$errors = array();

if (isset($_POST['submit'])) {

    $full_name       = $_POST['full_name'];
    $email           = $_POST['email'];
    $password        = $_POST['password'];
    $confirmpassword = $_POST['confirm_password'];
    $address         = $_POST['address'];
    $contact_number  = $_POST['contact_number'];

    $_SESSION['form_data'] = $_POST;

    if (empty($full_name))                                $errors[] = "Complete name is required.";
    if (!checkemail($email))                              $errors[] = "Email address is not valid.";
    if (!checkpassword($password))                        $errors[] = "Password must be at least 8 characters long.";
    if (!checkconfirmpassword($password, $confirmpassword)) $errors[] = "Passwords do not match.";
    if (empty($address))                                  $errors[] = "Complete address is required.";
    if (empty($contact_number))                           $errors[] = "Contact number is required.";

    if (empty($errors)) {
        $email_esc    = mysqli_real_escape_string($conn, $email);
        $check_result = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email_esc' LIMIT 1");
        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = "An account with that email address already exists.";
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: registration.php');
        exit();
    }

    $full_name_esc      = mysqli_real_escape_string($conn, $full_name);
    $email_esc          = mysqli_real_escape_string($conn, $email);
    $address_esc        = mysqli_real_escape_string($conn, $address);
    $contact_number_esc = mysqli_real_escape_string($conn, $contact_number);
    $password_hash      = password_hash($password, PASSWORD_DEFAULT);
    $token              = bin2hex(random_bytes(24));

    $sql = "INSERT INTO users (full_name, email, password_hash, address, contact_number, role, is_verified, verify_token)
            VALUES ('$full_name_esc','$email_esc','$password_hash','$address_esc','$contact_number_esc','buyer',0,'$token')";

    if (mysqli_query($conn, $sql)) {
        $new_user_id = mysqli_insert_id($conn);

        $desc_esc  = mysqli_real_escape_string($conn, "$full_name_esc registered a new buyer account.");
        mysqli_query($conn, "INSERT INTO audit_log (user_id, actor_name, actor_role, action, description)
                             VALUES ($new_user_id, '$full_name_esc', 'buyer', 'REGISTER', '$desc_esc')");

        $verify_link = (isset($_SERVER['HTTPS']) ? "https://" : "http://")
                       . $_SERVER['HTTP_HOST']
                       . dirname($_SERVER['PHP_SELF'])
                       . "/verify.php?token=$token";

        $sent = sendVerificationEmail($email, $full_name, $verify_link);
        if (!$sent) {
            $_SESSION['verify_link'] = $verify_link;
        }

        unset($_SESSION['form_data']);
        $_SESSION['success'] = "Registration successful! Please check your email to verify your account before logging in.";
        header('Location: login.php');
        exit();

    } else {
        $_SESSION['errors'] = array("Registration error: " . mysqli_error($conn));
        header('Location: registration.php');
        exit();
    }
}

mysqli_close($conn);

function checkemail($email) {
    return preg_match("/^[a-zA-Z]+[a-zA-Z0-9\._]*@[a-zA-Z0-9\._]+\.[a-zA-Z]{2,8}$/", $email);
}
function checkpassword($password) {
    return strlen($password) >= 8;
}
function checkconfirmpassword($password, $confirmpassword) {
    return $password === $confirmpassword;
}

function sendVerificationEmail($toEmail, $toName, $verifyLink) {
    $subject = "Confirm your ChairHive account";
    $body = "
    <div style='font-family:Inter,Arial,sans-serif;max-width:480px;margin:auto;padding:32px;border:1px solid #E2E8F0;border-radius:12px;'>
        <h2 style='color:#0E7490;margin:0 0 8px;'>Welcome to ChairHive!</h2>
        <p style='color:#64748B;'>Hi " . htmlspecialchars($toName) . ", thanks for registering. Click the button below to verify your email address and activate your account.</p>
        <p style='text-align:center;margin:28px 0;'>
            <a href='" . htmlspecialchars($verifyLink) . "' style='background:#0E7490;color:#fff;padding:13px 32px;border-radius:8px;text-decoration:none;font-weight:600;font-family:Inter,Arial,sans-serif;'>
                Confirm My Email
            </a>
        </p>
        <p style='color:#94A3B8;font-size:0.82rem;'>Or copy this link: <a href='" . htmlspecialchars($verifyLink) . "' style='color:#0E7490;'>" . htmlspecialchars($verifyLink) . "</a></p>
        <hr style='border:none;border-top:1px solid #E2E8F0;margin:24px 0;'>
        <p style='color:#CBD5E1;font-size:0.75rem;'>Educational purposes only &mdash; school project.</p>
    </div>";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: ChairHive <no-reply@chairhive.test>\r\n";

    $sent = @mail($toEmail, $subject, $body, $headers);
    if (!$sent) {
        $dir = __DIR__ . "/include/sent_emails";
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        file_put_contents($dir . "/" . time() . "_" . preg_replace('/[^a-z0-9]+/i', '_', $toEmail) . ".html", $body);
    }
    return $sent;
}
?>
