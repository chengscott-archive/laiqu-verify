<?php
/**
 * constants.php  定义团购券验证常量
 *
 * @package   o2o
 * @category  cpapi
 * @author    everpointer  zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-3-23 上午11:39
 */
/* security setting */
define('ALLOWED_IP','127.0.0.1');

/**
 * 团购券验证和消费结果常量
 **/
define('ACCESS_NOT_ALLOWED',9999);
define('UNKNOWN_EXCEPTION',10000);

define('COUPON_IS_VALID', 1001);    // 验证有效
define('COUPON_USED_OK', 1002);     // 使用成功
define('COUPON_INVALID_USED', 1003);// 已被使用
define('COUPON_ENDED', 1004);       // 已过期
define('COUPON_INVALID', 1005);     // 无效
define('COUPON_WRONG_PWD', 1006);     // 密码错误

// response code for telapi
// code for coupon
define("COUPON_VERIFY_OK", 100);    // 验证成功
define("COUPON_VERIRY_FAILED", 101);    // 验证失败
define("COUPON_CONSUME_OK", 102);    // 消费成功
define("COUPON_CONSUME_FAILED", 103);  // 消费失败
define("COUPON_CONSUME_NOT_EXIST", 104);  // 团购券不存在
define("COUPON_CONSUME_USED", 105);  // 团购券已被使用
define("COUPON_CONSUME_EXPIRED", 106);  // 团购券已过期
define("PARTNER_OR_PLATFORM_BIND",107); // 商家没有绑定手机号或平台
define("PLATFORM_LOGIN_FAILED", 108); // 平台登录失败
define("PLATFORM_NOT_BIND", 109); //平台未绑定
define("COUPON_CONSUME_TIMES_NOT_ENOUGH", 110); // 团购券剩余可消费次数不足
define("COUPON_SYNC_FAILED_ERROR", 111); // 团购券验证后同步失败

// code for supplier
define("PARTNER_BINDED", 200);    //商家账号已绑定
define("PARTNER_NOT_BIND", 201);    //商家账号未绑定
define("PARTNER_BIND_OK", 202);    //商家账号绑定成功
define("PARTNER_NOT_EXIST", 203);    //商家账号不存在
define("PARTNER_UNBIND_OK", 204);    //商家账号解除绑定成功
define("PARTNER_UNBIND_FAILED", 205);    //商家账号解除绑定失败
?>
