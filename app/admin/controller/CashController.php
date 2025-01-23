<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\db\Query;

/**
 * Class UserController
 * @package app\admin\controller
 * @adminMenuRoot(
 *     'name'   => '管理组',
 *     'action' => 'default',
 *     'parent' => 'user/AdminIndex/default',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   => '',
 *     'remark' => '管理组'
 * )
 */
class CashController extends AdminBaseController
{

    /**
     * 会员申请提现列表
     */
    public function index()
    {
        /**搜索条件**/
        $status = trim($this->request->param('status',0));
        $username = $this->request->param('user_name');


        $cash_list = Db::name('user_cash')
            ->where('id', '>',0)
            ->where(function (Query $query) use ($username, $status) {
                if ($username) {
                    $query->where('user_name', 'like', "%$username%");
                }

                if ($status) {
                    $query->where('status',$status);
                }
            })
            ->order("id DESC")
            ->paginate(10);
        // 获取分页显示
        $page = $cash_list->render();
        $this->assign("status", $status);
        $this->assign("page", $page);
        $this->assign("cash_list", $cash_list);
        return $this->fetch();
    }

    /**
     * 申请提现删除
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');

        if (Db::name('user_cash')->delete($id) !== false) {
            $this->success("删除成功！",url("cash/index"));
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     * 付款提现
     */
    public function pay()
    {
        $id = $this->request->param('id', 0, 'intval');
        if (!empty($id)) {
            $result = Db::name('user_cash')->where(["id" => $id, "status" => 0])->setField('status', '1');
            if ($result !== false) {
                $this->success("确认付款成功！", url("cash/index"));
            } else {
                $this->error('确认付款失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }
}