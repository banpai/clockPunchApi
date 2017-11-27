<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/26 0026
 * Time: 20:58
 */

namespace app\api\model;


use app\lib\exception\BannerMissException;
use think\Db;
use think\Exception;
use think\Model;
class Banner extends Model
{
    /**
     * 更改表
     */
    protected $table = 'category';
    public static function getBannerID($id){
        //TODO:根据bannerID 号获取Banner信息
//        try{
//            1/0;
//        }catch (Exception $ex){
//            //TODO:可以记录日志
//            throw $ex;
//        }
        // 数据库查询数据
        // $result = Db::query('select * from banner_item WHERE banner_id=?', [$id]);

        $result = Db::table('banner_item')->where('banner_id', '=', $id)->select();


        return $result;
    }
}