<?php
namespace app\admin\controller;

use think\Request;

/**
 * Class Welcome 后台登录登出注册控制器
 * @package app\admin\controller
 */
class Welcome extends Base
{

    //login登录业务
    protected $login = null;

    public function initialize()
    {

        parent::initialize();

        //实例化service
        $this->login = controller("LoginService", "service");

    }

    /**
     * 登录API 获取token
     * @return $token
     */
    public function loginSubmit()
    {

        //验证器
        $validate = new \app\admin\validate\Login();

        //信息验证
        if (!$validate->check(input())) {

            return $this->end('102', $validate->getError());

        }

        $result = $this->login->login( input() );

        return $this->end($result);
    }

    /**
     * 登出API
     * @return mixed
     */
    public function loginOut()
    {

        if (!$this->token) {
            return json([
                'code' => 102,
                'msg'  => '没有登陆',
                'data' => null
            ]);
        }

        //登出成功，清除缓存
        $result = $this->login->loginOut($this->token);

        return $this->end($result);

    }


}
