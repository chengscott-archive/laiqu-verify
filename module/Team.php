<?php
/**
 * Team.php  module for table team
 *
 * @package   VerifyCoupon
 * @category  team
 * @author    everpointer zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-3-14 上午10:30
 */
require_once('../common/class/MyTable.php');

class Team extends MyTable
{
    /* table constants */
    const TEAM_TABLE  = 'team';
    const PLATFORM_TABLE  = 'platform';
    
    /**
     * Constructor.
     *
     * @param string     
     * @return  
     */
    public function __construct()
    {
        parent::__construct(self::TEAM_TABLE); 
    }
    /**
    * query consumed coupons according to partner id and team id
    *
    * @param  array  $searchFields condition field to filter the result
    * @return  array consumed coupon list     
    * @throws   
    */
    public function get_teams($searchFields, $start, $offset)
    {
        foreach ($searchFields as $fieldKey => $fieldValue)
        {
            $$fieldKey = is_string($fieldValue)?mysql_real_escape_string($fieldValue): $fieldValue;
        }

        if ($partnerId <= 0)
        {
            throw new Exception(
                  CommonCodeMsg::get_message(CommonCodeMsg::INVALID_ARGUMENT_ERROR),
                  CommonCodeMsg::INVALID_ARGUEMENT);
        }
        if ($start < 0) $start = 0;
        if ($offset <= 0) $offset = 4;

        $select = "select t.id as teamId, t.title as teamTitle, p.title as platformTitle, t.begin_time as beginTime,";
        $select .= "t.end_time as endTime, t.now_num as nowNum, t.min_num as minNum, t.team_price as teamPrice,";
        $select .= "t.market_price as marketPrice, t.platform_key as platformKey,";
        $select .= "t.expire_time as expireTime, t.state";
        $select .= ' from '.self::DBDATABASE.'.'.self::TEAM_TABLE.' t,'.self::DBDATABASE.'.'.self::PLATFORM_TABLE.' p';
        $sql = $select." WHERE t.partner_id=$partnerId AND t.platform_key=p.key ";
        $sql .= "order by t.state desc,t.expire_time desc,t.id desc ";
        $sql .= "LIMIT $start,$offset ";

        $result = mysql_query($sql);
        if (mysql_num_rows($result) < 1)
            return array();
        else 
            return $this->parse_teamList($result);
    }

    /**
    * get all matched team nums
    *
    * @param    array  $searchField  fields to match
    * @return   int  num of all match team
    * @throws    
    */
    public function get_teamNums($searchFields)
    {
        $this->assure_dbConnection();
        foreach ($searchFields as $fieldKey => $fieldValue)
        {
            $$fieldKey = is_string($fieldValue)?mysql_real_escape_string($fieldValue): $fieldValue;
        }

        $sql = "SELECT count(*) as teamNums FROM ".$this->get_dbTableName(self::TEAM_TABLE);
        $sql .= " WHERE partner_id=$partnerId";

        $teamNums = 0;
        $result = mysql_query($sql);
        if (is_resource($result) && mysql_num_rows($result) > 0)
        {
            $teamRow = mysql_fetch_array($result);
            $teamNums = $teamRow['teamNums'];
        }

        return $teamNums;
    }
    /**
    * parse coupon list into specified array for show
    *
    * @param    Resource $result mysql result set resource
    * @return   array rearanged couon array
    * @throws    
    */
    private function parse_teamList($result)
    {
        $teamList = array();
        $teamNums = mysql_num_rows($result);
        if ($teamNums > 0)
        {
            while($team= mysql_fetch_array($result))
            {
                array_push($teamList, $team);
            }
        }
        return $teamList;
    }

    public function insert_team($teamInfo)
    {
        $this->assure_dbConnection();
        $insertSql = "INSERT INTO ".$this->get_dbTableName(self::TEAM_TABLE);
        $keys = "(";
        $values = " VALUES(";
        foreach($teamInfo as $key => $value)
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
        $insertTeamId = mysql_insert_id();
        $teamInfo['id'] = $insertTeamId;

        return $teamInfo;
    }
}
?>
