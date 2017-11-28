<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/21 0021
 * Time: 9:12
 */

namespace app\api\controller\clockPunch;

use app\api\model\ShowCard as ShowCardModel;
use app\api\model\Upload;
use app\api\model\Wechat as WechatModel;
class ShowCard
{
    /**
     * CMS后台上传打卡图的数据
     */
    public function changeData(){
        $base64 = input('fileVal');
        $position = input('positioan');
        $size = input('size');
        $wellKnow = input('wellKnow');
        $backData = [
            'code'=>0,
            'message'=>'上传成功'
        ];
        $Time = time();
        $createTime = date("Y-m-d H:i:s", $Time);
        if($base64){
            $path = Upload::base64Upload($base64);
            if($path){
                $data = ShowCardModel::get(1);
//                $data = new StdClass;
                if(!$data){
                    $data = new ShowCardModel();
                }
                $data->id = 1;
                $data->image = $path;
                $data->createTime = $createTime;
                $data->updateTime = $createTime;
                $data->wellKnow = $wellKnow;
                $data->flag = 1;
                $data->save();
            }else{
                $backData['code'] = 1;
                $backData['message'] = '图片上传失败';
            }
        }else{
            $data = ShowCardModel::get(1);
            if(!$data){
                $data = new ShowCardModel();
            }
            $data->id = 1;
            $data->createTime = $createTime;
            $data->flag = 1;
            $data->updateTime = $createTime;
            $data->wellKnow = $wellKnow;
            $data->save();
        }
        $tmp = new WechatModel();
        $tmp->delFile(ROOT_PATH . 'public/' . DS . '/uploads/ewm/ewmlogo/');
        return $backData;
    }
    /**
     * CMS获取打卡图的数据
     */
    public function getData(){
        $data = ShowCardModel::get(1);
        $data['host'] = config('host');
        return json($data);
    }
}