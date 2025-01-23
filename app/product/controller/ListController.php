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
use app\product\model\ProductCategoryModel;
use app\product\model\ProductPostModel;
use app\product\model\ProductTypeModel;
use app\product\model\ProductStyleModel;
use app\product\model\ProductColorModel;
use app\product\model\ProductFrameModel;
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
        $tid                  = $this->request->param('tid', 0, 'intval');//模板类型ID
        $sid                  = $this->request->param('sid', 0, 'intval');//模板风格ID
        $yid                  = $this->request->param('yid', 0, 'intval');//颜色分类ID
        $fid                  = $this->request->param('fid', 0, 'intval');//前端框架ID
        $cc                   = $this->request->param('cc', '');//源码类型
        $ProductCategoryModel = new ProductCategoryModel();
        $category = $ProductCategoryModel->where('id', $cid)->where('status', 1)->find();
        $this->assign('category', $category);
        $listTpl = empty($category['list_tpl']) ? 'list' : $category['list_tpl'];
        //源码分类
        $product_cate_model=new ProductCategoryModel();
        $product_cate_list=$product_cate_model->where('status',1)->select();
        $this->assign('product_cate_list',$product_cate_list);
        //模板类型
        $product_type_model=new ProductTypeModel();
        $product_type_list=$product_type_model->where('status',1)->select();
        $this->assign('product_type_list',$product_type_list);
        //模板风格
        $product_style_model=new ProductStyleModel();
        $product_style_list=$product_style_model->where('status',1)->select();
        $this->assign('product_style_list',$product_style_list);
        //颜色分类
        $product_color_model=new ProductColorModel();
        $product_color_list=$product_color_model->where('status',1)->select();
        $this->assign('product_color_list',$product_color_list);
        //前端框架
        $product_frame_model=new ProductFrameModel();
        $product_frame_list=$product_frame_model->where('delete_time',0)->where("status",1)->select();
        $this->assign('product_frame_list',$product_frame_list);
        $product_model=new ProductPostModel();
        $where="post_status=1";
        if(!empty($cc))
        {
            $where.=" and code_class='".$cc."'";
        }
        if(!empty($cid))
        {
            $where.=" and category_id=".$cid;
        }
        if(!empty($tid))
        {
            $where.=" and type_id=".$tid;
        }
        if(!empty($sid))
        {
            $where.=" and type_id=".$sid;
        }
        if(!empty($yid))
        {
            $where.=" and color_id=".$yid;
        }
        if(!empty($fid))
        {
            $where.=" and frame_id=".$fid;
        }

        $product_list=$product_model->where($where)->order("create_time","desc")->paginate(20);
        $this->assign('product_list', $product_list);
        $this->assign("cid",$cid);
        $this->assign("tid",$tid);
        $this->assign("sid",$sid);
        $this->assign("yid",$yid);
        $this->assign("fid",$fid);
        // 在 render 前，使用appends方法保持分页条件
        $params=$this->request->param();
        $product_list->appends($params);
        $this->assign('page', $product_list->render());//单独提取分页出来
        return $this->fetch('/' . $listTpl);
    }

}
