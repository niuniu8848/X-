<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\product\controller;

use cmf\controller\AdminBaseController;
use app\product\model\ProductPostModel;
use app\product\service\PostService;
use app\product\model\ProductCategoryModel;
use app\product\model\ProductTypeModel;
use app\product\model\ProductStyleModel;
use app\product\model\ProductColorModel;
use app\product\model\ProductFrameModel;
use think\Db;
use app\admin\model\ThemeModel;

class AdminArticleController extends AdminBaseController
{
    /**
     * 文章列表
     * @adminMenu(
     *     'name'   => '文章管理',
     *     'parent' => 'product/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章列表',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $content = hook_one('product_admin_article_index_view');

        if (!empty($content)) {
            return $content;
        }

        $param = $this->request->param();

        $categoryId = $this->request->param('category', 0, 'intval');

        $postService = new PostService();
        $data        = $postService->adminArticleList($param);

        $data->appends($param);

        $portalCategoryModel = new ProductCategoryModel();
        $categoryTree        = $portalCategoryModel->adminCategoryTree($categoryId);

        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('articles', $data->items());
        $this->assign('category_tree', $categoryTree);
        $this->assign('category', $categoryId);
        $this->assign('page', $data->render());


        return $this->fetch();
    }

    /**
     * 添加文章
     * @adminMenu(
     *     'name'   => '添加文章',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加文章',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $content = hook_one('product_admin_article_add_view');

        if (!empty($content)) {
            return $content;
        }
        $product_model=new ProductPostModel();
        $product=$product_model->where("id",'>',0)->order("id",'desc')->select()->toArray();
        $pid=$product[0]['id']+1;
        $this->assign('pid', $pid);
        $themeModel        = new ThemeModel();
        $articleThemeFiles = $themeModel->getActionThemeFiles('product/Article/index');
        $this->assign('article_theme_files', $articleThemeFiles);
        return $this->fetch();
    }

    /**
     * 添加文章提交
     * @adminMenu(
     *     'name'   => '添加文章提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加文章提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();

            //状态只能设置默认值。未发布、未置顶、未推荐
            $data['post']['post_status'] = 0;
            $data['post']['is_top']      = 0;
            $data['post']['recommended'] = 0;

            $post = $data['post'];

            $result = $this->validate($post, 'AdminArticle');
            if ($result !== true) {
                $this->error($result);
            }

            $portalPostModel = new ProductPostModel();

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
            $portalPostModel->adminAddArticle($data['post'], $data['post']['category_id']);

            $data['post']['id'] = $portalPostModel->id;
            $hookParam          = [
                'is_add'  => true,
                'article' => $data['post']
            ];
            hook('product_admin_after_save_article', $hookParam);


            $this->success('添加成功!', url('AdminArticle/edit', ['id' => $portalPostModel->id]));
        }

    }

    /**
     * 编辑文章
     * @adminMenu(
     *     'name'   => '编辑文章',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑文章',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $content = hook_one('product_admin_article_edit_view');

        if (!empty($content)) {
            return $content;
        }

        $id = $this->request->param('id', 0, 'intval');

        $portalPostModel = new ProductPostModel();
        $post            = $portalPostModel->where('id', $id)->find();
        //$postCategories  = $post->categories()->alias('a')->column('a.name', 'a.id');
        //$postCategoryIds = implode(',', array_keys($postCategories));
        $category=explode(',',$post['category_id']);//程序分类
        $cate_str='';
        foreach ($category as $v)
        {
            $cate=ProductCategoryModel::get($v);
            $cate_str.=$cate['name'].',';
        }
        $cate_str=rtrim($cate_str,',');
        $type=explode(',',$post['type_id']);//模板类型
        $type_str='';
        foreach ($type as $v)
        {
            $ty=ProductTypeModel::get($v);
            $type_str.=$ty['name'].',';
        }
        $type_str=rtrim($type_str,',');
        $style=explode(',',$post['style_id']);//模板风格
        $style_str='';
        foreach ($style as $v)
        {
            $st=ProductStyleModel::get($v);
            $style_str.=$st['name'].',';
        }
        $style_str=rtrim($style_str,',');
        $color=explode(',',$post['color_id']);//模板颜色
        $color_str='';
        foreach ($color as $v)
        {
            $co=ProductColorModel::get($v);
            $color_str.=$co['name'].',';
        }
        $color_str=rtrim($color_str,',');
        $frame=explode(',',$post['frame_id']);//前端框架
        $frame_str='';
        foreach ($frame as $v)
        {
            $fr=ProductFrameModel::get($v);
            $frame_str.=$fr['name'].',';
        }
        $frame_str=rtrim($frame_str,',');
        $themeModel        = new ThemeModel();
        $articleThemeFiles = $themeModel->getActionThemeFiles('product/Article/index');
        $this->assign('article_theme_files', $articleThemeFiles);
        $this->assign('post', $post);
        $this->assign('pid',$id);
        $this->assign('post_categories', $cate_str);
        $this->assign('post_type', $type_str);
        $this->assign('post_style', $style_str);
        $this->assign('post_color', $color_str);
        $this->assign('post_frame', $frame_str);
        return $this->fetch();
    }

    /**
     * 编辑文章提交
     * @adminMenu(
     *     'name'   => '编辑文章提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑文章提交',
     *     'param'  => ''
     * )
     * @throws \think\Exception
     */
    public function editPost()
    {

        if ($this->request->isPost()) {
            $data = $this->request->param();

            //需要抹除发布、置顶、推荐的修改。
            unset($data['post']['post_status']);
            unset($data['post']['is_top']);
            unset($data['post']['recommended']);

            $post   = $data['post'];
            $result = $this->validate($post, 'AdminArticle');
            if ($result !== true) {
                $this->error($result);
            }

            $portalPostModel = new ProductPostModel();

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
            $portalPostModel->adminEditArticle($data['post'], $data['post']['category_id']);

            $hookParam = [
                'is_add'  => false,
                'article' => $data['post']
            ];
            hook('product_admin_after_save_article', $hookParam);

            $this->success('保存成功!');

        }
    }

