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

class VerifyLogin
{
    /* constants for database */
    const DBHOST = 'localhost';
    const DBUSER = 'root';
    const DBPASS = 'ghz86377328';
    const DBDATABASE = 'lq_verify';
    const USERTABLE = 'partner';

    private $_dbconn = null;
    /**
     * Constructor.
     *
     * @param string     
     * @return  
     * @throw Exception when fail to connect or select the database
     */
    public function __construct()
    {
        $this->_dbconn = mysql_connect(self::DBHOST, self::DBUSER, self::DBPASS);
        if (!$this->_dbconn)
        {
            throw new Exception(
                VerifyLoginCodeMsg::get_message(VerifyLoginCodeMsg::MYSQL_CONNECT_ERROR),
                VerifyLoginCodeMsg::MYSQL_CONNECT_ERROR);
        }
        if (!mysql_select_db(self::DBDATABASE, $this->_dbconn))
        {
            throw new Exception(
                VerifyLoginCodeMsg::get_message(VerifyLoginCodeMsg::MYSQL_SELECT_DB_ERROR),
                VerifyLoginCodeMsg::MYSQL_SELECT_DB_ERROR);
        }
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
            $_SESSION['partnerId'] = $row['id'];
            $_SESSION['username']  = $row['username'];
            $_SESSION['title']     = $row['title'];
        } else {
            throw new Exception(
                VerifyLoginCodeMsg::get_message(VerifyLoginCodeMsg::ERROR_NAME_PASS),
                VerifyLoginCodeMsg::ERROR_NAME_PASS);
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

        $response['code'] = $code;
        $response['msg']  = $msg;
        $response['dateTime'] = date("Y-m-d, h:i:s");

        return json_encode($response);
    }
}
?>
