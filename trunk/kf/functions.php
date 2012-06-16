<?php
require_once 'common.php';

function staff_login($username, $password)
{
    $staff_table_name = "staff";
    $mysql = new MyTable($staff_table_name);
    $username = mysql_real_escape_string($username);
    $password = mysql_real_escape_string($password);

    $cond = array(
        "username" => $username,
        "password" => crypt_pass($password)
    );
    $staff_row = $mysql->get_row($cond);
    if ($staff_row !== null)
    {
        session_start();
        $_SESSION['is_staff'] = true;
        $_SESSION['staff_info'] = array(
            "username" => $staff_row['username'],
            "nickname" => $staff_row['nickname'],
            "role" => $staff_row['role']
        );
        return true;
    } else {
        return false;
    }
}

function is_staff_login()
{
    session_start();
    if (isset($_SESSION['is_staff']) && $_SESSION['is_staff'] === true && isset($_SESSION['staff_info']))
    {
        return true;
    } else {
        return false;
    }
}
?>
