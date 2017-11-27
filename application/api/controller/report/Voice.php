<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/3 0003
 * Time: 15:25
 */

namespace app\api\controller\report;
use app\lib\Qcloud\Jssdk;
use think\Db;

class Voice
{
    /**
     * 初始化JSSDK
     */
    public function getJssdk(){
        $url = input('url');
        $data = [
            'code' => '0',
            'url' => ''
        ];
        $jssdk = new Jssdk(config('we_appId'), config('we_appSecret'));
        $signPackage = $jssdk->GetSignPackage($url);
        if(is_array($signPackage)){
            $data['signPackage'] = $signPackage;
        }
        return json($data);
    }
    public function light(){
        $light = input('light');
        $result = Db::table('voice')
            ->where('id', 1)
            ->update([
                'light'=>$light
            ]);
        return json($result);
    }
    public function getLight(){
        $result = Db::table('voice')
            ->where('id',1)
            ->find();
        return $result['light'];
    }
}