    /**
     * 文章删除
     * @adminMenu(
     *     'name'   => '文章删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章删除',
     *     'param'  => ''
     * )
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function delete()
    {
        $param           = $this->request->param();
        $portalPostModel = new ProductPostModel();

        if (isset($param['id'])) {
            $id           = $this->request->param('id', 0, 'intval');
            $result       = $portalPostModel->where('id', $id)->find();
            $data         = [
                'object_id'   => $result['id'],
                'create_time' => time(),
                'table_name'  => 'product_post',
                'name'        => $result['post_title'],
                'user_id'     => cmf_get_current_admin_id()
            ];
            $resultPortal = $portalPostModel
                ->where('id', $id)
                ->update(['delete_time' => time()]);
            if ($resultPortal) {
                Db::name('product_category_post')->where('post_id', $id)->update(['status' => 0]);
                Db::name('product_tag_post')->where('post_id', $id)->update(['status' => 0]);

                Db::name('recycleBin')->insert($data);
            }
            $this->success("删除成功！", '');

        }

        if (isset($param['ids'])) {
            $ids     = $this->request->param('ids/a');
            $recycle = $portalPostModel->where('id', 'in', $ids)->select();
            $result  = $portalPostModel->where('id', 'in', $ids)->update(['delete_time' => time()]);
            if ($result) {
                Db::name('product_category_post')->where('post_id', 'in', $ids)->update(['status' => 0]);
                Db::name('product_tag_post')->where('post_id', 'in', $ids)->update(['status' => 0]);
                foreach ($recycle as $value) {
                    $data = [
                        'object_id'   => $value['id'],
                        'create_time' => time(),
                        'table_name'  => 'product_post',
                        'name'        => $value['post_title'],
                        'user_id'     => cmf_get_current_admin_id()
                    ];
                    Db::name('recycleBin')->insert($data);
                }
                $this->success("删除成功！", '');
            }
        }
    }

    /**
     * 文章发布
     * @adminMenu(
     *     'name'   => '文章发布',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章发布',
     *     'param'  => ''
     * )
     */
    public function publish()
    {
        $param           = $this->request->param();
        $portalPostModel = new ProductPostModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');
            $portalPostModel->where('id', 'in', $ids)->update(['post_status' => 1, 'published_time' => time()]);
            $this->success("发布成功！", '');
        }

        if (isset($param['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');
            $portalPostModel->where('id', 'in', $ids)->update(['post_status' => 0]);
            $this->success("取消发布成功！", '');
        }

    }

    /**
     * 文章置顶
     * @adminMenu(
     *     'name'   => '文章置顶',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章置顶',
     *     'param'  => ''
     * )
     */
    public function top()
    {
        $param           = $this->request->param();
        $portalPostModel = new ProductPostModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $portalPostModel->where('id', 'in', $ids)->update(['is_top' => 1]);

            $this->success("置顶成功！", '');

        }

        if (isset($_POST['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $portalPostModel->where('id', 'in', $ids)->update(['is_top' => 0]);

            $this->success("取消置顶成功！", '');
        }
    }

    /**
     * 文章推荐
     * @adminMenu(
     *     'name'   => '文章推荐',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章推荐',
     *     'param'  => ''
     * )
     */
    public function recommend()
    {
        $param           = $this->request->param();
        $portalPostModel = new ProductPostModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $portalPostModel->where('id', 'in', $ids)->update(['recommended' => 1]);

            $this->success("推荐成功！", '');

        }
        if (isset($param['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $portalPostModel->where('id', 'in', $ids)->update(['recommended' => 0]);

            $this->success("取消推荐成功！", '');

        }
    }

    /**
     * 文章排序
     * @adminMenu(
     *     'name'   => '文章排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        parent::listOrders(Db::name('product_category_post'));
        $this->success("排序更新成功！", '');
    }
}
