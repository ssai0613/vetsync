<?php
session_start();

require_once "../db/database.php";
require_once "../models/User.php";

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_un = $_POST['user_un'];
    $user_pass = $_POST['user_pass'];

    $found = $user->login($user_un, $user_pass);
    if ($found) {
        $_SESSION['user_id'] = $found['user_id'];
        $_SESSION['user_role'] = $found['user_role'];

        // Redirect based on user role
        if ($found['user_role'] === 'admin') {
            header("Location: ../views/admin/dashboard.php");
        } elseif ($found['user_role'] === 'staff') {
            header("Location: ../views/staff/staff_dashboard.php");
        } else {
            // Optional fallback if role is unexpected
            header("Location: ../views/login.php?error=unknownrole");
        }
        exit();
    } else {
        header("Location: ../views/login.php?error=invalid");
        exit();
    }
}