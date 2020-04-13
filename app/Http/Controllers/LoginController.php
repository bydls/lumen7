<?php
/**
 * @Desc:用户登录
 * @author: hbh
 * @Time: 2020/4/9   18:26
 */

namespace App\Http\Controllers;

use App\Extension\Captcha\Captcha;
use App\Extension\Utils\{CodeUtil, DateUtil, CheckUtil};
use App\Services\LoggerService;
use App\models\ApiUser;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends BaseController
{

    /**帐号密码登录
     * @param Request $request
     * @return mixed
     * @author: hbh
     * @Time: 2020/4/9   18:23
     */
    public function login(Request $request)
    {
        $this->checkout_field = ['username', 'password'];
        $this->validator($request, 'login');
        if ($this->error_msg) return $this->result->error($this->error_msg);
        //判断验证码是否正确
        $this->error_msg = Captcha::checkCaptchaCode($request->input('captcha'), $request->input('identity'));
        if ($this->error_msg) return $this->result->error($this->error_msg);

        $password = $request->input('password');

        $hashPwd = CodeUtil::hashMixed($password);

        $where = [
            ['username', $request->input('username')],
            ['password', $hashPwd],
            ['is_del', 0],
        ];
        $user_info = ApiUser::query()->where($where)->first();
        if (!isset($user_info)) return $this->result->errorCode(101001);
        //添加登录日志

        /** @var ApiUser $user_info */
        LoggerService::login();
        $token = $this->auth->login($user_info);
        $return_data = [
            'token' => $token,
            'username' => $user_info->username,
            'role_id' => $user_info->role_id,
        ];
        return $this->result->success($return_data);

    }

    /**退出登录
     * @param Request $request
     * @return mixed
     * @author: hbh
     * @Time: 2020/4/9   18:23
     */
    public function outLogin(Request $request)
    {
        $this->getCommonInfo($user, $request, 'login');
        if ($this->error_msg) return $this->result->error($this->error_msg);
        try {
            //添加退出登录日志
            LoggerService::loginOut();
            $this->auth->logout();
        } catch (\Exception $e) {
            //token不存在会报异常，清除文件缓存也可能会报异常。
            return $this->result->error($e->getMessage());
        }

        return $this->result->success();
    }


    /**刷新获取一个新的令牌
     * @param Request $request
     * @return mixed
     * @author: hbh
     * @Time: 2020/4/9   14:13
     */
    public function refresh(Request $request)
    {
        $this->getCommonInfo($user, $request, 'login');
        if ($this->error_msg) return $this->result->error($this->error_msg);
        try {
            $new_token = $this->auth->refresh(true, true);
        } catch (\Exception $e) {
            return $this->result->error($e->getMessage());
        }
        $return_data = [
            'token' => $new_token,
        ];
        return $this->result->success($return_data);
    }

    /**获取验证码
     * @return mixed
     * @author: hbh
     * @Time: 2020/4/10   10:25
     */
    public function getCaptcha()
    {
        $captcha = Captcha::getcaptcha();
        return $this->result->success($captcha);
    }

    /**编辑用户（新建或者编辑）
     * @param Request $request
     * @return mixed
     * @author: hbh
     * @Time: 2020/4/10   12:42
     */
    public function editUser(Request $request)
    {
        $this->checkout_field = ['username', 'role_id'];
        $this->getCommonInfo($user, $request, 'login');
        if ($this->error_msg) return $this->result->error($this->error_msg);
        $id = $request->input('id', 0);
        $username = $request->input('username');
        $role_id = $request->input('role_id');
        $edit_data = [
            'username' => $username,
            'role_id' => $role_id,
        ];
        $nickname = $request->input('nickname', '');
        if ($nickname) {
            if (!checkUtil::checkNickname($nickname)) return $this->result->errorCode();
            $edit_data['nickname'] = $nickname;
        }
        if ($id) {
            //修改用户
            $where = [
                ['id', $id],
                ['username', $username],
            ];
            $user = ApiUser::query()->where($where)->first();
            if (!isset($user)) return $this->result->errorCode(101005);
            try {
                $user->update($edit_data);
                return $this->result->updateSuccess([], $user);
            } catch (\Exception $e) {
                return $this->result->error($e->getMessage());
            }
        } else {
            //新建用户
            //验证用户名
            if (!CheckUtil::checkUserName($username)) return $this->result->errorCode(101006);
            //验证密码
            $password = $request->input('password', '');
            if (!CheckUtil::checkPassword($password)) return $this->result->errorCode(101007);
            $password = CodeUtil::hashMixed($password);
            $edit_data['password'] = $password;
            $edit_data['addtime'] = DateUtil::addtime();
            $edit_data['salt'] = CodeUtil::getSalt();
            try {
                $id = ApiUser::query()->insertGetId($edit_data);
                return $this->result->addSuccess([], $user);
            } catch (\Exception $e) {
                return $this->result->error($e->getMessage());
            }
        }
    }


    public function getToken()
    {
        $user_info = ApiUser::query()->where('id', 1)->first();
        echo $this->auth->login($user_info);
    }

    public function test()
    {

        //print_r($this->auth->user());     // n
        //  echo  $this->auth->refresh(true, true); //y
        //  var_dump($this->auth->logout()); //n
        print_r(JWTAuth::parseToken()->touser());//y  ==>$this->auth->user()
        //  print_r(JWTAuth::parseToken()->refresh(true, true)); //y ==> $this->auth->refresh(true, true)
    }

}
