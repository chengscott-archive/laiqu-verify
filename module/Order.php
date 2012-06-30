<?php
/**
 * Order.php module for table coupon
 *
 * @package   module
 * @category  Order
 * @author    everpointer zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-3-14 上午10:30
 */
require_once('../common/class/MyTable.php');
require_once('../common/Exception.php');
require_once('../common/CodeMessages.php');

class Order extends MyTable
{
    /* table constants */
    const COUPON_TABLE = 'coupon';    
    const TEAM_TABLE  = 'team';
    const ORDER_TABLE  = 'order';
    const USER_TABLE  = 'user';
    
    /**
     * Constructor.
     *
     * @param string     
     * @return  
     */
    public function __construct()
    {
        parent::__construct(self::ORDER_TABLE); 
    }

    /**
    * insert into order table order info
    *
    * @param    array  $orderInfo  order info
    * @return   array  if true return order array, else return null
    * @throws   THROW-TYPE
    */
    public function insert_order($orderInfo)
    {
        $this->assure_dbConnection();
        $insertSql = "INSERT INTO ".$this->get_dbTableName(self::ORDER_TABLE);
        $keys = "(";
        $values = " VALUES(";
        foreach($orderInfo as $key => $value)
        {
            $keys .= $key . ",";
            // 偷懒，对now()特殊处理，不加引号
            if (is_string($values))
            {
                $values .= "'" . $value . "'" . ",";
            }
            else
            {
                $values .=  $value. ",";
            } 
            
            
        }

        $keys = substr($keys, 0, -1) . ")";
        $values = substr($values, 0, -1) . ")";

        $insertSql .= $keys . $values;
        $result = mysql_query($insertSql);
        if (!$result) {
            return null;
        }
        $insertedOrderId = mysql_insert_id();
        $orderInfo['id'] = $insertedOrderId;

        return $orderInfo;
    }

    public function get_consumedCoupontimes($searchFields)
    {
        $this->assure_dbConnection();
        $sql = $this->gen_couponSearchSql($searchFields);

        $consumedtimes = 0;
        $result = mysql_query($sql);
        while($couponRow = mysql_fetch_assoc($result))
        {
            $consumedtimes += $couponRow['consumeTimes'];
        }
        return $consumedtimes;
    }    

}
?>
