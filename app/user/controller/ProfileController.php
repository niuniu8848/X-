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

use cmf\lib\Storage;
use think\Validate;
use think\Image;
use cmf\controller\UserBaseController;
use app\user\model\UserModel;
use app\product\model\ProductPostModel;
use app\product\model\ProductCategoryModel;
use app\product\model\ProductTypeModel;
use app\product\model\ProductStyleModel;
use app\product\model\ProductColorModel;
use app\product\model\ProductFrameModel;
use app\user\model\MessageModel;
use app\user\model\DownloadModel;
use think\Db;

class ProfileController extends UserBaseController
{

    /**
     * 会员中心首页
     */
    public function center()
    {
        $user = cmf_get_current_user();
        $this->assign($user);

        $userId = cmf_get_current_user_id();

        $userModel = new UserModel();
        $user      = $userModel->where('id', $userId)->find();
        $this->assign('user', $user);
        return $this->fetch();
    }

    /**
     * 编辑用户资料
     */
    public function edit()
    {
        $user = cmf_get_current_user();
        $this->assign($user);
        return $this->fetch('edit');
    }

    /**
     * 编辑用户资料提交
     */
    public function editPost()
    {
        if ($this->request->isPost()) {
            $validate = new Validate([
                'user_nickname' => 'max:32',
                'sex'           => 'between:0,2',
            ]);
            $validate->message([
                'user_nickname.max'   => lang('NICKNAME_IS_TO0_LONG'),
                'sex.between'         => lang('SEX_IS_INVALID'),
            ]);

            $data = $this->request->post();
            $data['mobile']=$this->request->post("user_mobile");
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $editData = new UserModel();
            if ($editData->editData($data)) {
                $this->success(lang('EDIT_SUCCESS'), "user/profile/edit");
            } else {
                $this->error(lang('NO_NEW_INFORMATION'));
            }
        } else {
            $this->error(lang('ERROR'));
        }
    }

    /**
     * 个人中心修改密码
     */
    public function password()
    {
        $user = cmf_get_current_user();
        $this->assign($user);
        return $this->fetch();
    }

