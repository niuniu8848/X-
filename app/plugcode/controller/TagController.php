<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\plugcode\controller;

use cmf\controller\HomeBaseController;
use app\plugcode\model\PlugcodeTagModel;

class TagController extends HomeBaseController
{
    /**
     * 标签
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $id             = $this->request->param('id');

        $PluginsTagModel = new PlugcodeTagModel();

        if(is_numeric($id)){
            $tag = $PluginsTagModel->where('id', $id)->where('status', 1)->find();
        }else{
            $tag = $PluginsTagModel->where('name', $id)->where('status', 1)->find();
        }


        if (empty($tag)) {
            abort(404, '标签不存在!');
        }

        $this->assign('tag', $tag);

        return $this->fetch('/tag');
    }

}
