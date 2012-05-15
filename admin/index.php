<?php
require '../common/common.php';
require_once '../common/functions/common.funcs.php';
// check authority
$ip = get_client_ip();
// ::1 means localhost
if ($ip !== "::1" && $ip !== LAIQU_OFFICE_IP)
{
    echo 'Access denied!';
    exit;
}
?>
