<?php
/**
 * verifyLogin.php verify user login
 *
 * @package   VerifyCoupon
 * @category  VerifyCoupon
 * @author    everpointer zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-3-12 下午3:31
 */

/**
 * VerifyLogin class to check login
 */
require_once('../common/CodeMessages.php');
require_once('../common/Exception.php');
require_once('../verifyCoupon/functions.php');
require_once('../common/class/MyTable.php');

class VerifyLogin
{
    const USERTABLE = 'partner';

    private $_mysql = null;
    /**
     * Constructor.
     *
     * @param string     
     * @return  
     * @throw Exception when fail to connect or select the database
     */
    public function __construct()
    {
        $this->_mysql = new MyTable();
    }

    // get mysql (MyTable Object)
    public function get_mysql()
    {
        return $this->_mysql;
    }


    /**
    * verify_login check username and password
    *
    * @param    string $username username
    * @return   bool true if use exists and password correct
    * @throws   Exception
    */
    public function verify_login($username, $password)
    {
        $user = mysql_real_escape_string($username);
        $pass = mysql_real_escape_string($password);
        if (empty($user) || empty($pass) )
        {
            throw new Exception(
                VerifyLoginCodeMsg::get_message(VerifyLoginCodeMsg::INVALID_NAME_PASS),
                VerifyLoginCodeMsg::INVALID_NAME_PASS
            );
        }
        $pass = $this->crypt_pass($pass);
        $query = sprintf("SELECT * FROM %s WHERE username='%s' AND password='%s' LIMIT 1",
                    self::USERTABLE, $user, $pass);

        $result = mysql_query($query);

        if (mysql_num_rows($result) == 1)
        {
            $row = mysql_fetch_array($result);
            session_start();
            $partnerId = $row['id'];
            $_SESSION['partnerId'] = $partnerId;
            $_SESSION['username'] = $row['username'];
            $_SESSION['title'] = $row['title'];
            $_SESSION['subbranch_matters'] = $row['subbranch_matters'];

            // 进行平台账户登陆
            $this->do_loginPlatform($partnerId);
        } else {
            throw new Exception(
                VerifyLoginCodeMsg::get_message(VerifyLoginCodeMsg::ERROR_NAME_PASS),
                VerifyLoginCodeMsg::ERROR_NAME_PASS);
        }
    }

    //进行平台账户的登陆
    private function do_loginPlatform($partnerId)
    {
        $query = sprintf("SELECT * FROM %s WHERE partner_id=%s AND status=1",
                    'partner_platforms', $partnerId);
        $result = mysql_query($query);
        while($row = mysql_fetch_array($result))
        {
            if ($row['platform_key'] === 'juhuasuan')
            {
                //聚划算登录可能会发生失败，只尝试10次
                $tryTimes = 10;
                $count = 0;
                while(!login_platform('juhuasuan'))
                {
                   if (++$count === 10 ) break;
                }
            }
        }
    }
    
    /**
    * crypt_pass cript the password
    *
    * @param    string $pass password to be cripted
    * @return   string cripted password
    * @throws    
    */
    private function crypt_pass($pass)
    {
        if (empty($pass))
            return false;
        $salt = substr(md5($pass), 0, 2);
        return md5(crypt($pass, $salt));
    }

    //public function __destruct()
    //{
        ////if (is_resource($this->_dbconn))
            ////mysql_close($this->_dbconn);
    //}
    
    /**
    * genenrate response for clients in json stype
    *
    * @param    int|Exception code or exception to response
    *
    * @return   string response in json
    *
    * @throws    
    */
    static public function gen_response_json($code)
    {
        // success or fail
        $msg = VerifyLoginCodeMsg::get_message($code);

        // gen_response_json exception
        if (empty($msg))
        {
            $code =  CommonCodeMsg::INVALID_ARGUMENT_ERROR;
            $msg  =  CommonCodeMsg::get_message($code);  
        }

        if ($code === VerifyLoginCodeMsg::VERIFY_LOGIN_SUCCESS)
        {
            $response['success'] = true;
        }
        else {
            $response['success'] = false;
        }
        $response['code'] = $code;
        $response['msg']  = $msg;
        $response['dateTime'] = date("Y-m-d, h:i:s");

        return json_encode($response);
    }
}
?>
