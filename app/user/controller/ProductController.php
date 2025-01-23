<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use app\product\service\ApiService;
use cmf\controller\UserBaseController;
use app\user\model\UserModel;
use app\user\model\UserClassModel;
use app\product\model\ProductPostModel;
use think\Db;
class ProductController extends UserBaseController
{
    /**
     * 个人中心我的文章列表
     */
    public function index()
    {
        $user = cmf_user_info();
        if($user['user_type'] !=3)
        {
            $this->error("你还不是商户！", "user/profile/center");
        }
        else
        {
            if($user['com_status']==0)
            {
                $this->error("未支付商户！", "user/profile/center");
            }
        }
        $articleModel = new PortalPostModel();
        $articles     = $articleModel->where(['user_id' => cmf_get_current_user_id(), 'delete_time' => 0])
            ->order('create_time DESC')->paginate();
        $this->assign($user);
        $this->assign("page", $articles->render());
        $this->assign("articles", $articles);
        return $this->fetch();
    }

    /**
     * 用户删除文章
     */
    public function delete()
    {
        $user = cmf_user_info();
        if($user['user_type'] !=3)
        {
            $this->error("你还不是商户！", "user/profile/center");
        }
        else
        {
            if($user['com_status']==0)
            {
                $this->error("未支付商户！", "user/profile/center");
            }
        }
        $id   = $this->request->param("id", 0, "intval");
        $delete = new UserModel();
        $data = $delete->deleteArticle($id);
        if ($data) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }

    /*
     * 用户发布文章
     */
    public function add()
    {
        $user = cmf_user_info();
        if($user['user_type'] !=3)
        {
            $this->error("你还不是商户！", "user/profile/center");
        }
        else
        {
            if($user['com_status']==0)
            {
                $this->error("未支付商户！", "user/profile/center");
            }
        }
        $class_list=UserClassModel::all();
        $this->assign('class_list',$class_list);
        return $this->fetch();
    }
    /*
    * 用户编辑文章
    */
    public function edit()
    {
        $user = cmf_user_info();
        if($user['user_type'] !=3)
        {
            $this->error("你还不是商户！", "user/profile/center");
        }
        else
        {
            if($user['com_status']==0)
            {
                $this->error("未支付商户！", "user/profile/center");
            }
        }
        $id = $this->request->param('id', 0, 'intval');

        $portalPostModel = new PortalPostModel();
        $post            = $portalPostModel->where('id', $id)->find();
        $look_user_ids=$post['look_user_ids'];
        if(is_string($look_user_ids))
        {
            $look_user_ids=explode(',',$look_user_ids);
        }
        $look_user_name='';
        if(is_array($look_user_ids))
        {
            foreach ($look_user_ids as $val)
            {
                $userModel=new UserModel();
                $users=$userModel->where('id',$val)->find();
                if(empty($users['user_nickname']))
                {
                    $look_user_name.=$users['mobile'].",";
                }
                else
                {
                    $look_user_name.=$users['user_nickname'].",";
                }

            }
        }
        $look_user_name=rtrim($look_user_name,',');
        $postCategories  = $post->categories()->alias('a')->column('a.name', 'a.id');
        $postCategoryIds = implode(',', array_keys($postCategories));
        $class_list=UserClassModel::all();
        $this->assign('class_list',$class_list);
        $this->assign('pid',$id);
        $this->assign('post', $post);
        $this->assign('post_categories', $postCategories);
        $this->assign('look_user_name',$look_user_name);
        $this->assign('post_category_ids', $postCategoryIds);
        return $this->fetch();
    }
    /*
    * 用户发布作品action
    */
    public function addPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $data['post']['post_status'] = 0;
            $data['post']['is_top'] = 0;
            $data['post']['recommended'] = 0;
            $data['post']['user_id'] = cmf_get_current_user_id();
            $data['post']['create_time'] = time();
            $data['post']['update_time'] = time();
            $data['post']['published_time'] = date('Y-m-d H:s',time());
            if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
                $data['post']['more']['photos'] = [];
                foreach ($data['photo_urls'] as $key => $url) {
                    $photoUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
                }
            }
            if (!empty($data['file_names']) && !empty($data['file_urls'])) {
                $data['post']['more']['files'] = [];
                foreach ($data['file_urls'] as $key => $url) {
                    $fileUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['files'], ["url" => $fileUrl, "name" => $data['file_names'][$key]]);
                }
            }
            if (!empty($data['file2_names']) && !empty($data['file2_urls'])) {
                $data['post']['more']['html'] = [];
                foreach ($data['file2_urls'] as $key => $url) {
                    $fileUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['html'], ["url" => $fileUrl, "name" => $data['file2_names'][$key]]);
                }
            }
            $portalPostModel = new ProductPostModel();
            $portal=$portalPostModel->userAddArticle($data['post'], $data['post']['category_id']);
            $data['post']['id'] = $portalPostModel->id;
            if($data['post']['id'])
            {
                //更新用户发布产品总数量
                $user=Db::name("user")->where("id",cmf_get_current_user_id())->find();
                $product_num=$user['product_num']+1;
                Db::name("user")->where("id",cmf_get_current_user_id())->update(["product_num"=>$product_num]);
                $this->success('添加成功!', url('user/profile/product'));
            }
            else
            {
                $this->error('添加失败!', url('user/profile/product'));
            }
        }
    }
    /**
     * 用户编辑源码提交
     * @adminMenu(
     *     'name'   => '编辑源码提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑源码提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $data['post']['post_status'] = 0;
            $data['post']['is_top'] = 0;
            $data['post']['recommended'] = 0;
            $data['post']['user_id'] = cmf_get_current_user_id();
            if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
                $data['post']['more']['photos'] = [];
                foreach ($data['photo_urls'] as $key => $url) {
                    $photoUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
                }
            }
            if (!empty($data['file_names']) && !empty($data['file_urls'])) {
                $data['post']['more']['files'] = [];
                foreach ($data['file_urls'] as $key => $url) {
                    $fileUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['files'], ["url" => $fileUrl, "name" => $data['file_names'][$key]]);
                }
            }
            if (!empty($data['file2_names']) && !empty($data['file2_urls'])) {
                $data['post']['more']['html'] = [];
                foreach ($data['file2_urls'] as $key => $url) {
                    $fileUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['html'], ["url" => $fileUrl, "name" => $data['file2_names'][$key]]);
                }
            }
            $portalPostModel = new ProductPostModel();
            $portalPostModel->userEditArticle($data['post'], $data['post']['category_id']);

            $this->success('保存成功!');

        }
    }
}