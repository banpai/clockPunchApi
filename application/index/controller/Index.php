<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        /**
         * 跳转到后台CMS
         */
        $url = '/view/clockpunch/cms/index_prod.html';
        return redirect($url);
    }
}
