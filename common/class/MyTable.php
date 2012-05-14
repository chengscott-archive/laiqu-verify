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
    
    protected $_tablename = null;
    protected $_dbconn = null;
    /**
     * Constructor.
     *
     * @param string     
     * @return  
     */
    public function __construct($tableName = "")
    {
        if ($tableName !== "")
            $this->_tablename = $tableName;
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

    public function get_dbTableName($tableName)
    {
        if ($tableName !== "")
            $tableName = self::DBDATABASE.".".$tableName;

        return $tableName;
    }
    // set tableName
    public function set_tablename($tableName)
    {
        $this->_tablename = $tableName;
    }

    // todo: 
    public function get_row($conditions)
    {
        $this->assure_dbConnection();

        $selectSql = "SELECT * FROM ".$this->get_dbTableName($this->_tablename)." WHERE 1=1 ";
        foreach ($conditions as $key => $value)
        {
            $key = mysql_real_escape_string($key);
            $value = mysql_real_escape_string($value);

            if (is_string($value))
            {
                $value = "'".$value."'";
            }
            $selectSql .= "AND ".$key."=".$value;
        }
        $result = mysql_query($selectSql);
        if ($result && mysql_num_rows($result) > 0)
        {
            return mysql_fetch_array($result);
        }
        else {
            return null;
        }
    }
    public function query($sql)
    {
        $this->assure_dbConnection();
        return mysql_query($sql);
    }
 
}

?>
