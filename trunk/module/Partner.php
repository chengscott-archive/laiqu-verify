<?php
/**
 * Partner.php  class for handling table partner
 *
 * @package   verifyCoupon
 * @category  Partner
 * @author    everpointer  zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-3-16 下午3:35
 */
require_once('../common/Exception.php');
require_once('../common/class/MyTable.php');
require_once('../common/common.php');

class Partner extends MyTable
{
    /* varibale defination */
    const PARTNER_TABLE = 'partner';
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * change a partner's password
    *
    * @param    int  $partnerId  specify a partner
    * @return   bool  true if password is changed
    * @throws   Exception
    */
    public function changePass($partnerId, $originPasswd, $newPasswd)
    {
        $this->assure_dbConnection();
        $originPasswd = mysql_real_escape_string($originPasswd);
        $newPasswd = mysql_real_escape_string($newPasswd);
        if (strlen($newPasswd) < 6 || strlen($newPasswd) > 16)
        {
            throw new Exception(
                ChangePassCodeMsg::get_message(ChangePassCodeMsg::NEW_PASS_NOT_ALLOWED_ERROR),
                ChangePassCodeMsg::NEW_PASS_NOT_ALLOWED_ERROR);
        }
        if (!empty($originPasswd))
        {
            $originPasswd = crypt_pass($originPasswd);
            $selectSql = "SELECT * FROM ".$this->get_dbTableName(self::PARTNER_TABLE)." WHERE id=".$partnerId.
                         " AND password='".$originPasswd."'";
            $result = mysql_query($selectSql);
            if ($result && mysql_num_rows($result) == 1)
            {
                $updateSql = "UPDATE ".$this->get_dbTableName(self::PARTNER_TABLE)." SET";
                $updateSql .= " password='".crypt_pass($newPasswd)."'"." WHERE id=".$partnerId;
                return mysql_query($updateSql)? LQ_OK: LQ_FAIL;
            }
        }
        // if come here means the original password is wrong 
        throw new Exception(
            ChangePassCodeMsg::get_message(ChangePassCodeMsg::ORIGIN_PASS_NOT_MATCH),
            ChangePassCodeMsg::ORIGIN_PASS_NOT_MATCH);
    }
 }
 
?>
