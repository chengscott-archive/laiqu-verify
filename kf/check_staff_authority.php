<?php
require_once 'functions.php';
if (is_staff_login() !== true)
{
    header('Location: login.html');
    exit;
}
?>
