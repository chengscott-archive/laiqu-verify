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
        parent::__construct(); 
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
        foreach ($searchFields as $fieldKey => $fieldValue)
        {
            $$fieldKey = is_string($fieldValue)?mysql_real_escape_string($fieldValue): $fieldValue;
        }

        if ($start < 0) $start = 0;
        if ($offset <= 0) $offset = 6;

        $select = "select c.order_id as orderId, t.title as teamTitle, c.id as couponId,";
        $select .= "u.username as username, u.email as email, o.subbranch as subbranch, c.consume_time as consumeTime";
        $select .= ' from '.self::DBDATABASE.'.'.self::COUPON_TABLE.' c,'.self::DBDATABASE.'.'.self::TEAM_TABLE.' t,'.self::DBDATABASE.'.'.self::USER_TABLE.' u,'.self::DBDATABASE.'.'.self::ORDER_TABLE.' o ';
        $sql = $select." WHERE c.consume='Y' ";
        if (count(searchFelds) > 0)
        {
            if ($partnerId > 0)
                $sql .= " AND c.partner_id=$partnerId";
            if ($teamId > 0)
                $sql .= " AND c.team_id=$teamId ";
            if (!empty($platform))
                $sql .= " AND c.platform_key='$platform'";
            if ($orderId > 0)
                $sql .= " AND c.order_id=$orderId";
            if ($couponId > 0)
                $sql .= " AND c.id=$couponId";
            if (!empty($username))
                $sql .= " AND u.username='$username'";
        }
        $sql .= " AND t.id=c.team_id AND o.id=c.order_id AND u.id=c.user_id";
        $sql .= " AND t.id=c.team_id AND o.id=c.order_id AND u.id=c.user_id";
        // group and order
        $sql .= " order by c.consume_time desc";
        $sql .= " LIMIT $start,$offset";

        $result = mysql_query($sql);
        if (mysql_num_rows($result) < 1)
            return array();
        else 
            return $this->parse_couponList($result);
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
        foreach ($searchFields as $fieldKey => $fieldValue)
        {
            $$fieldKey = is_string($fieldValue)?mysql_real_escape_string($fieldValue): $fieldValue;
        }

        $sql = "SELECT count(*) as couponNums FROM ".$this->get_dbTableName(self::COUPON_TABLE).' as c,';
        $sql .= $this->get_dbTableName(self::USER_TABLE).' as u';
        $sql .= " WHERE consume='Y'";

        if (count($searchFields) > 0)
        {
            $sql .= $partnerId>0 ? " AND c.partner_id=".$partnerId : "";
            $sql .= !empty($platform)? " AND c.platform_key='$platform'" : "";
            $sql .= $teamId>0 ? " AND c.team_id=".$teamId : "";
            $sql .= $orderId>0 ? " AND c.order_id=".$orderId : "";
            $sql .= $couponId>0 ? " AND c.coupon_id=".$couponId : "";
            $sql .= !empty($username) ? " AND u.username='$username'" : "";
        }
        
        $couponNums = 0;
        $result = mysql_query($sql);
        if (is_resource($result) && mysql_num_rows($result) > 0)
        {
            $countRow = mysql_fetch_array($result);
            $couponNums = $countRow['couponNums'];
        }

        return $couponNums;
    }

    /**
    * mark a coupon is consumed by id and platform key
    *
    * @param    int  couponId   
    * @throws   VerifyCoupon_Exception
    */
    public function consume_coupon($couponId, $platformKey)
    {
        if ($couponId <= 0 || empty($platformKey))
        {
            throw new VerifyCoupon_Exception(
                CommonCodeMsg::get_message(CommonCodeMsg::INVALID_ARGUMENT_ERROR),
                CommonCodeMsg::INVALID_ARGUMENT_ERROR);
        }
        $update = 'UPDATE '.self::DBDATABASE.'.'.self::COUPON_TABLE;
        $update .= " set consume='Y',consume_time=UNIX_TIMESTAMP() ";
        $where = "WHERE id=".$couponId." AND platform_key='".$platformKey."' AND consume='N'";
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

}
?>
