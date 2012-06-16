<?php
require_once 'common.php';
require_once 'functions.php';

if (!isset($_REQUEST['username']) || $_REQUEST['username'] === "" ||
    !isset($_REQUEST['password']) || $_REQUEST['password'] === "")
{
    echo gen_failResp("请输入您的用户名和密码");
    exit;
}
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];

if (staff_login($username, $password))
{
    echo gen_successResp();
} else {
    echo gen_failResp("用户名和密码不匹配");
}
?>
