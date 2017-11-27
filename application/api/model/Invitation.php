<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/7 0007
 * Time: 17:20
 */

namespace app\api\model;


use think\Db;
use think\Image;
use think\Model;
use think\Session;

class Invitation extends Model
{
    protected $table = 't_invitation';

    /*
     * 向微信服务器发送图片素材
     * 生成图片id以便发送客服消息
     */
    public function sendMsg($imgUrl){
        var_dump($imgUrl);
        $TOKEN = Session::get('accessToken');
        if(!file_exists($imgUrl)){
            return '不存在图片';
//            exit();
        }
        $URL ='http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token='.$TOKEN.'&type=image';
//        $data = array('media'=>'@'.$imgUrl);
        $mediaFile = realpath($imgUrl);
        $data   = array('media' => '@'.$mediaFile);
        $miniType = mime_content_type($mediaFile);
        if(class_exists('CurlFile')){
            $media = new \CURLFile($mediaFile);
            $media->setMimeType($miniType);
            $data = array('media' => $media);
        }
        $res = json_decode($this->curl_post($URL, $data), true);
//        var_dump($res);
//        $result = $this->curl_post($URL,$data);
//        $data = json_decode($result,true);
        return $res['media_id'];
    }
    /**
     * 生成发送的邀请卡图片
     */
    public function buildCard($openid){
        $data = $this->get(1);
        // 获取二维码
        $dh = ROOT_PATH . 'public/' . DS . '/uploads/ewm/ewmlogo/'.$openid.'.jpg';
        if(!file_exists($dh)){
            $urlWechat = $this->weCode($openid);
            $this->qrcode($urlWechat);
        }
        /*
         * 根据图片和二维码，合成图片
         */
        $url = ROOT_PATH .'public/'.$data['image'];
        $image = Image::open($url);
        $buildUrl = ROOT_PATH . 'public' . DS .'/uploads/sharecard/'.$openid.'.png';
        $image->water($dh,$data['position'])
            ->save($buildUrl);
        // 获取微信服务器图片素材id
        $media_id = $this->sendMsg($buildUrl);
        // 发送客服消息
        $send = new Wechat();
        $pb = $send->sendNews($openid, $media_id);
        return $pb;
    }
    /**
     * 通过微信服务器，获取该微信公众号的二维码
     */
    public function weCode($openid){
        $TOKEN = Session::get('accessToken');
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$TOKEN;
        $data = '{
            "expire_seconds": 2592000,
            "action_name": "QR_STR_SCENE", 
            "action_info": 
                {"scene": 
                    {"scene_str": "'.$openid.'"}
                }
        }';
        $result = $this->api_notice_increment($url, $data);
        $result = json_decode($result, true);
        if($result['url']) {
            return $result['url'];
        }else{
            return '生成微信公众号的二维码失败！';
        }
    }
    private function api_notice_increment($url, $data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            return 'Errno'.curl_error($curl);
        }
        curl_close($curl);
        return $result;
    }
    /**
     * 生成发送的二维码图片
     */
    public function qrcode($url,$level=3,$size=5)
    {
        $openid = Session::get('openid');
        Vendor('phpqrcode.phpqrcode');
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        // 生成二维码图片
        $object = new \QRcode();
        $QR = ROOT_PATH . 'public/' . DS . '/uploads/ewm/'.$openid.'.jpg';
        $object->png($url, $QR, $errorCorrectionLevel, $matrixPointSize, 2);
        // 查询当前openid的头像地址
        $logo = Db::table('t_members')->where('openid',$openid)->value('headimgurl');
        $dh = ROOT_PATH . 'public/' . DS . '/uploads/ewm/ewmlogo/'.$openid.'.jpg';  //二维码图片地址
        if($logo !== FALSE){
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);
            $QR_height = imagesy($QR);
            $logo_width = imagesx($logo);
            $logo_height = imagesy($logo);
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        }
        $result = imagepng($QR,$dh);  //跟logo合并之后的地址
        return $result;
    }
    public function curl_post($url, $data = null){
         //创建一个新cURL资源
         $curl = curl_init();
         //设置URL和相应的选项
         curl_setopt($curl, CURLOPT_URL, $url);
         if (!empty($data)){
          curl_setopt($curl, CURLOPT_POST, 1);
          curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行curl，抓取URL并把它传递给浏览器
        $output = curl_exec($curl);
        //关闭cURL资源，并且释放系统资源
        curl_close($curl);
        return $output;
    }
}