<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/28 0028
 * Time: 8:46
 */

namespace app\api\controller\report;
use app\lib\Qcloud\Jssdk;
use app\lib\Qcloud\Openid as OpenidModel;
use think\Session;

class Openid
{
    public function getOpenid (){
        // 获取头部信息
        if(!input('code')){
            $redirect_uri = config('host').'dist/html/index.html';
            $appId = config('we_appId');
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appId.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
            $data = [
                'code' => '1',
                'url' => $url
            ];
            return $data;
        }else{
            $openid = Session::get('openid');
            if(!$openid){
                $code = input('code');
                $openid = OpenidModel::get($code);
                if($openid){
                    Session::set('openid', $openid);
                }
            }
            $data = [
                'code' => '0',
                'url' => ''
            ];
            return $data;
        }
    }
    public function getJssdk(){
        $openidData = $this->getOpenid();
        if($openidData['code'] == '0'){
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
        }else{
            return $openidData;
        }
    }
}