<?php
/**
 * @Desc:请求
 * @author: hbh
 * @Time: 2020/4/10   11:25
 */

namespace App\Extension\Utils;


class RequestUtil
{

    /**获取用户真是IP地址
     * @return mixed|string
     * @author: hbh
     * @Time: 2020/4/9   16:21
     */
    public static function getIPAddress()
    {
        $ipAddress = '';

        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $ipAddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $ipAddress = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $ipAddress = $_SERVER["REMOTE_ADDR"];
            }
        }

        return $ipAddress;
    }


    /**
     * @retu将 IPV4 的字符串互联网协议转换成长整型数字rn string
     * @author: hbh
     * @Time: 2020/4/9   16:22
     */
    public static function getLongIPAddress()
    {
        return sprintf("%u", ip2long(self::getIPAddress()));
    }
    /**获取请求来源的url
     * @return string
     * @author: hbh
     * @Time: 2020/4/9   17:26
     */
    public static function get_url(){
        return $_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?".$_SERVER["QUERY_STRING"];
    }

    /**获取请求来源url中的path部分
     * @return array|false|int|string|null
     * @author: hbh
     * @Time: 2020/4/9   17:28
     */
    public static function get_url_path(){
        $url=self::get_url();
        return trim(parse_url($url, PHP_URL_PATH));
    }
}
