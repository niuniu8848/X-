<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use app\portal\model\PortalTagModel;
use app\product\model\ProductPostModel;
use app\portal\model\PortalPostModel;
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

        $this -> assign("keyword", $keyword);
        //热门标签
        $portal_tag_model=new PortalTagModel();
        $portal_tag_list=$portal_tag_model->where('status',1)->limit(0,20)->order("post_count","desc")->select();
        $this->assign('portal_tag_list',$portal_tag_list);
        //热门模板
        $product_model=new ProductPostModel();
        $product_list=$product_model->where('post_status',1)->limit(0,10)->order("post_hits","desc")->select();
        $this->assign('product_list',$product_list);
        //热门文章
        $portal_model=new PortalPostModel();
        $portal_list=$portal_model->where('post_status',1)->where("post_type",1)->limit(0,10)->order("post_hits","desc")->select();
        $this->assign('portal_list',$portal_list);
        $listTpl = empty($category['list_tpl']) ? 'list' : $category['list_tpl'];
        return $this->fetch('/search');
    }
}
