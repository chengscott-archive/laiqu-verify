<?php
/**
 * MyTable.php base class for table manipulation
 *
 * @package   VerifyCoupon
 * @category  Tabel
 * @author    everpointer zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-3-14 上午10:44
 */
require_once('../common/CodeMessages.php');
require_once('../common/Exception.php');

class MyTable
{
    /* database constants */
    const DBHOST = 'localhost';
    const DBUSER = 'root';
    const DBPASS = 'ghz86377328';
    const DBDATABASE = 'lq_verify';
    
    protected $_dbconn = null;
    /**
     * Constructor.
     *
     * @param string     
     * @return  
     */
    public function __construct()
    {
        $this->assure_dbConnection();
    }

    protected function assure_dbConnection()
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
        mysql_query("SET CHARACTER SET 'utf8'", $this->_dbconn);
    }

    protected function get_dbTableName($tableName)
    {
        if ($tableName !== "")
            $tableName = self::DBDATABASE.".".$tableName;

        return $tableName;
    }
 
}

?>
