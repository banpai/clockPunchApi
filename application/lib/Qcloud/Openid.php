<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/28 0028
 * Time: 7:43
 */

namespace app\lib\Qcloud;

class Openid
{
    public static function get($code){
        $appid = config('we_appId');
        $secret = config('we_appSecret');
        $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
        $weixin = file_get_contents($get_token_url);
        $jsondecode = json_decode($weixin); //对JSON格式的字符串进行编码
        $array = get_object_vars($jsondecode);//转换成数组
//        var_dump($array);
        $openid = $array['openid'];//输出openid
        return $openid;
        //$ch = curl_init();
//        curl_setopt($ch,CURLOPT_URL,$get_token_url);
//        curl_setopt($ch,CURLOPT_HEADER,0);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
//        $res = curl_exec($ch);
//        var_dump($res);
//        curl_close($ch);
//        $json_obj = json_decode($res,true);

        //此时已获得微信的openid
//        $openid = $json_obj['openid'];

        //获取用户的全部可获取到的信息方法如下
        //根据openid和access_token查询用户信息
//        $access_token = $json_obj['access_token'];
//        $openid = $json_obj['openid'];
//        $get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
//
//        $ch = curl_init();
//        curl_setopt($ch,CURLOPT_URL,$get_user_info_url);
//        curl_setopt($ch,CURLOPT_HEADER,0);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
//        $res = curl_exec($ch);
//        curl_close($ch);

        //解析json
//        $user_obj = json_decode($res,true);
//        $_SESSION['user'] = $user_obj;
//        print_r($user_obj);
    }
}