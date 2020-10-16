<?php

namespace app\common\behavior;

use think\Exception;
use think\Response;
use Request;
use app\common\redis\Redis;

class Cors
{
    public function run($dispatch){

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers:Accept,Accept-Encoding,Accept-Language,Connection,Content-Length,Content-Type,Host,Origin,platform,Referer,token,User-Agent,Access-Control-Request-Headers");
        header('Access-Control-Allow-Methods: POST,GET,PUT,DELETE');

        //实例化redis
    	$redis = new Redis();

        //如果控制器在相关数组里，就允许token为空

        if( !in_array(
            strtolower(Request::instance()->action()),
            [
                strtolower('loginSubmit'),  //登陆
            ]
        ) ){

        	//测试使用
        	$headerToken = Request::instance()->header()['token'] ?? input('token');

	        //验证token

	        if (empty($headerToken)) {
	        	
	            die(json_encode([
	                'code' => 102,
	                'msg'  => '请登录',
	                'data' => null
	            ], JSON_UNESCAPED_UNICODE));

	        } else {

		    	//获取缓存
		    	$token = $redis->initRedis()
		    			->getCache($headerToken);

		    	//登录的token不存在
		    	if (!$token) {

			    	die(json_encode([
		                'code' => 102,
		                'msg'  => '登录失效',
		                'data' => null
		            ], JSON_UNESCAPED_UNICODE));

		    	}
	        }

        }
    }
}