<?php
/**
 * PartnerPlatforms.php  class for handling table partner_platforms
 *
 * @package   verifyCoupon
 * @category  partnet_platforms
 * @author    everpointer  zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-3-16 下午3:35
 */
require_once('../common/Exception.php');
require_once('../common/class/MyTable.php');
require_once('../common/common.php');

class PartnerPlatforms extends MyTable
{
    /* varibale defination */
    const PARTNET_PLATFORMS_TABLE = 'partner_platforms';
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(self::PARTNET_PLATFORMS_TABLE);
    }

 }
 
?>
