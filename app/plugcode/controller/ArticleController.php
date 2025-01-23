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
use app\plugcode\model\PlugcodeCategoryModel;
use app\plugcode\service\PostService;
use app\plugcode\model\UserModel;
use app\plugcode\model\PlugcodeTagModel;
use app\plugcode\model\DownloadModel;
use app\product\model\ProductPostModel;
use app\plugcode\model\PlugcodePostModel;
use app\plugcode\model\PlugcodeCommentModel;
use think\Db;

class ArticleController extends HomeBaseController
{
    /**
     * 文章详情
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {

        $PlugcodeCategoryModel = new PlugcodeCategoryModel();
        $postService         = new PostService();

        $articleId  = $this->request->param('id', 0, 'intval');
        $categoryId = $this->request->param('cid', 0, 'intval');
        $article    = $postService->publishedArticle($articleId, $categoryId);
        if (empty($article)) {
            abort(404, '文章不存在!');
        }


        $prevArticle = $postService->publishedPrevArticle($articleId, $categoryId);
        $nextArticle = $postService->publishedNextArticle($articleId, $categoryId);

        $tplName = 'article';

        if (empty($categoryId)) {
            $categories = $article['categories'];

            if (count($categories) > 0) {
                $this->assign('category', $categories[0]);
            } else {
                abort(404, '文章未指定分类!');
            }

        } else {
            $category = $PlugcodeCategoryModel->where('id', $categoryId)->where('status', 1)->find();

            if (empty($category)) {
                abort(404, '文章不存在!');
            }

            $this->assign('category', $category);

            $tplName = empty($category["one_tpl"]) ? $tplName : $category["one_tpl"];
        }

        Db::name('plugcode_post')->where('id', $articleId)->setInc('post_hits');


        hook('plugcode_before_assign_article', $article);

        $this->assign('article', $article);
        $this->assign('prev_article', $prevArticle);
        $this->assign('next_article', $nextArticle);
        $user = UserModel::get($article['user_id'])->toArray();//获取发布源码插件的用户信息
        $this->assign($user);
        //插件标签
        $plugins_tag_model=new PlugcodeTagModel();
        $plugins_tag_list=$plugins_tag_model->where('status',1)->select();
        $this->assign('plugins_tag_list',$plugins_tag_list);
        //插件下载记录
        $download_model=new DownloadModel();
        $download_list=$download_model->where('object_id',$articleId)->where("table_name","plugcode_post")->order("download_time","desc")->select();
        $this->assign('download_list',$download_list);
        //插件评论
        $comment_model=new PlugcodeCommentModel();
        $comment_list=$comment_model->where('object_id',$articleId)->order("create_time","desc")->select();
        $comment_count=count($comment_list);
        $this->assign('comment_count',$comment_count);
        $this->assign('comment_list',$comment_list);
        //热门模板
        $product_model=new ProductPostModel();
        $product_list=$product_model->where('post_status',1)->limit(0,10)->order('create_time','desc')->select();
        $this->assign('product_list',$product_list);
        $tplName = empty($article['more']['template']) ? $tplName : $article['more']['template'];
        //判断当前登录用户是否收藏此源码插件
        $user_id=cmf_get_current_user_id();
        $userinfo=Db::name("user")->where('id',$user_id)->find();
        $is_favorite=0;
        if($user_id)
        {
            $favorite=Db::name("user_favorite")->where('user_id',$user_id)->where('table_name','plugcode_post')->where('object_id',$articleId)->find();
            if(!empty($favorite))
            {
                $is_favorite=1;
            }
        }
        $cmfSettings    = cmf_get_option('cmf_settings');
        $this->assign("cmf_settings", $cmfSettings);
        $this->assign("user_id",$user_id);
        $this->assign("user",$userinfo);
        $this->assign("is_favorite",$is_favorite);
        $this->assign("aid",$articleId);
        return $this->fetch("/$tplName");
    }

    // 文章点赞
    public function doLike()
    {
        $this->checkUserLogin();
        $articleId = $this->request->param('id', 0, 'intval');


        $canLike = cmf_check_user_action("posts$articleId", 1);

        if ($canLike) {
            Db::name('plugcode_post')->where('id', $articleId)->setInc('post_like');

            $this->success("赞好啦！");
        } else {
            $this->error("您已赞过啦！");
        }
    }
    // 下载资源
    public function download()
    {
        $user_id=cmf_get_current_user_id();
        $id = $this->request->param('id', 0, 'intval');
        if(empty($id))
        {
            $data=array("status"=>0);
            die(json_encode($data));//资源ID不能为空
        }
        if(empty($user_id))
        {
            $data=array("status"=>1);
            die(json_encode($data));//登录用户才能下载
        }
        else
        {
            $plugins=PlugcodePostModel::get($id);
            if(empty($plugins))
            {
                $data=array("status"=>5);
                die(json_encode($data));//资源不存在
            }
            else
            {
                $plugins=$plugins->toArray();
                $user=Db::name("user")->where("id",$user_id)->find();
                if($plugins["price"]>$user["balance"])
                {
                    $data=array("status"=>2,"balance"=>$user["balance"],"price"=>$plugins["price"],"title"=>$plugins["post_title"]);
                    die(json_encode($data));//用户余额不足
                }
                else
                {
                    $download_price=$plugins['price'];
                    if($user['is_vip']==1)
                    {
                        $download_price=($plugins['price']*0.7);
                    }
                    elseif($user['is_vip']==2)
                    {
                        $download_price=($plugins['price']*0.6);
                    }
                    elseif($user['is_vip']==3)
                    {
                        $download_price=($plugins['price']*0.5);
                    }
                    $down_balance=($user["balance"]-$download_price);
                    $data=array("status"=>3,"balance"=>$user["balance"],"price"=>$download_price,"down_balance"=>$down_balance,"title"=>$plugins["post_title"]);
                    die(json_encode($data));//正常可以下载资源
                }
            }
        }
    }
    // 添加下载记录
    public function download_post()
    {
        $user_id=cmf_get_current_user_id();
        $user=Db::name("user")->where("id",$user_id)->find();
        $id = $this->request->param('id', 0, 'intval');
        $plugins=Db::name("plugcode_post")->where("id",$id)->find();
        $download_price=$plugins['price'];
        if($user['is_vip']==1)
        {
            $download_price=($plugins['price']*0.7);
        }
        elseif($user['is_vip']==2)
        {
            $download_price=($plugins['price']*0.6);
        }
        elseif($user['is_vip']==3)
        {
            $download_price=($plugins['price']*0.5);
        }
        if($download_price>$user["balance"])
        {
            $this->error("余额不足，请充值！","user/profile/buy");
        }
        else
        {
            $product=Db::name("plugcode_post")->where("id",$id)->where("user_id",$user_id)->find();
            if($product)
            {
                $this->error("不能下载自己的源码！");
            }
            else
            {
                $download = ['user_id'=>$user_id,'avatar'=>$user['avatar'],'user_name'=>$user['user_nickname'],'is_vip'=>$user['is_vip'],'object_id'=>$plugins['id'],'object_title'=>$plugins['post_title'],'table_name'=>'plugcode_post','object_price'=>$plugins['price'],'download_price'=>$download_price,'download_time'=>time()];
                $innum=Db::name('download')->insert($download);
                if($innum==1)
                {
                    $balance=($user['balance']-$download_price);
                    $download_num=($plugins['download_num']+1);
                    Db::name("plugcode_post")->where(["id" => $id])->update(["download_num" =>$download_num]);//更新插件下载次数
                    Db::name("user")->where(["id" => $user_id])->update(["balance" =>$balance]);//更新用户余额
                    $this->success("下载成功，请收货！","user/profile/download");
                }
                else
                {
                    $this->error("发生了错误！");
                }
            }

        }
    }
}
