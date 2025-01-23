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

use app\admin\model\RouteModel;
use cmf\controller\AdminBaseController;
use app\product\model\ProductTypeModel;
use think\Db;
use app\admin\model\ThemeModel;


class AdminTypeController extends AdminBaseController
{
    /**
     * 源码模板类型列表
     * @adminMenu(
     *     'name'   => '模板类型管理',
     *     'parent' => 'product/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '源码模板类型列表',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $content = hook_one('product_admin_category_index_view');

        if (!empty($content)) {
            return $content;
        }
        $ProductTypeModel = new ProductTypeModel();
        $keyword             = $this->request->param('keyword');

        if (empty($keyword)) {
            $categoryTree = $ProductTypeModel->adminCategoryTableTree();
            $this->assign('category_tree', $categoryTree);
        } else {
            $categories = $ProductTypeModel->where('name', 'like', "%{$keyword}%")
                ->where('delete_time', 0)->select();
            $this->assign('categories', $categories);
        }

        $this->assign('keyword', $keyword);

        return $this->fetch();
    }

    /**
     * 添加源码模板类型
     * @adminMenu(
     *     'name'   => '添加源码模板类型',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加源码模板类型',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $content = hook_one('product_admin_category_add_view');

        if (!empty($content)) {
            return $content;
        }

        $parentId            = $this->request->param('parent', 0, 'intval');
        $ProductTypeModel = new ProductTypeModel();
        $categoriesTree      = $ProductTypeModel->adminCategoryTree($parentId);

        $themeModel        = new ThemeModel();
        $listThemeFiles    = $themeModel->getActionThemeFiles('product/List/index');
        $articleThemeFiles = $themeModel->getActionThemeFiles('product/Article/index');

        $this->assign('list_theme_files', $listThemeFiles);
        $this->assign('article_theme_files', $articleThemeFiles);
        $this->assign('categories_tree', $categoriesTree);
        return $this->fetch();
    }

    /**
     * 添加源码模板类型提交
     * @adminMenu(
     *     'name'   => '添加源码模板类型提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加源码模板类型提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $ProductTypeModel = new ProductTypeModel();

        $data = $this->request->param();

        $result = $this->validate($data, 'ProductType');

        if ($result !== true) {
            $this->error($result);
        }

        $result = $ProductTypeModel->addCategory($data);

        if ($result === false) {
            $this->error('添加失败!');
        }

        $this->success('添加成功!', url('AdminType/index'));
    }

    /**
     * 编辑源码模板类型
     * @adminMenu(
     *     'name'   => '编辑源码模板类型',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑源码模板类型',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {

        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $ProductTypeModel = new ProductTypeModel();
            $category            = $ProductTypeModel->get($id)->toArray();


            $categoriesTree = $ProductTypeModel->adminCategoryTree($category['parent_id'], $id);

            $themeModel        = new ThemeModel();
            $listThemeFiles    = $themeModel->getActionThemeFiles('product/List/index');
            $articleThemeFiles = $themeModel->getActionThemeFiles('product/Article/index');

            $routeModel = new RouteModel();
            $alias      = $routeModel->getUrl('product/List/index', ['id' => $id]);

            $category['alias'] = $alias;
            $this->assign($category);
            $this->assign('list_theme_files', $listThemeFiles);
            $this->assign('article_theme_files', $articleThemeFiles);
            $this->assign('categories_tree', $categoriesTree);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    /**
     * 编辑源码模板类型提交
     * @adminMenu(
     *     'name'   => '编辑源码模板类型提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑源码模板类型提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data = $this->request->param();
        $result = $this->validate($data, 'ProductType');

        if ($result !== true) {
            $this->error($result);
        }

        $ProductTypeModel = new ProductTypeModel();

        $result = $ProductTypeModel->editCategory($data);

        if ($result === false) {
            $this->error('保存失败!');
        }

        $this->success('保存成功!');
    }

    /**
     * 源码模板类型选择对话框
     * @adminMenu(
     *     'name'   => '源码模板类型选择对话框',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '源码模板类型选择对话框',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function select()
    {
        $ids                 = $this->request->param('ids');
        $selectedIds         = explode(',', $ids);
        $ProductTypeModel = new ProductTypeModel();

        $tpl = <<<tpl
<tr class='data-item-tr'>
    <td>
        <input type='checkbox' class='js-check' data-yid='js-check-y' data-xid='js-check-x' name='ids[]'
               value='\$id' data-name='\$name' \$checked>
    </td>
    <td>\$id</td>
    <td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
</tr>
tpl;

        $categoryTree = $ProductTypeModel->adminCategoryTableTree($selectedIds, $tpl);

        $categories = $ProductTypeModel->where('delete_time', 0)->select();

        $this->assign('categories', $categories);
        $this->assign('selectedIds', $selectedIds);
        $this->assign('categories_tree', $categoryTree);
        return $this->fetch();
    }

    /**
     * 源码模板类型排序
     * @adminMenu(
     *     'name'   => '源码模板类型排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '源码模板类型排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        parent::listOrders(Db::name('product_type'));
        $this->success("排序更新成功！", '');
    }

    /**
     * 源码模板类型显示隐藏
     * @adminMenu(
     *     'name'   => '源码模板类型显示隐藏',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '源码模板类型显示隐藏',
     *     'param'  => ''
     * )
     */
    public function toggle()
    {
        $data                = $this->request->param();
        $ProductTypeModel = new ProductTypeModel();
        $ids                 = $this->request->param('ids/a');

        if (isset($data['ids']) && !empty($data["display"])) {
            $ProductTypeModel->where('id', 'in', $ids)->update(['status' => 1]);
            $this->success("更新成功！");
        }

        if (isset($data['ids']) && !empty($data["hide"])) {
            $ProductTypeModel->where('id', 'in', $ids)->update(['status' => 0]);
            $this->success("更新成功！");
        }

    }

    /**
     * 删除源码模板类型
     * @adminMenu(
     *     'name'   => '删除源码模板类型',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除源码模板类型',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $ProductTypeModel = new ProductTypeModel();
        $id                  = $this->request->param('id');
        //获取删除的内容
        $findCategory = $ProductTypeModel->where('id', $id)->find();

        if (empty($findCategory)) {
            $this->error('模板类型不存在!');
        }
        //判断此模板类型有无子模板类型（不算被删除的子模板类型）
        $categoryChildrenCount = $ProductTypeModel->where(['parent_id' => $id, 'delete_time' => 0])->count();

        if ($categoryChildrenCount > 0) {
            $this->error('此模板类型有子类无法删除!');
        }

        $categoryPostCount = Db::name('product_category_post')->where('category_id', $id)->count();

        if ($categoryPostCount > 0) {
            $this->error('此模板类型有文章无法删除!');
        }

        $data   = [
            'object_id'   => $findCategory['id'],
            'create_time' => time(),
            'table_name'  => 'product_type',
            'name'        => $findCategory['name']
        ];
        $result = $ProductTypeModel
            ->where('id', $id)
            ->update(['delete_time' => time()]);
        if ($result) {
            Db::name('recycleBin')->insert($data);
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }
}
