<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/21
 * Time: 21:31
 */

namespace Admin\Controller;
use Think\Controller;

	/*
	 *此处的作用是：
	 *该页面属于老师
	 *
	 */

class ChartshowController extends Controller
{	

	
	public function index()
	{
		is_login();
	  	$time 			= date('m/d/Y',time());
	  	$show_time 		= date('m-d',time());
		$data['date']	= $time;
		$data['type']	= 'site';
		$data['path'] 	= 'fx';
		$data 			= get_all_data($data);
	  	$re_data 		= $data['1'];
	
	  	$re_data 		= json_encode($re_data);
	  	$one_data 		= $data['0'];

	  	$this->assign('one_data',$one_data);
	  	$this->assign('now_time',json_encode($time));
	  	$this->assign('show_time',$show_time);
	  	$this->assign('in_data',$re_data);
		$this->display();
	}
	public function getChart()
	{
					
	$query_data['type'] 	= I('get.type');
	$query_data['path']		= I('get.path');
	$query_data['date'] 	= I('get.date');		
	$data 					= get_all_data($query_data);
	$re_data['sen'] 		= $data['1'];
	$re_data['one'] 		= $data['0'];
	$re_data 	=	json_encode($re_data);
	$this->assign('re_data',$re_data);
	$this->display();
	}
	public function test($value='')
	{
		$data['date'] = '04/16/2016';
		$data['type'] = 'site';
		$data['path']  = "jf";	
		get_one_data($data);
	}
}