    /**
     * 个人中心修改密码提交
     */
    public function passwordPost()
    {
        if ($this->request->isPost()) {
            $validate = new Validate([
                'old_password' => 'require|min:6|max:32',
                'password'     => 'require|min:6|max:32',
                'repassword'   => 'require|min:6|max:32',
            ]);
            $validate->message([
                'old_password.require' => lang('old_password_is_required'),
                'old_password.max'     => lang('old_password_is_too_long'),
                'old_password.min'     => lang('old_password_is_too_short'),
                'password.require'     => lang('password_is_required'),
                'password.max'         => lang('password_is_too_long'),
                'password.min'         => lang('password_is_too_short'),
                'repassword.require'   => lang('repeat_password_is_required'),
                'repassword.max'       => lang('repeat_password_is_too_long'),
                'repassword.min'       => lang('repeat_password_is_too_short'),
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            $login = new UserModel();
            $log   = $login->editPassword($data);
            switch ($log) {
                case 0:
                    $this->success(lang('change_success'));
                    break;
                case 1:
                    $this->error(lang('password_repeat_wrong'));
                    break;
                case 2:
                    $this->error(lang('old_password_is_wrong'));
                    break;
                default :
                    $this->error(lang('ERROR'));
            }
        } else {
            $this->error(lang('ERROR'));
        }

    }

    // 用户头像编辑
    public function avatar()
    {
        $user_id = cmf_get_current_user_id();
        $user=Db::name('user')->where('id',$user_id)->find();
        $this->assign($user);
        return $this->fetch();
    }
    // 用户头像上传
    public function avatar_post()
    {
        $user = cmf_get_current_user();
        if ($this->request->isPost()) {
            $avatar = $this->request->post('face');

            $login = new UserModel();
            $log   = $login->editAvatar($avatar);
            switch ($log) {
                case 0:
                    $this->success(lang('change_success'));
                    break;
                case 1:
                    $this->error('用户头像不能为空');
                    break;
                default :
                    $this->error(lang('ERROR'));
            }
        } else {
            $this->error(lang('ERROR'));
        }
    }
    // 实名认证
    public function certification()
    {
        $user_id = cmf_get_current_user_id();
        $user=Db::name('user')->where('id',$user_id)->find();
        $this->assign($user);
        return $this->fetch();
    }
    // 用户头像上传
    public function avatarUpload()
    {
        $file   = $this->request->file('file');
        $result = $file->validate([
            'ext'  => 'jpg,jpeg,png',
            'size' => 1024 * 1024
        ])->move(WEB_ROOT . 'upload' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR);

        if ($result) {
            $avatarSaveName = str_replace('//', '/', str_replace('\\', '/', $result->getSaveName()));
            $avatar         = 'avatar/' . $avatarSaveName;
            session('avatar', $avatar);

            return json_encode([
                'code' => 1,
                "msg"  => "上传成功",
                "data" => ['file' => $avatar],
                "url"  => ''
            ]);
        } else {
            return json_encode([
                'code' => 0,
                "msg"  => $file->getError(),
                "data" => "",
                "url"  => ''
            ]);
        }
    }

    // 用户头像裁剪
    public function avatarUpdate()
    {
        $avatar = session('avatar');
        if (!empty($avatar)) {
            $w = $this->request->param('w', 0, 'intval');
            $h = $this->request->param('h', 0, 'intval');
            $x = $this->request->param('x', 0, 'intval');
            $y = $this->request->param('y', 0, 'intval');

            $avatarPath = WEB_ROOT . "upload/" . $avatar;

            $avatarImg = Image::open($avatarPath);
            $avatarImg->crop($w, $h, $x, $y)->save($avatarPath);

            $result = true;
            if ($result === true) {
                $storage = new Storage();
                $result  = $storage->upload($avatar, $avatarPath, 'image');

                $userId = cmf_get_current_user_id();
                Db::name("user")->where("id", $userId)->update(["avatar" => $avatar]);
                session('user.avatar', $avatar);
                $this->success("头像更新成功！");
            } else {
                $this->error("头像保存失败！");
            }

        }
    }

    /**
     * 绑定手机号或邮箱
     */
    public function binding()
    {
        $user = cmf_get_current_user();
        $this->assign($user);
        return $this->fetch();
    }

    /**
     * 绑定手机号
     */
    public function bindingMobile()
    {
        if ($this->request->isPost()) {
            $validate = new Validate([
                'username'          => 'require|number|unique:user,mobile',
                'verification_code' => 'require',
            ]);
            $validate->message([
                'username.require'          => '手机号不能为空',
                'username.number'           => '手机号只能为数字',
                'username.unique'           => '手机号已存在',
                'verification_code.require' => '验证码不能为空',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $errMsg = cmf_check_verification_code($data['username'], $data['verification_code']);
            if (!empty($errMsg)) {
                $this->error($errMsg);
            }
            $userModel = new UserModel();
            $log       = $userModel->bindingMobile($data);
            switch ($log) {
                case 0:
                    $this->success('手机号绑定成功');
                    break;
                default :
                    $this->error('未受理的请求');
            }
        } else {
            $this->error("请求错误");
        }
    }

    /**
     * 绑定邮箱
     */
    public function bindingEmail()
    {
        if ($this->request->isPost()) {
            $validate = new Validate([
                'username'          => 'require|email|unique:user,user_email',
                'verification_code' => 'require',
            ]);
            $validate->message([
                'username.require'          => '邮箱地址不能为空',
                'username.email'            => '邮箱地址不正确',
                'username.unique'           => '邮箱地址已存在',
                'verification_code.require' => '验证码不能为空',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $errMsg = cmf_check_verification_code($data['username'], $data['verification_code']);
            if (!empty($errMsg)) {
                $this->error($errMsg);
            }
            $userModel = new UserModel();
            $log       = $userModel->bindingEmail($data);
            switch ($log) {
                case 0:
                    $this->success('邮箱绑定成功');
                    break;
                default :
                    $this->error('未受理的请求');
            }
        } else {
            $this->error("请求错误");
        }
    }
    /**
     * 更新用户认证资料
     */
    public function certificationPost()
    {
        $userId = cmf_get_current_user_id();
        $userModel = new UserModel();
        $user      = $userModel->where('id', $userId)->find();
        $real_name = $this->request->param('real_name', '');
        $user_mobile=$this->request->param('user_mobile', '');
        $qq=$this->request->param('qq', '');
        $id_card=$this->request->param('id_card', '');
        $card_photo=$this->request->param('card_photo', '');
        $alipay=$this->request->param('alipay', '');
        if($user['is_identification']==1)
        {
            $this->error("您的资料审核中！");
        }
        elseif($user['is_identification']==2)
        {
            $this->error("您的资料认证通过！");
        }
        else
        {

            Db::name("user")->where(["id" => $userId])->update(["real_name" => $real_name,"user_mobile"=>$user_mobile,"qq"=>$qq,"id_card"=>$id_card,"card_photo"=>$card_photo,'alipay'=>$alipay,'is_identification'=>1]);

            $this->success("资料添加成功，请等待审核！");
        }

    }
    /**
     * 用户添加源码
     */
    public function product()
    {
        $user_id=cmf_get_current_user_id();
        $product_model=new ProductPostModel();
        $product_list=$product_model->where("user_id",$user_id)->where("delete_time",0)->order("create_time","desc")->paginate(10);
        $this->assign('product_list', $product_list);
        $this->assign('page', $product_list->render());//单独提取分页出来
        return $this->fetch();
    }
    /**
     * 用户添加源码
     */
    public function add_product()
    {
        $product_model=new ProductPostModel();
        $product=$product_model->where("id",'>',0)->order("id",'desc')->select()->toArray();
        $pid=$product[0]['id']+1;
        $this->assign('pid', $pid);
        return $this->fetch();
    }
    /**
     * 用户删除源码
     */
    public function del_product()
    {
        $user_id=cmf_get_current_user_id();
        $id = $this->request->param('id', '');
        $id_arr=explode('`',$id);
        foreach ($id_arr as $v)
        {
            Db::name('product_post')->where('user_id',$user_id)->where('id',$v)->update(["delete_time" => time()]);
        }
        $this->success("删除成功！","user/profile/product");
    }
    /**
     * 用户编辑源码
     */
    public function edit_product()
    {
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
            $co=ProductFrameModel::get($v);
            $frame_str.=$co['name'].',';
        }
        $frame_str=rtrim($frame_str,',');
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
     * 用户消息记录
     */
    public function message()
    {
        $user_id=cmf_get_current_user_id();
        $message_model=new MessageModel();
        $message_list=$message_model->where("user_id",$user_id)->order("add_time","desc")->paginate(10);
        $this->assign('message_list', $message_list);
        $this->assign('page', $message_list->render());//单独提取分页出来
        return $this->fetch();
    }
    /**
     * 用户查看消息
     */
    public function look_message()
    {
        $id = $this->request->param('id', 0, 'intval');
        $message=MessageModel::get($id);
        if(!empty($message))
        {
            $this->assign('message', $message);
            $message->is_look=1;
            $message->look_time=time();
            $message->save();
        }
        return $this->fetch();
    }
    /**
     * 用户删除消息
     */
    public function del_message()
    {
        $user_id=cmf_get_current_user_id();
        $id = $this->request->param('id', '');
        $id_arr=explode('`',$id);
        foreach ($id_arr as $v)
        {
            Db::name('message')->where('user_id',$user_id)->where('id',$v)->delete();
        }
        $this->success("删除成功！","user/profile/message");
    }

    /**
     * 用户下载资源记录
     */
    public function download()
    {
        $user_id=cmf_get_current_user_id();
        $download_model=new DownloadModel();
        $download_list=$download_model->where("user_id",$user_id)->order("download_time","desc")->paginate(10);
        $this->assign('download_list', $download_list);
        $this->assign('page', $download_list->render());//单独提取分页出来
        return $this->fetch();
    }
    // 会员收货
    public function mycode()
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
            $down=DownloadModel::get($id);
            if(empty($down))
            {
                $data=array("status"=>2);
                die(json_encode($data));//资源不存在
            }
            else
            {
                $down=$down->toArray();
                $user=Db::name("user")->where("id",$user_id)->find();
                $code=Db::name($down["table_name"])->where("id",$down["object_id"])->find();
                if($code)
                {
                    $code["show_url"]=cmf_get_domain().cmf_url("product/article/show",array("id"=>$id));
                    $data=array("status"=>3,"baidu_link"=>$code["baidu_link"],"baidu_pwd"=>$code["baidu_pwd"],"show_pwd"=>$code["show_pwd"],"show_url"=>$code["show_url"]);
                    die(json_encode($data));//正常可以下载资源
                }
                else
                {
                    $data=array("status"=>2);
                    die(json_encode($data));//资源不存在
                }

            }
        }
    }
}