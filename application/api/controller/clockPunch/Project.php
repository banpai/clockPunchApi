<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/14 0014
 * Time: 16:50
 */

namespace app\api\controller\clockPunch;

use app\api\model\Project as ProjectModel;
use app\lib\exception\ProjectMissException;

class Project
{
    /*
     * 获取数据
     */
    public function getData(){
        $dom = new ProjectModel();
        $data = $dom->getProject();
        if(!$data){
            throw new ProjectMissException();
        }
        return $data;
    }
    /*
     * 上传数据
     */
    public function putData(){
        $getData = input();
        $dom = new ProjectModel();
        $data = $dom->changeProject($getData);
        return $data;
    }
    /*
     * 上传图片
     */
    public function uploadImg(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
//                echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
//                echo '/uploads/' . $info->getSaveName();
                $data = [
                    'url' => '/uploads/' . $info->getSaveName(),
                    'name' => $info->getFilename()
                ];
                return json($data);
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
//                echo $info->getFilename();
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }
}