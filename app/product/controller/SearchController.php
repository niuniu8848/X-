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
namespace app\product\controller;

use cmf\controller\HomeBaseController;
use app\product\model\ProductTagModel;
class SearchController extends HomeBaseController
{
    /**
     * 搜索
     * @return mixed
     */
    public function index()
    {
        $keyword = $this->request->param('keyword');

        if (empty($keyword)) {
            $this -> error("关键词不能为空！请重新输入！");
        }
        //源码标签
        $product_tag_model=new ProductTagModel();
        $product_tag_list=$product_tag_model->where('status',1)->select();
        $this->assign('product_tag_list',$product_tag_list);
        $this -> assign("keyword", $keyword);
        return $this->fetch('/search');
    }
}
