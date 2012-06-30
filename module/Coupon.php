<?php
/**
 * Coupon.php module for table coupon
 *
 * @package   VerifyCoupon
 * @category  Coupon
 * @author    everpointer zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-3-14 上午10:30
 */
require_once('../common/class/MyTable.php');
require_once('../common/Exception.php');
require_once('../common/CodeMessages.php');

class Coupon extends MyTable
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
        parent::__construct(self::COUPON_TABLE); 
    }
    /**
    * query consumed coupons according to partner id and team id
    *
    * @param  int  $partnedId  
    * @param  int  $teamId 
    * @param  int  $start  start position 
    * @param  int  $partnedId  num of coupon to fetch
    * @return  array consumed coupon list     
    * @throws   
    */
    public function get_consumedCoupons($searchFields, $start, $offset)
    {
        if ($start < 0) $start = 0;
        if ($offset <= 0) $offset = 6;

        $sql = $this->gen_couponSearchSql($searchFields, $start, $offset);

        $result = mysql_query($sql);
        if (mysql_num_rows($result) < 1)
            return array();
        else 
            return $this->parse_couponList($result);
    }

    /**
    * 查询当前符合条件的已消费券次数（不是券数，而是次数）
    *
    * @param           
    * @return   int  length of consumed coupons in database
    * @throws    
    */
    public function get_consumedCoupontimes($searchFields)
    {
        $this->assure_dbConnection();
        $result = $this->get_select_result($searchFields);

        $consumedtimes = 0;
        while($couponRow = mysql_fetch_assoc($result))
        {
            $consumedtimes += $couponRow['consume_times'];
        }
        return $consumedtimes;
    }    

    public function gen_couponSearchSql($searchFields, $start = 0, $offset = -1)
    {
        foreach ($searchFields as $fieldKey => $fieldValue)
        {
            $$fieldKey = is_string($fieldValue)?mysql_real_escape_string($fieldValue): $fieldValue;
        }

        $select = "select c.order_id as orderId, t.title as teamTitle, c.id as couponId, c.platform_order_id as platformOrderId, c.operation_type as operationType, c.taobao_id as taobaoId,";
        $select .= "u.username as username, u.email as email, o.subbranch as subbranch, c.consume_time as consumeTime,c.consume_times as consumeTimes,c.platform_coupon_id as platformCouponId,c.consumer_mobile as consumerMobile";
        $select .= ' from '.self::DBDATABASE.'.'.self::TEAM_TABLE.' t,'.self::DBDATABASE.'.'.self::ORDER_TABLE.' o,';
        $select .= self::DBDATABASE.'.'.self::COUPON_TABLE.' c left join '.self::DBDATABASE.'.'.self::USER_TABLE.' u on c.user_id=u.id';
        $sql = $select." WHERE operation_type='兑换' ";
        if (count($searchFields) > 0)
        {
            if (isset($partnerId) && $partnerId > 0)
                $sql .= " AND c.partner_id=$partnerId";
            if (isset($teamId) && $teamId > 0)
                $sql .= " AND c.team_id=$teamId ";
            if (isset($platform) && !empty($platform))
                $sql .= " AND c.platform_key='$platform'";
            if (isset($orderId) && $orderId > 0)
            {
                if ($platform === "juhuasuan"){
                    $sql .= " AND c.taobao_id=$orderId";
                }
                else {
                    $sql .= " AND c.platform_order_id=$oderId";
                }

            }
            if (isset($couponId) && $couponId > 0)
                $sql .= " AND c.platform_coupon_id=$couponId";
            if (isset($mobile) && !empty($mobile))
                $sql .= " AND c.consumer_mobile='$mobile'";
            if (isset($productId) && !empty($productId))
                $sql .= " AND c.platform_product_id='$productId'";
        }
        $sql .= " AND t.platform_record_id=c.platform_product_id AND o.platform_record_id=c.platform_order_id ";
        // group and order
        $sql .= " order by c.consume_time desc";
        if ($start > 0 && $offset === -1) {
            $sql .= " LIMIT $start";
        }
        elseif ($start >= 0 && $offset > 0)
        {
           $sql .= " LIMIT $start,$offset"; 
        }
        return $sql;
    }

    /**
    * get_consumedCouponLength
    *
    * @param           
    * @return   int  length of consumed coupons in database
    * @throws    
    */
    public function get_consumedCouponNums($searchFields)
    {
        $this->assure_dbConnection();
        $sql = $this->gen_couponSearchSql($searchFields);
        
        $couponNums = 0;
        $result = mysql_query($sql);
        $couponNums = mysql_num_rows($result);
        //if (is_resource($result) && mysql_num_rows($result) > 0)
        //{
            //$countRow = mysql_fetch_array($result);
            //$couponNums = $countRow['couponNums'];
        //}
        return $couponNums;
    }

    /**
    * mark a coupon is consumed by id and platform key
    *
    * @param    int  couponId   
    * @throws   VerifyCoupon_Exception
    */
    public function consume_coupon($couponId, $consumed_times = 1)
    {
        if ($couponId <= 0 || !is_numeric($consumed_times) || $consumed_times < 1)
        {
            throw new VerifyCoupon_Exception(
                CommonCodeMsg::get_message(CommonCodeMsg::INVALID_ARGUMENT_ERROR),
                CommonCodeMsg::INVALID_ARGUMENT_ERROR);
        }

        $update = 'UPDATE '.$this->get_dbTableName(self::COUPON_TABLE);
        $update .= " set consume='Y',consume_time=UNIX_TIMESTAMP(),consume_times=consume_times+".$consumed_times;
        $where = " WHERE id=".$couponId;
        $sql = $update.$where;

        return mysql_query($sql);
    }
    /**
    * parse coupon list into specified array for show
    *
    * @param    Resource $result mysql result set resource
    * @return   array rearanged couon array
    * @throws    
    */
    private function parse_couponList($result)
    {
        $consumedCouponList = array();
        $couponNums = mysql_num_rows($result);
        if ($couponNums > 0)
        {
            while($consumedCoupon = mysql_fetch_array($result))
            {
                array_push($consumedCouponList, $consumedCoupon);
            }
        }
        return $consumedCouponList;
    }

    /**
    * insert coupon by an coupon info
    *
    * @param    array  couponInfo  coupon info array
    * @return   arrayif true return coupon data, else return null
    * @throws   THROW-TYPE
    */
    public function insert_coupon($couponInfo)
    {
        $this->assure_dbConnection();
        $insertSql = "INSERT INTO ".$this->get_dbTableName(self::COUPON_TABLE);
        $keys = "(";
        $values = " VALUES(";
        foreach($couponInfo as $key => $value)
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
        $insertedcouponId = mysql_insert_id();
        $couponInfo['id'] = $insertedcouponId;

        return $couponInfo;
    }

}
?>
