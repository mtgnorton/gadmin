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

class ReadCsvController extends Controller
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
      $this->display();

}
public function upload_file(){
   $upload = new \Think\Upload();// 实例化上传类
  $upload->maxSize   =     3145728 ;// 设置附件上传大小
  $upload->exts      =     array('csv');// 设置附件上传类型
  $upload->savePath  =      './'; // 设置附件上传目录    // 上传文件
  $info   =   $upload->upload();
  if(!$info) {
         $this->error($upload->getError());    }
         else
 {
   $file   =  $info['file'];

   $file_path_name  = C('csv_dir').$file['savepath'].$file['savename'];
   $dir_path_name   = C('csv_dir').$file['savepath'];
  if (is_file($file_path_name)) {

  $info = $this->handle_data($file_path_name);
  unlink($file_path_name);
  rmdir($dir_path_name);
  $temp =$info['msg'];
  $this->assign('info',$temp);
  $this->display();


   } else{
   $this->error( "文件不存在");
   }
}
}

  public function handle_data($file='')
  {

        $map = array();
        $readlist = array();
        $store = array();
        $line_number  = 0;
        $file  =fopen($file, "r");
      while ($data = fgetcsv($file)) {    //每次读取CSV里面的一行内容
        if($line_number == 0){ //跳过表头
                $line_number++;
                continue;
        }

       $data=array_iconv('gb2312','utf-8',$data);

        $temp = explode('至', $data['0']);
        if ($temp['0'] != $temp['1']){
          save_log('某个日期不正确'+implode('--', $insert_data));
          return array(
            'flag'=>0,
            'msg'=>"某个日期不正确，请重新上传");
        }
         if ($line_number == 1) {
          $map['time']  = $temp['0'];

          $readlist     = M("dataclick")->where($map)->select();
          foreach($readlist as $rk=> $rdata){
          $time = $rdata["time"];
          $account = $rdata["account"];
          $spread_plan = $rdata["spread_plan"];
          $show_number = $rdata["show_number"];
          $store[$time][$account][$spread_plan] = $show_number;

        }

        }

         $insert_data['time']           = $temp['0'];
         $insert_data['account']        = $data['1'];
         $insert_data['spread_plan']    = $data['2'];
         $insert_data['show_number']    = $data['3'];
         $insert_data['click_number']   = $data['4'];
         $insert_data['consume']        = $data['5'];

         if(!isset($store[$insert_data['time']][$insert_data['account']][$insert_data['spread_plan']])){
          $is_suc = $this->dataclickModel->add($insert_data);
          save_log('此数据已插入'.implode('_', $insert_data));
         }else if($store[$insert_data['time']][$insert_data['account']][$insert_data['spread_plan']] != $insert_data['click_number']){
          $is_suc = $this->dataclickModel->where()->save($insert_data);
          save_log('此数据已更新'.implode('_', $insert_data));
         }
         }




      return array(
            'flag'=>2,
            'msg'=>"文件读取成功");
     fclose($file);
  }


}
