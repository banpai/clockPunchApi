<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/7 0007
 * Time: 16:12
 */

namespace app\api\controller\clockPunch;
use app\api\model\Upload;
use app\api\model\Invitation as InvitationModel;
use app\api\validate\OpenidValidate;
use app\api\model\Wechat as WechatModel;
use think\Image;

class Invitation
{
    /**
     * CMS 后台上传邀请卡的数据接口
     */
    public function putImage(){
        $base64 = input('fileVal');
        $position = input('positioan');
        $wellKnow1 = input('wellKnow1');
        $wellKnow2 = input('wellKnow2');
        $size = input('size');
        $backData = [
            'code'=>0,
            'message'=>'上传成功'
        ];
        $Time = time();
        $createTime = date("Y-m-d H:i:s", $Time);
        if($base64){
            $path = Upload::base64Upload($base64);
            if($path){
                $data = InvitationModel::get(1);
                if(!$data){
                    $data = new InvitationModel();
                }
                $data->id = 1;
                $data->image = $path;
                $data->createTime = $createTime;
                $data->updateTime = $createTime;
                $data->wellKnow1 = $wellKnow1;
                $data->wellKnow2 = $wellKnow2;
                $data->flag = 1;
                $data->save();
            }else{
                $backData['code'] = 1;
                $backData['message'] = '图片上传失败';
            }
        }else{
            $data = InvitationModel::get(1);
            if(!$data){
                $data = new InvitationModel();
            }
            $data->id = 1;
            $data->createTime = $createTime;
            $data->flag = 1;
            $data->updateTime = $createTime;
            $data->wellKnow1 = $wellKnow1;
            $data->wellKnow2 = $wellKnow2;
            $data->save();
        }
        $tmp = new WechatModel();
        $tmp->delFile(ROOT_PATH . 'public/' . DS . '/uploads/ewm/ewmlogo/');
        return $backData;
    }

    /**
     * 获取邀请卡的数据接口
     */
    public function getData(){
        $data = InvitationModel::get(1);
        $data['host'] = config('host');
        return json($data);
    }
}