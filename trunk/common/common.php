<?php
require_once('constants.php');
require_once('utilities.php');
/**
 * page directing
 **/
function redirect($url) {
    if(!empty($url))
    {
        header("Location: {$url}");
    }
    exit;
}

/**
 * check whether a partner is login
 **/
function is_partner_login()
{
    session_start();
    if (isset($_SESSION['partnerId']))
        return ($_SESSION['partnerId'] > 0);
    return false;
}

/** 
* crypt_pass cript the password 
* 
* @param    string $pass password to be cripted 
* @return   string cripted password 
* @throws     
*/ 
function crypt_pass($pass) 
{ 
    if (empty($pass)) 
        return false; 
    $salt = substr(md5($pass), 0, 2); 
    return md5(crypt($pass, $salt)); 
} 

/**
* get_pageNavHtml get page bar by current page and total pages
*
* @param    int  $page  current page
* @param    int  $totalPage  total pages
* @param    string  $urlPrefix  page url prefix
* @param    int  $showPagesNums  numbers of page to show, must be a odd num and >=3 
* @return   string  designed page nav html, return "" showPagesNums is even and < 3
* @throws    
*/
function get_pageNavHtml($page, $totalPages, $urlPrefix, $showPagesNums = 11)
{
    if ($totalPages <= 0 || $page <= 0)
        return "";
    if ($showPagesNums <3 || $showPagesNums%2 === 0)
        return "";
    $pageNavHtml = "";

    $sideShowPages = floor($showPagesNums/2);
    $start = ($page - $sideShowPages) > 1? $page-$sideShowPages : 1;
    $end = ($page + $sideShowPages) < $totalPages? $page+$sideShowPages : $totalPages;

    for ($i = $start; $i<=$end; $i++)
    {
        $eachPageUrl = $urlPrefix."page=".$i;
        $pageNavHtml .= "<a href='$eachPageUrl' id='page_num_$i'>$i</a>&nbsp";
    }
    return $pageNavHtml;
}
?>
