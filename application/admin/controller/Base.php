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

        $token = Request::instance()->header()['token'] ?? input('token');

    	//定义用户信息
    	$this->token = $token;

    }

    /**
     * 返回数据
     * @param $code (int or array)
     * @param null $msg
     * @param null $data
     */
    protected function end($code = 200, $msg=null, $data=null) {


        if(is_array($code))
        {
            $return_data['code'] = $code['code'];
            $return_data['msg'] = $code['msg'];
            $return_data['data'] = $code['data'];

        }else{

            $return_data['code'] = $code;
            $return_data['msg'] = $msg;
            $return_data['data'] = $data;

        }

        return json($return_data);

    }

}
