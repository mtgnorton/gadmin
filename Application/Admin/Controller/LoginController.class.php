<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/21
 * Time: 21:31
 */

namespace Admin\Controller;
use Think\Controller;
// use Common\Tool\T3;
	/*
	 *此处的作用是：
	 *该页面属于老师
	 *
	 */

class LoginController extends Controller
{


	public function index($course='',$tran_class='')
	{

	$this->display();
	}
	public function judge_login($value='')
	{
		$data 			= I('post.data');
		$email 			= $data['0']['value'];
		$password		= $data['1']['value'];

		$userModel 		= M('user');
		$password_d 		= $userModel->where("email='$email'")->getField('password');
		$judge_password 	= crypt($password,'mtg');
		if ($judge_password === $password_d) {
		session('email',$email);
		$this->ajaxReturn(
			array(
				'flag'=>1,
				'msg'=>'登录成功'
				)
			);
		}
		else{
		$this->ajaxReturn(
			array(
				'flag'=>0,
				'msg'=>'登录失败'
				)
			);
		}
		}
	public function logout($value='')
	{
		session(null);
		$this->success("退出成功",'index');
	}
	public function test($value='')
	{
		$t = crypt('mtg456','mtg');
		$m = crypt('mtg456','mtg');

	}
}