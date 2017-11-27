<?php
/**
 * Created by 半拍.
 * User: 29423
 * Date: 2017/10/25 0025
 * Time: 23:32
 */

namespace app\api\controller\v1;

use app\api\validate\IDMustBePostivenInt;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BannerMissException;
use think\Exception;


class Banner
{
    /**
     *  获取指定id的banner信息
     * @url /banner/:id
     * @http GET
     * @id banner的id号
     */
    public function getBanner($id){
        (new IDMustBePostivenInt())->goCheck();
        $banner = BannerModel::get($id);
//        $banner = new BannerModel();
//        $banner = $banner->get($id);
        if(!$banner){
            throw new BannerMissException();
        }
        return $banner;
//        try{
//            $banner = BannerModel::getBannerID($id);
//        }catch (Exception $ex){
//            $err = [
//                'error_code' => 10001,
//                'msg' => $ex->getMessage()
//            ];
//            return json($err);
//        }
//        return $banner;
        // 独立验证
//        $data = [
//            'id' => $id
//        ];
//        // 验证器
//        $validate = new IDMustBePostivenInt();
//        $resualt = $validate->batch()->check($data);
//        var_dump($validate->getError());

    }
}