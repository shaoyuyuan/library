<?php
namespace app\admin\controller;

use think\Request;
use think\Config;   //加载配置
use app\common\redis\Redis;

/**
 * Class Welcome 后台登录登出注册控制器
 * @package app\admin\controller
 */
class Welcome extends Base
{


    /**
     * 登录API 获取token
     * @return $token
     */
    public function loginSubmit()
    {

        $username = input('username');
        $password = input('password');

        //验证用户名，密码
        if (empty($username) || empty($password)) {

            return json([
                'code' => 102,
                'msg'  => '用户名或密码必填',
                'data' => null
            ]);
            
        }

        //查询用户信息
        $admin = db('admin')
            ->field('id,username,password,is_del')
            ->where(['username' => $username, 'is_del' => 0])
            ->find();

        //用户信息验证
        if (empty($admin)) {
            
            return json([
                'code' => 102,
                'msg'  => '用户不存在',
                'data' => null
            ]);
        } elseif ($admin['password'] != md5(md5($password . 'library'))) {
            //验证密码
            return json([
                'code' => 102,
                'msg'  => '密码错误',
                'data' => null
            ]);

        }

        //登录成功，写一个token，里面存放用户的基本信息，有效期10天
        $token = strtoupper(md5($username .time()));

        $ttl = 10*86400;

        //token缓存
        $adminSetCache = $this->redis->initRedis(config("data_config.admin")['select'])
                    ->setCache($token, $admin, $ttl);

        return json($adminSetCache);
    }

    /**
     * 登出API
     * @return mixed
     */
    public function logoutSubmit()
    {

        if (!$this->token) {
            return json([
                'code' => 102,
                'msg'  => '没有登陆',
                'data' => null
            ]);
        }

        //登出成功，清除缓存
        $adminDelCache = $this->redis->initRedis(config("data_config.admin")['select'])
                    ->delCache($this->token);

        return json($adminDelCache);

    }


}
