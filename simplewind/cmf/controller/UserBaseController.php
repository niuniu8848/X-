<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace cmf\controller;
use think\Db;
class UserBaseController extends HomeBaseController
{

    public function initialize()
    {
        parent::initialize();
        $this->checkUserLogin();
        $user_id=cmf_get_current_user_id();
        //统计用户消息记录数量
        $message=Db::name("message")->where("user_id",$user_id)->where("is_look",0)->select();
        $message_num=count($message);
        $this->assign("message_num",$message_num);
        //网站公告
        $notice_list=Db::name("portal_post")->where("category_id",5)->limit(0,1)->order("create_time","desc")->select();
        $this->assign("notice_list",$notice_list);
        //登录用户信息
        $user=Db::name("user")->where("id",$user_id)->find();
        $this->assign("userinfo",$user);
    }


}