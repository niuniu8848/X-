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

use cmf\controller\UserBaseController;
use app\user\model\UserFavoriteModel;
use think\Db;

class FavoriteController extends UserBaseController
{

    /**
     * 个人中心我的收藏列表
     */
    public function index()
    {
        $userFavoriteModel = new UserFavoriteModel();
        $data              = $userFavoriteModel->favorites();
        $count=count($data['lists']);
        $user              = cmf_get_current_user();
        $this->assign($user);
        $this->assign('count',$count);
        $this->assign("page", $data['page']);
        $this->assign("lists", $data['lists']);
        return $this->fetch();
    }

    /**
     * 用户取消收藏
     */
    public function delete()
    {
        $id                = $this->request->param("id", 0, "intval");
        $id_arr=explode('`',$id);
        $i=0;
        foreach ($id_arr as $v)
        {
            $userFavoriteModel = new UserFavoriteModel();
            $data              = $userFavoriteModel->deleteFavorite($v);
            $i++;
        }

        if ($i>0) {
            $this->success("取消收藏成功！");
        } else {
            $this->error("取消收藏失败！");
        }
    }

    /**
     * 用户收藏
     */
    public function add()
    {
        $data   = $this->request->param();
        $result = $this->validate($data, 'Favorite');

        if ($result !== true) {
            $this->error($result);
        }

        $id    = $this->request->param('id', 0, 'intval');
        $table = $this->request->param('table');


        $findFavoriteCount = Db::name("user_favorite")->where([
            'object_id'  => $id,
            'table_name' => $table,
            'user_id'    => cmf_get_current_user_id()
        ])->count();

        if ($findFavoriteCount > 0) {
            $this->error("您已收藏过啦");
        }


        $title       = base64_decode($this->request->param('title'));
        $url         = $this->request->param('url');
        $url         = base64_decode($url);
        $description = $this->request->param('description', '', 'base64_decode');
        $description = empty($description) ? $title : $description;
        Db::name("user_favorite")->insert([
            'user_id'     => cmf_get_current_user_id(),
            'title'       => $title,
            'description' => $description,
            'url'         => $url,
            'object_id'   => $id,
            'table_name'  => $table,
            'create_time' => time()
        ]);

        $this->success('收藏成功');

    }
    /**
     * 用户ajax收藏
     */
    public function ajax_add()
    {
        $id    = $this->request->param('id', 0, 'intval');
        $table = $this->request->param('table');
        if(empty($id))
        {
            die('0');//收藏资源ID不能为空
        }
        $uid=cmf_get_current_user_id();
        if(empty($uid))
        {
            die('1');//只有登录用户才能收藏
        }
        if($table=='plugcode_post')
        {
            $plugins=Db::name("plugcode_post")->where("id",$id)->find();
        }
       else
       {
           $plugins=Db::name("product_post")->where("id",$id)->find();
       }
        if(empty($plugins))
        {
            die('2');//源码插件不存在
        }
        $findFavoriteCount = Db::name("user_favorite")->where([
            'object_id'  => $id,
            'table_name' => $table,
            'user_id'    => cmf_get_current_user_id()
        ])->count();

        if ($findFavoriteCount > 0) {
            die('3');//已经收藏过
        }


        $title       = $this->request->param('title');
        $url         = $this->request->param('url');
        $url         = base64_decode($url);
        $thumbnail = $this->request->param('thumbnail');
        $description = $this->request->param('description');
        $description = empty($description) ? $title : $description;
        $innum=Db::name("user_favorite")->insert([
            'user_id'     => cmf_get_current_user_id(),
            'title'       => $title,
            'thumbnail' => $thumbnail,
            'description' => $description,
            'url'         => $url,
            'object_id'   => $id,
            'table_name'  => $table,
            'create_time' => time()
        ]);
        if($innum>0)
        {
            $favorites_num=($plugins['post_favorites']+1);
            if($table=='plugcode_post')
            {
                Db::name("plugcode_post")->where(["id" => $id])->update(["post_favorites" =>$favorites_num]);//更新插件收藏次数
            }
            else
            {
                Db::name("product_post")->where(["id" => $id])->update(["post_favorites" =>$favorites_num]);//更新插件收藏次数
            }
            //更新用户收藏总数量
            $user=Db::name("user")->where("id",$uid)->find();
            $user_fnum=$user['favorite_num']+1;
            Db::name("user")->where("id",$uid)->update(["favorite_num"=>$user_fnum]);
            die('4');//收藏成功
        }
        else
        {
            die('5');//未知错误
        }

    }
    /**
     * 用户ajax删除收藏
     */
    public function ajax_del()
    {
        $id    = $this->request->param('id', 0, 'intval');
        $table = $this->request->param('table');
        if(empty($id))
        {
            die('0');//收藏资源ID不能为空
        }
        $uid=cmf_get_current_user_id();
        if(empty($uid))
        {
            die('1');//只有登录用户才能收藏
        }
        if($table=='plugcode_post')
        {
            $plugins=Db::name("plugcode_post")->where("id",$id)->find();
        }
        else
        {
            $plugins=Db::name("product_post")->where("id",$id)->find();
        }
        if(empty($plugins))
        {
            die('2');//源码插件不存在
        }

        $innum=Db::name("user_favorite")->where("object_id",$id)->where("table_name",$table)->where("user_id",$uid)->delete();
        if($innum>0)
        {
            die('6');//收藏成功
        }
        else
        {
            die('5');//未知错误
        }

    }
}