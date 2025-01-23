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
use app\plugcode\model\PlugcodePostModel;
use app\plugcode\model\PlugcodeCategoryModel;
use app\plugcode\model\PlugcodeEasyModel;
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
        $cid                  = $this->request->param('cid', 0, 'intval');//分类ID
        $eid                  = $this->request->param('eid', 0, 'intval');//模板类型ID
        $PluginsCategoryModel = new PlugcodeCategoryModel();

        $category = $PluginsCategoryModel->where('id', $cid)->where('status', 1)->find();
       
        $this->assign('category', $category);

        $listTpl = empty($category['list_tpl']) ? 'list' : $category['list_tpl'];
        //插件分类
        $product_cate_model=new PlugcodeCategoryModel();
        $product_cate_list=$product_cate_model->where('status',1)->select();
        $this->assign('product_cate_list',$product_cate_list);
        //难易程度
        $product_type_model=new PlugcodeEasyModel();
        $product_type_list=$product_type_model->where('status',1)->select();
        $this->assign('product_type_list',$product_type_list);
        $product_model=new PlugcodePostModel();
        $where="post_status=1";
        if(!empty($cid))
        {
            $where.=" and category_id like '".$cid.",%' or category_id like '%,".$cid."' or category_id like '%,".$cid.",%' or category_id=".$cid;
        }
        if(!empty($eid))
        {
            $where.=" and easy_id like '".$eid.",%' or easy_id like '%,".$eid."' or easy_id like '%,".$eid.",%' or easy_id=".$eid;
        }
        $product_list=$product_model->where($where)->order("create_time","desc")->paginate(20);
        $this->assign('product_list', $product_list);
        $params=$this->request->param();
        $product_list->appends($params);
        $this->assign('page', $product_list->render());//单独提取分页出来
        $this->assign("cid",$cid);
        $this->assign("eid",$eid);
        return $this->fetch('/' . $listTpl);
    }

}
