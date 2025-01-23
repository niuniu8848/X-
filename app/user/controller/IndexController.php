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

use app\user\model\UserModel;
use cmf\controller\HomeBaseController;
use app\product\model\ProductPostModel;
class IndexController extends HomeBaseController
{

    /**
     * 前台用户首页(公开)
     */
    public function index()
    {
        $id        = $this->request->param("id", 0, "intval");
        $sort        = $this->request->param("s", '', "string");
        $userModel = new UserModel();
        $user      = $userModel->where('id', $id)->find();
        if (empty($user)) {
            $this->error("查无此人！");
        }
        $pmodel=new ProductPostModel();
        $order="create_time";
        if($sort=='h')
        {
            $order="post_hits";
        }
        elseif($sort=='f')
        {
            $order="post_favorites";
        }
        elseif($sort=='d')
        {
            $order="download_num";
        }
        $plist=$pmodel->where("user_id",$id)->where("post_status",1)->order($order,"desc")->paginate(12);
        $this->assign('product_list', $plist);
        $params=$this->request->param();
        $plist->appends($params);
        $this->assign('page', $plist->render());//单独提取分页出来
        $this->assign($user->toArray());
        $this->assign('id',$id);
        $this->assign('s',$sort);
        $this->assign('user',$user);
        return $this->fetch(":index");
    }

    /**
     * 前台ajax 判断用户登录状态接口
     */
    function isLogin()
    {
        if (cmf_is_user_login()) {
            $this->success("用户已登录",null,['user'=>cmf_get_current_user()]);
        } else {
            $this->error("此用户未登录!");
        }
    }

    /**
     * 退出登录
    */
    public function logout()
    {
        session("user", null);//只有前台用户退出
        return redirect($this->request->root() . "/");
    }

}
