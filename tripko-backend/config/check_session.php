<?php
session_start();

function checkAdminSession() {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type_id'] != 1) {
        header("Location: ../file_html/SignUp_LogIn_Form.php");
        exit();
    }
}
?>