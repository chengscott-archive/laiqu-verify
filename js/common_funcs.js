/**
 ** common used js functions by this project
 **
 ** @package   common_funcs
 ** @category  laiqu
 ** @author    everpointer  zhangfeng@laicheap.com
 ** @version   v1.000
 ** @history   v1.000 first release 2012-5-20 下午11:11
 **/
var http_host = 'http://localhost:8888/laiqu';

// 高亮左侧导航栏的当前导航项
function highlight_left_nav_bar(nav_bar_bg_id)
{
    $("#nav_bg_" + nav_bar_bg_id).addClass("nav_bg");
} 
// 显示加载框
// 需要：jquery.simplermodal.js插件
function show_loading_twirly()
{
  $.modal("<div id='loading_twirly_div'><img src='" + http_host+"/images/loading.gif'>&nbsp;&nbsp;&nbsp;正在加载......</div>",
          {'position':[240,null]});
}

// 去除加载框
function remove_loading_twirly(container)
{
  $.modal.close();
}
