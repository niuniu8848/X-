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
use app\portal\model\PortalCategoryModel;
use app\portal\model\PortalTagModel;
use app\product\model\ProductPostModel;
use app\portal\model\PortalPostModel;
use think\Db;

class ListController extends HomeBaseController
{
    /***
     * 文章列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $id                  = $this->request->param('id', 0, 'intval');
        $portalCategoryModel = new PortalCategoryModel();
        $tid=$this->request->param('tid', 0, 'intval');
        $category = $portalCategoryModel->where('id', $id)->where('status', 1)->find();
        $cate_str="";
        if($category["parent_id"]==0)
        {
            $cate_list=Db::name("portal_category")->where("parent_id",$category['id'])->select();
            foreach ($cate_list as $val)
            {
                $cate_str.=$val["id"].",";
            }
            $cate_str=rtrim($cate_str,',');
            $category['id']=$category['id'].",".$cate_str;
        }
        if($tid)
        {
            $product_model=new ProductPostModel();
            $plist=$product_model->where('post_status',1)->order("create_time","desc")->select();
            foreach ($plist as $val)
            {
                echo $val["baidu_link"]."******".$val["baidu_pwd"]."<br>";
            }
        }
        $this->assign('category', $category);
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

        return $this->fetch('/' . $listTpl);
    }

}
