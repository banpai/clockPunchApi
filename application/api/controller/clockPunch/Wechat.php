<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/17 0017
 * Time: 10:06
 */

namespace app\api\controller\clockPunch;


use app\api\model\Upload;
use app\api\validate\OpenidValidate;
use app\api\validate\OtherOpenidValidate;
use app\api\model\Wechat as WechatModel;
use app\api\model\Invitation as InvitationModel;
use app\lib\exception\ProjectMissException;
use think\Db;
use think\Session;
use app\api\model\ShowCard as ShowCardModel;
use think\Image;
class Wechat
{
    /*
     * 点击提醒打卡
     * 给相关的openid发送模板消息
     * 上一个用户提醒该用户打卡
     */
    public function warn(){
        (new OpenidValidate())->goCheck();
        (new OtherOpenidValidate())->goCheck();
        $otherOpenid = input('otheropenid');
        $nickname = input('nickname');
        $template_id = 'Z2rAnMxiUoBHyWxYU6iGN0gdVdT9N_3PnoN4VDM1o30';
        $temp = new WechatModel();
        $baseurl = input('baseurl');
        $acessToken = $temp->getAcessToken();
        $data = '{
        "touser":"'.$otherOpenid.'",
        "template_id":"'.$template_id.'",
        "url":"'.$baseurl.'",
        "data":{
                "first":{
                     "value": "'.$nickname.'",
                      "color":"#173177"
                }
            } 
        }';
        if($acessToken){
            $flag = $temp->sendTemplate($acessToken,$data);
            return $flag;
        }else{
            return '获取accesstoken出错！';
        }
    }
    /**
     * 发送邀请卡给微信的客服消息
     */
    public function sendCard(){
        (new OpenidValidate())->goCheck();
        $openid = input('openid');
        $headImgUrl = input('headimgurl');
        if($openid){
            Session::set('openid', $openid);
        }
        $data = InvitationModel::get(1);
        if($data['image']){
            $temp = new WechatModel();
            $Token = $temp->getAcessToken();
            // 获取二维码地址
            $codeUrl = $temp->getEwm($openid,$Token);
            // 获取背景图
            $url = config('host').$data['image'];
            // 获取头图
            $headImg= $temp->getHeadImg($openid, $headImgUrl);
            // 鸡汤
            $wellKnow1 = $data['wellKnow1'];
            if(!$wellKnow1){
                $wellKnow1 = '每 天 早 上 你 和 阳 光 都 在，';
            }
            $wellKnow2 = $data['wellKnow2'];
            if(!$wellKnow2){
                $wellKnow2 = '这 就 是 我 想 要 的 未 来。';
            }
            $data = [
                // 合成的背景图地址
                'backgroundUrl' => $url,
                'base64Bgurl' => $url,
                // 合成的二维码图片的地址
                'codeResult' => $codeUrl,
                'codeBase64' => $codeUrl,
                'headimgurlbase64' => $headImg,
                'str1' => $wellKnow1,
                'str2' => $wellKnow2
            ];
            $bData = [
                'err_code' => 0,
                'msg' => '',
                'data' => $data
            ];
            return $bData;
        }else{
            throw new ProjectMissException();
        }
    }
    /**
     * 发送打卡图给微信的客服消息
     */
    public function punchCard(){
        (new OpenidValidate())->goCheck();
        $openid = input('openid');
        $headImgUrl = input('headimgurl');
        $punchTime =  input('punchTime');
        if($openid){
            Session::set('openid', $openid);
        }
        // 获取数据
        $data = ShowCardModel::get(1);
        if($data['image']){
            $temp = new WechatModel();
            $Token = $temp->getAcessToken();
            $Time = time();
            $createTime = date("Y-m-d", $Time);
            // 获取背景图
            $url = config('host').$data['image'];
            // 获取二维码地址
            $codeUrl = $temp->getEwm($openid,$Token);
            // 获取头图
            $headImg= $temp->getHeadImg($openid, $headImgUrl);
            // 获取参加的人数
            $count = Db::table('t_members')->count();
            // 计算比多少人起的晚，拿我起床之前的总人数，除以总人数
            $number = Db::table('t_punch')
                ->where('punchDate', $createTime)
                ->where('punchTime','<',$punchTime)
                ->count();
            $percent = floor(($number / $count)*100);
            // 连续早起的天数
            $days = Db::table('t_members')->where('openid', $openid)->value('punchDays');
            // 鸡汤
            $wellKnow = $data['wellKnow'];
            if(!$wellKnow){
                $wellKnow = '每天都当做人生的最后一天去过吧！';
            }
            $dataB = [
                // 合成的背景图地址
                'backgroundUrl' => $url,
                'base64Bgurl' => $url,
                // 合成的二维码图片的地址
                'codeResult' => $codeUrl,
                'codeBase64' => $codeUrl,
                'headimgurlbase64' => $headImg,
                // 多少人正在参加
                'count' => $count,
                // 比百分之多少的人起的晚
                'percent' => $percent,
                // 连续早起的天数
                'days' => $days,
                // 今天的日期
                'date' => $createTime,
                // 鸡汤 25个字内
                'wellKnow' => $wellKnow
            ];
            $bData = [
                'err_code' => 0,
                'msg' => '',
                'data' => $dataB
            ];
            return $bData;
        }else{
            throw new ProjectMissException();
        }
    }


 /**
 * 获取图片的Base64编码(不支持url)
 * @date 2017-02-20 19:41:22
 *
 * @param $img_file 传入本地图片地址
 *
 * @return string
 */
public function imgToBase64($img_file) {
   $img_base64 = '';
    if (file_exists($img_file)) {
      $app_img_file = $img_file; // 图片路径
      $img_info = getimagesize($app_img_file); // 取得图片的大小，类型等
    //echo '<pre>' . print_r($img_info, true) . '</pre><br>';
      $fp = fopen($app_img_file, "r"); // 图片是否可读权限
      if ($fp) {
         $filesize = filesize($app_img_file);
          $content = fread($fp, $filesize);
            $file_content = chunk_split(base64_encode($content)); // base64编码
            switch ($img_info[2]) {           //判读图片类型
             case 1: $img_type = "gif";
               break;
            case 2: $img_type = "jpg";
                   break;
               case 3: $img_type = "png";
              break;
           }
         $img_base64 = 'data:image/' . $img_type . ';base64,' . $file_content;//合成图片的base64编码
       }
       fclose($fp);
     }
    return $img_base64; //返回图片的base64
 }
    /**
     * 接受合成的图片，发送图片的客服消息
     */
    public function sendImg(){
        (new OpenidValidate())->goCheck();
        $openid = input('openid');
        if($openid){
            Session::set('openid', $openid);
        }
        $imgData = input('imgData');
        $up = new Upload();
        $url = ROOT_PATH . 'public' . DS .$up->base64Upload($imgData);
        $temp = new WechatModel();
        $Token = $temp->getAcessToken();
        // 根据$url 获取临时素材
        $media_id = $temp->sendMsg($url,$Token);
        if(!$media_id){
            return '获取不了微信媒体图片id，可能需要重启服务器。';
        }else{
            // 发送客服消息
            $pb = $temp->sendNews($openid, $media_id);
            return $pb;
        }
    }
}