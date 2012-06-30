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

    public function get_select_result($conditions, $start = 0, $offset = -1, $tableName = "")
    {
        $this->assure_dbConnection();

        $selectSql = $this->get_select_sql($conditions,$start, $offset, $tableName);
        $result = mysql_query($selectSql);
        return $result;
    }

    public function get_select_rows($conditions, $start = 0, $offset = -1, $tableName = "")
    {
        $this->assure_dbConnection();

        $selectSql = $this->get_select_sql($conditions,$start, $offset, $tableName);
        $result = mysql_query($selectSql);

        $rows = array();
        while($orderRow = mysql_fetch_assoc($result))
        {
            array_push($rows, $orderRow);
        }
        return $rows;
    }
    public function get_select_nums($conditions, $tableName = "")
    {
        $nums = 0;
        $this->assure_dbConnection();
        $selectSql = $this->get_select_sql($conditions, 0, -1, $tableName);
        $selectSql = str_replace('*', 'count(*) as nums', $selectSql);
        $result = mysql_query($selectSql);
        $row = mysql_fetch_assoc($result);

        if ($row)
        {
            $nums = $row['nums'];
        }
        return $nums;
    }

    public function get_select_sql($conditions, $start, $offset,$tableName = "")
    {
        if ($tableName === "" && $this->_tablename === "")
        {
            return "";
        } else if ($tableName === "")
        {
            $tableName = $this->_tablename;
        }
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
        if ($start > 0 && $offset === -1) {
            $selectSql .= " LIMIT $start";
        }
        elseif ($start >= 0 && $offset > 0)
        {
           $selectSql .= " LIMIT $start,$offset"; 
        }
        return $selectSql;
    }
    // todo: 
    public function get_row($conditions, $tableName = "")
    {
        $this->assure_dbConnection();

        $result = $this->get_select_result($conditions, $tableName);
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
