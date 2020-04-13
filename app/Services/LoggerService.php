<?php
/**
 * @Desc:日志服务
 * @author: hbh
 * @Time: 2020/4/9   15:08
 */

namespace App\Services;


use App\Extension\Utils\DateUtil;
use App\Extension\Utils\RequestUtil;

use App\Models\LogUserOperate;
use App\Models\SystemApi;
use App\Models\LogUserLogin;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoggerService
{
    public static function getUser(){
        return JWTAuth::parseToken()->touser();
    }
    /**登录
     * @return bool
     * @author: hbh
     * @Time: 2020/4/9   16:37
     */
    public static function login()
    {
        try {
            $log = new LogUserLogin();
            $log->addtime = DateUtil::addtime();
            $log->ip = RequestUtil::getIPAddress();
            $log->status = 1;
            $user=self::getUser();
            $log->uid = $user->id;
            $log->username = $user->username;
            return $log->save();
        } catch (\Exception $e) {
            //记录到文件
            self::exception('[用户登录记录异常！！]' . $e->getMessage());
        }

        return false;
    }

    /**退出登录
     * @return bool
     * @author: hbh
     * @Time: 2020/4/9   16:37
     */
    public static function loginOut()
    {
        try {
            $log = new LogUserLogin();
            $log->addtime = DateUtil::addtime();
            $log->ip = RequestUtil::getIPAddress();
            $log->status = 2;
            $user=self::getUser();
            $log->uid = $user->id;
            $log->username = $user->username;
            return $log->save();
        } catch (\Exception $e) {
            //记录到文件
            self::exception('[用户退出登录记录异常！！]' . $e->getMessage());
        }

        return false;
    }

    /**添加操作
     * @param $object
     * @author: hbh
     * @Time: 2020/4/9   17:00
     */
    public static function operateAdd($object)
    {
        self::userOperate(1, $object);
    }

    /**更新数据操作
     * @param $object
     * @author: hbh
     * @Time: 2020/4/9   17:01
     */
    public static function operateUpdate($object)
    {
        self::userOperate(2, $object);
    }

    /**更新数据操作
     * @param $object
     * @author: hbh
     * @Time: 2020/4/9   17:01
     */
    public static function operateDel( $object)
    {
        self::userOperate(3, $object);
    }

    /**
     * @param $operate_type
     * @param $obj_type
     * @param $object
     * @return bool
     * @author: hbh
     * @Time: 2020/4/9   17:04
     */
    public static function userOperate( $operate_type, $object)
    {
        try {
            $obj_type = SystemApi::getIdByRequest();
            $log = new LogUserOperate();
            $log->operate_type = $operate_type;
            $log->obj_type = $obj_type;
            $log->addtime = DateUtil::addtime();
            $log->ip = RequestUtil::getIPAddress();
            $log->obj_id = $object->id;
            $log->obj_data = json_encode($object);
            $user=self::getUser();
            $log->uid = $user->id;
            $log->username = $user->username;
            return $log->save();
        } catch (\Exception $e) {
            //记录到文件
            self::exception('[用户操作记录异常！！]' . $e->getMessage());
        }

        return false;
    }

    /**
     *  默认文件日志
     * @param $message
     * @param string $title
     * @param string $method
     */
    public static function info($message, $title = '', $method = '')
    {
        try {
            $message = $title . '|' . $message;
            if (empty($method)) {
                $backtrace = debug_backtrace();
                if (isset($backtrace[1]['function'])) $message = $backtrace[1]['function'] . '|' . $message;
            } else $message = $method . '|' . $message;

            Log::info($message);
        } catch (\Exception $e) {
            self::exception($e->getMessage());
        }
    }

    /**
     *  记录错误级别的日志
     * @param $message
     * @param string $title
     * @param string $method
     */
    public static function error($message, $title = '', $method = '')
    {
        try {
            $message = $title . '|' . $message;
            if (empty($method)) {
                $backtrace = debug_backtrace();
                if (isset($backtrace[1]['function'])) $message = $backtrace[1]['function'] . '|' . $message;
            } else $message = $method . '|' . $message;

            Log::error($message);
        } catch (\Exception $e) {
            self::exception($e->getMessage());
        }
    }

    /**一般异常记录
     * @param $message
     * @author: hbh
     * @Time: 2020/4/9   16:56
     */
    public static function exception($message)
    {
        try {
            Log::channel('exception')->info($message);
        } catch (\Exception $e) {
        }
    }


}
