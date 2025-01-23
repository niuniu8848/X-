<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use app\user\model\CommentModel;
use cmf\controller\UserBaseController;
use app\user\model\UserModel;
use think\Db;


class CommentController extends UserBaseController
{
    /**
     * 个人中心我的评论列表
     */
    public function index()
    {
        $user = cmf_get_current_user();

        $commentModel = new CommentModel();
        $comments     = $commentModel->where(['user_id' => cmf_get_current_user_id(), 'delete_time' => 0])
            ->order('create_time DESC')->paginate();
        $this->assign($user);
        $this->assign("page", $comments->render());
        $this->assign("comments", $comments);
        return $this->fetch();
    }

    /**
     * 用户删除评论
     */
    public function delete()
    {
        $id   = $this->request->param("id", 0, "intval");
        $delete = new UserModel();
        $data = $delete->deleteComment($id);
        if ($data) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     * 用户ajax发表评论
     */
    public function add_comment()
    {
        $id    = $this->request->param('id', 0, 'intval');
        $table = $this->request->param('table');
        if(empty($id))
        {
            die('0');//评论资源ID不能为空
        }
        $uid=cmf_get_current_user_id();
        $user=Db::name("user")->where("id",$uid)->find();
        if(empty($uid))
        {
            die('1');//只有登录用户才能评论
        }
        if($table=="plugcode_post")
        {
            $plugins=Db::name("plugcode_post")->where("id",$id)->find();
        }
        elseif($table=="product_post")
        {
            $plugins=Db::name("product_post")->where("id",$id)->find();
        }
        if(empty($plugins))
        {
            die('2');//源码插件不存在
        }

        $content = $this->request->param('content','');
        if($table=="plugcode_post")
        {
            $innum=Db::name("plugcode_comment")->insert([
                'user_id'     => cmf_get_current_user_id(),
                'object_id'       => $id,
                'full_name' => $user["user_nickname"],
                'content' => $content,
                'table_name'  => $table,
                'avatar'  => $user["avatar"],
                'create_time' => time()
            ]);
        }
        elseif($table=="product_post")
        {
            $innum=Db::name("product_comment")->insert([
                'user_id'     => cmf_get_current_user_id(),
                'object_id'       => $id,
                'full_name' => $user["user_nickname"],
                'content' => $content,
                'table_name'  => $table,
                'avatar'  => $user["avatar"],
                'create_time' => time()
            ]);
        }

        if($innum>0)
        {
            die('3');//评论成功
        }
        else
        {
            die('4');//未知错误
        }

    }
}