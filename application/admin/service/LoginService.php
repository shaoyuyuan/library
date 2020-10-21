<?php

namespace app\admin\service;

use app\admin\model\Admin;
use think\Controller;

class LoginService extends Base
{

    /**
     * 登录
     * @author syy 新增
     *
     */
    public function login( $data ){

        //实例化管理员
        $adminModel = new Admin();
        //查询管理员信息
        $adminInfo = $adminModel::where([
            'username' => $data['username'],
            'is_del'   => 0
        ])->find()->toArray();

        //验证用户是否存在
        if (empty($adminInfo['id'])) {

            return [
                'code' => 102,
                'msg'  => "没有用户信息",
                'data' => null
            ];

        }

        //验证密码是否正确
        if ($adminInfo['password'] != md5(md5($data['password'] . 'library'))) {

            return [
                'code' => 102,
                'msg'  => '密码错误',
                'data' => null
            ];
        }

        //登录成功，写一个token，里面存放用户的基本信息，有效期10天
        $token = strtoupper(md5($data['username'] .time()));

        //token过期时间
        $ttl = 10*86400;

        //去除密码
        unset($adminInfo['password']);

        //token缓存
        $adminSetCache = $this->redis
            ->initRedis(config("data_config.admin")['select'])
            ->setCache($token, $adminInfo, $ttl);

        if ($adminSetCache['code'] !== 200) {
            return $adminSetCache;
        }

        return [
            'code' => 200,
            'msg'  => 'success',
            'data' => $token
        ];

    }

    /**
     * 登出
     * @param $token
     */
    public function loginOut($token) {

        //登出成功，清除缓存
        $adminDelCache = $this->redis->initRedis(config("data_config.admin")['select'])
            ->delCache($token);

        return $adminDelCache;
    }

}