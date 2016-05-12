<?php
 	function is_login()
{

	$email 	=session('email');
	if (empty($email)) {
	redirect(U('Admin/Login/index'), 1, '请登录');
	}
}
   function curl_request($url){//$cookie='', $returnCookie=0,
        $curl   = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
}

/*
 *此处的作用是：
 *根据数据接口，封装数据，返回给前端
 *
 */
 function get_all_data($query_data='')
{
    $time                   = $query_data['date'];
    $s_time                 = strtotime($time);
    $one_day                = 60*60*24;
    for ($i=0; $i < 7; $i++) {
    $temp_time              = $s_time-$i*$one_day;
    $date_arr[$i]           = date('Ymd',$temp_time);#将7天的时间存储在数组中
    }
    $s_time                 = date('Ymd',$s_time);

    foreach ($date_arr as $key => $value) { #计算每一天的访问总量和今天的详细访问量

    $query_data['date']     = $value;
    $year                   = substr($value,0,4);
    $month                  = substr($value,4,2);
    $day                    = substr($value,6,2);
    $temp_date              = $month.'-'.$day;
    $query_string           = http_build_query($query_data);
    $url                    = 'http://wz.ghdfns.com/Service/query/getstatistics?'.$query_string;

    $data                   = curl_request($url);
    $data                   = json_decode($data);

    $sum                    = 0;

    if ($s_time == $value) {  #当时间为今天时，将今天访问的详细情况存储到re_data_one数组中

    foreach ($data as $key => $value) {

    $sum                    = $sum + (int)($value->countnums);
    if ($query_data['type'] == 'wxcode') { #根据访问的类型生成数据的site，对应网站名和微信号

    $re_data_one[]   = array('site'=>$value->wxcode,'number'=>$value->countnums);
    $sort_data[] = $value->countnums;
    }
    else{

    $re_data_one[]   = array('site'=>$value->site_url,'number'=>$value->countnums);
    $sort_data[] = $value->countnums;
    }
    array_multisort($sort_data,SORT_DESC,$re_data_one);  #将re_data_one数据以访问次数进行降序排列

    }
    $re_data_seven[$temp_date]        = $sum;
    }
    else{
    foreach ($data as $key => $value) {
    $sum                              = $sum + (int)($value->countnums);
     }
    $re_data_seven[$temp_date]        = $sum;
    }
    }
    $re_data_seven                =array_reverse($re_data_seven,true);
    $re_data['0']   = $re_data_one;
    $re_data['1']   = $re_data_seven;
    return $re_data;
}

    function array_iconv($in_charset,$out_charset,$arr){
            return eval('return '.iconv($in_charset,$out_charset,var_export($arr,true).';'));
    }
     function save_log($content='')
    {
        $filename = C('csv_dir').'log.txt';
        $time     = date('Y-m-d H:i:s');
        file_put_contents($filename, $time.'--'.$content."\n",FILE_APPEND);
    }