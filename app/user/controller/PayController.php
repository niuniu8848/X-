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
use cmf\controller\UserBaseController;
use app\user\model\PayorderModel;
use app\user\model\UserCashModel;
use app\user\model\DownloadModel;
use think\Db;

class PayController extends UserBaseController
{

    /**
     * 会员充值中心首页
     */
    public function index()
    {
        return $this->fetch();
    }
    /**
     * 会员充值类型页面
     */
    public function paction()
    {
        $user_id=cmf_get_current_user_id();
        $trade_status=isset($_GET['trade_status'])?$_GET['trade_status']:'';
        $is_success=0;
        if(!empty($trade_status))
        {
            $is_success=1;
            $vip=isset($_GET['vip'])?$_GET['vip']:0;
            $out_trade_no=isset($_GET['out_trade_no'])?$_GET['out_trade_no']:'';
            $orders=Db::name("payorder")->where("order_no",$out_trade_no)->find();
            if($orders["is_success"]==0)
            {
                if($vip>0)
                {
                    $start_time=time();
                    $end_time=time();
                    if($vip==1)
                    {
                        $end_time=strtotime('+ 30 day');//30天月费会员
                    }
                    elseif($vip==2)
                    {
                        $end_time=strtotime('+ 365 day');//365天年费会员
                    }
                    elseif($vip==3)
                    {
                        $end_time=strtotime('+ 3650 day');//3650天终身会员
                    }
                    Db::name("user")->where("id",$user_id)->update(["is_vip"=>$vip,"vip_start_time"=>$start_time,"vip_end_time"=>$end_time]);
                    Db::name("payorder")->where("order_no",$out_trade_no)->update(["is_success"=>1]);
                }
                else
                {
                    $user=Db::name("user")->where("id",$user_id)->find();
                    $balance=$user["balance"]+$_GET["total_fee"];
                    Db::name("user")->where("id",$user_id)->update(["balance"=>$balance]);
                    Db::name("payorder")->where("order_no",$out_trade_no)->update(["is_success"=>1]);
                }
            }
        }
        else
        {
            $type=$this->request->post("product");
            $pid=$this->request->post("pid",0,"intval");
            if($type=="card")
            {
                $this->assign("order_no","YM158".time()."RN".mt_rand(100,999));
                $this->assign("member_type","余额充值");//余额充值
                $order_money=0;
                switch ($pid)
                {
                    case 1:
                        $order_money=500;
                        break;
                    case 2:
                        $order_money=300;
                        break;
                    case 3:
                        $order_money=200;
                        break;
                    case 4:
                        $order_money=100;
                        break;
                    case 5:
                        $order_money=50;
                        break;
                    default:
                        $order_money=500;
                        break;
                }
                $this->assign("vip_type",0);
                $this->assign("order_money",$order_money);
                $this->assign("product_name","充值余额".$order_money."元");
            }elseif ($type=="member")
            {
                $this->assign("order_no","YM158".time()."RN".mt_rand(100,999));
                $this->assign("member_type","会员升级");//会员升级
                $order_money=0;
                $product_name="";
                switch ($pid)
                {
                    case 5:
                        $order_money=380;
                        $product_name="终身会员";
                        $vip_type=3;
                        break;
                    case 6:
                        $order_money=180;
                        $product_name="年费会员";
                        $vip_type=2;
                        break;
                    case 7:
                        $order_money=50;
                        $product_name="月费会员";
                        $vip_type=1;
                        break;
                    default:
                        $order_money=380;
                        $product_name="终身会员";
                        $vip_type=0;
                        break;
                }
                $this->assign("vip_type",$vip_type);
                $this->assign("order_money",$order_money);
                $this->assign("product_name",$product_name);
            }
        }
        $this->assign("is_success",$is_success);
        return $this->fetch();
    }
    /*
     * 我的充值记录
     */
    public function recharge()
    {
        $orderModel = new PayorderModel();
        $orders     = $orderModel->where(['member_id' => cmf_get_current_user_id()])
            ->order('addtime DESC')->paginate(20);
        $this->assign("page", $orders->render());
        $this->assign("orders", $orders);
        return $this->fetch();
    }
    /*
    * 我的消费记录
    */
    public function operation()
    {
        $orderModel = new DownloadModel();
        $orders     = $orderModel->where(['user_id' => cmf_get_current_user_id()])
            ->order('download_time DESC')->paginate(20);
        $this->assign("page", $orders->render());
        $this->assign("orders", $orders);
        return $this->fetch();
    }
    /*
    * 提现
    */
    public function cashout()
    {
        $user=cmf_get_current_user();
        $this->assign($user);
        return $this->fetch();
    }
    /*
    * 用户提现
    */
    public function cashout_post()
    {
        $user_id=cmf_get_current_user_id();
        $user=Db::name("user")->where("id",$user_id)->find();
        $user_name=isset($user["user_nickname"])?$user["user_nickname"]:$user["user_login"];
        $cash_money=$this->request->post("cash_money",0);
        $cash_type=$this->request->post("cash_type",0);//0是支付宝
        $card_number=$this->request->post("card_number",'');
        $real_name=$this->request->post("real_name",'');
        $cash_bili=cmf_get_site_info()['site_cash_bili'];
        $service_charge=($cash_money*$cash_bili);
        if(100>$cash_money)
        {
            $this->error("提现金额不能小于100!");
        }
        if($cash_money>$user["balance"])
        {
            $this->error("提现金额不能大于余额!");
        }
        $innum=Db::name("user_cash")->insert([
            'trade_no'     => "ym".time(),
            'user_id'     => $user_id,
            'user_name'       => $user_name,
            'cash_money' => ($cash_money-$service_charge),
            'service_charge' => $service_charge,
            'cash_type' => $cash_type,
            'card_number'=> $card_number,
            'real_name'   => $real_name,
            'status'  => 0,
            'add_time' => time()
        ]);
        if($innum>0)
        {
            $balance=($user["balance"]-$cash_money);
            Db::name("user")->where(["id" => $user_id])->update(["balance" => $balance]);
            $this->success("提现成功,请关注提现状态！","user/pay/cashlist");
        }
    }
    /*
    * 我的充值记录
    */
    public function cashlist()
    {
        $orderModel = new UserCashModel();
        $orders     = $orderModel->where(['user_id' => cmf_get_current_user_id()])
            ->order('add_time DESC')->paginate(20);
        $this->assign("page", $orders->render());
        $this->assign("orders", $orders);
        return $this->fetch();
    }
}