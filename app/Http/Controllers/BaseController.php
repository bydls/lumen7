<?php
/**
 * @Desc:基础类
 * @author: hbh
 * @Time: 2020/4/9   18:08
 */

namespace App\Http\Controllers;


use App\Extension\Cipher\Cipher;
use App\Http\Validators\CommonValidator;
use App\Services\ResultService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\JWTGuard;
use Tymon\JWTAuth\Facades\JWTAuth;
class BaseController extends Controller
{
    /**
     * @var  JWTGuard 认证服务
     */
    protected $auth;

    /**
     * @var ResultService 返回结果处理服务
     */
    protected $result;

    //需要验证的参数
    protected $checkout_field;

    //异常信息
    protected $error_msg;

    public function __construct(Cipher $cipherService)
    {
        $this->auth = app('auth.driver');
        $this->result = new ResultService($cipherService, $this->auth);
    }


    /**获取用户 及必传参数
     * @param  $user
     * @param Request $request
     * @param string $object_name
     * @return bool|string
     * @author: hbh
     * @Time: 2020/4/9   18:40
     */
    protected function getCommonInfo(&$user, Request $request, $object_name = '')
    {
        $user = JWTAuth::parseToken()->touser();
        if (!$user) {
            $this->error_msg = '获取用户失败';
            return false;
        }
        $this->validator($request, $object_name);
        return true;
    }

    /**验证必传参数
     * @param Request $request
     * @param $object_name
     * @return bool
     * @author: hbh
     * @Time: 2020/4/10   13:44
     */
    protected function validator(Request $request, $object_name)
    {
        if (!empty($this->checkout_field)) {
            $params = $request->input();
            $check_result = CommonValidator::checkParams($params, $this->checkout_field, $object_name);
            if ($check_result) {
                $this->error_msg = $check_result;
                return false;
            }
        }
        return true;
    }
}
