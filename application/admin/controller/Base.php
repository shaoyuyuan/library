<?php
namespace app\admin\controller;

use think\Controller;
use Request;
use think\Config;   //加载配置
use app\common\redis\Redis;

class Base extends Controller
{

    //token信息
    protected $token = null;

    //reids
    protected $redis = null;
    //初始化

	public function initialize()
    {

    	parent::initialize();

    	//获取登陆者信息

        //实例化redis
        $this->redis = new Redis();
    	//测试使用,behavior已经验证token了，这步不需要验证了
        $adminGetCache = $this->redis->initRedis(config('data_config.admin')['select'])
                    ->getCache(Request::instance()->header()['token'] ?? input('token'));

    	//定义用户信息
    	$this->token = $adminGetCache['data'];

    }
}
