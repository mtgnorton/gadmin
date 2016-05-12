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

class ShowCsvController extends Controller
{
  public   $model  ;
  public   $dataclickModel;

  public function _initialize($value='')
  {
    $this->model = M();
    $this->dataclickModel   = M('dataclick');
  }

  public function index($value='')
  {
      is_login();
      $email   = session('email');
      $this   -> assign('email',$email);
      $sql    = "select MAX(time) as max_time from mtg_dataclick";

      $max_time = $this->model->query($sql);
      $max_time = $max_time['0']['max_time'];
      $sql  = "select time,account,spread_plan,show_number,click_number,consume,(click_number/show_number) as CTR ,(consume/show_number) as ave_price  from mtg_dataclick where time='$max_time'";
      $today_data   = $this->model->query($sql);

    $sum_sql ="select SUM(show_number) as show_number_sum,SUM(click_number) as click_number_sum ,SUM(consume) as consume_num from mtg_dataclick where time='$max_time' ";


    $sum_data = $this->model->query($sum_sql);
    $re_sum_data['show_number_sum'] = $sum_data['0']['show_number_sum'];
    $re_sum_data['click_number_sum'] = $sum_data['0']['click_number_sum'];
    $re_sum_data['consume_sum']      = $sum_data['0']['consume_num'];
    $re_sum_data['CTR_sum']       =   $re_sum_data['click_number_sum']/ $re_sum_data['show_number_sum'];
     $re_sum_data['CTR_sum']   = sprintf("%.4f",  $re_sum_data['CTR_sum']) ;
    $re_sum_data['ave_con_sum']   =  $re_sum_data['consume_sum'] / $re_sum_data['show_number_sum'];
     $re_sum_data['ave_con_sum']   = sprintf("%.4f",  $re_sum_data['ave_con_sum']);
      $this->assign('sum_data',$re_sum_data);
      $this->assign('max_time',$max_time);
      $this->assign('today_data',$today_data);
      $this->display();

  }
  public function get_query_data($value='')
  {

    define('ONEDAY', 24*60*60);

    $start_time     = I('post.start_time');
    $end_time     = I('post.end_time');
    $max_time     = I('post.max_time');
    $start_time_unix = strtotime($start_time);
    $end_time_unix   = strtotime($end_time);
    $time_sql   = "and time in(";
    while ($start_time_unix <= $end_time_unix) {
    $temp_time  = date("Y-m-d",$start_time_unix);
    $time_sql .="'".$temp_time."',";
    $start_time_unix +=ONEDAY;
    }
    $time_sql   = substr($time_sql, 0,strlen($time_sql)-1);
    $time_sql  .=')';

    $account = I('post.account');
    $spread_plan = I('post.spread_plan');

    if (empty($start_time) && empty($end_time)) {
    $time_sql = "";//and time = '$max_time'
    }
    if (!empty($start_time) && empty($end_time)) {
    $query_start_time = date("Y-m-d",$start_time_unix);
    $time_sql = "and time= '$query_start_time'";
    }
    if (!empty($end_time) && empty($start_time)) {
    $query_end_time   = date('Y-m-d',$end_time_unix);
    $time_sql  = "and time = '$query_end_time'";
    }
    if (!empty($account)) {
    $where_sql  .=  "and account='$account'";
    }
    if (!empty($spread_plan)) {
    $where_sql  .=  "and spread_plan='$spread_plan'";
    }
    $sql  = "select time,account,spread_plan,show_number,click_number,consume,(round(click_number/show_number*100,2)) as CTR ,(round(consume/show_number*100,2)) as ave_price   from mtg_dataclick where 1 ".$where_sql.$time_sql."order by time desc";
    $sum_sql ="select SUM(show_number) as show_number_sum,SUM(click_number) as click_number_sum ,SUM(consume) as consume_num from mtg_dataclick where 1 ".$where_sql.$time_sql;
    $query_data = $this->model->query($sql);

    $sum_data = $this->model->query($sum_sql);
    $re_sum_data['show_number_sum'] = $sum_data['0']['show_number_sum'];
    $re_sum_data['click_number_sum'] = $sum_data['0']['click_number_sum'];
    $re_sum_data['consume_sum']      = $sum_data['0']['consume_num'];
    $re_sum_data['CTR_sum']       =   $re_sum_data['click_number_sum']/ $re_sum_data['show_number_sum'];
    $re_sum_data['CTR_sum']   = sprintf("%.4f",  $re_sum_data['CTR_sum'])*100 ;
    $re_sum_data['ave_con_sum']   =  $re_sum_data['consume_sum'] / $re_sum_data['show_number_sum'];
    $re_sum_data['ave_con_sum']   = sprintf("%.4f",  $re_sum_data['ave_con_sum'])*100;

    $this->ajaxReturn(
    json_encode(array(
      '0'=>$query_data,
      '1'=>$re_sum_data
      ))
      );
  }
}