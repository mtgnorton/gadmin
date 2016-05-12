
    /*
     *此函数的作用是：
     *获得html页面中隐藏的跳转页面url
     *
     */
    function del_suffix (pre) {
      var temp = pre.indexOf('shtml');
          pre = pre.substr(0, temp - 1);
          return pre;
    }

        function count(){
        var numberArr   = [];
        var xArr        = [];
        var titleArr    = [];
        var sen_data    = [];
        var one_data    = [];
        var url         =   del_suffix($('#getChart').val());
        var type        =   $("#type").val();
        var path        =   $("#path").val();
        var time        =   $(".textbox-value").val();

        if (type == '') {
        sweetAlert('请输入类型1','','error');
        return;
        }
        if (path == '') {
        sweetAlert('请输入类型2','','error');
        return;
        }
        if (time == '') {
        time = $("input.textbox-text").val();
        if (time == '') {
        sweetAlert('请输入时间','','error');
        return;
        }

        }
        $.ajax({

            url :   url,
            type:   "GET",
            cache: false,
            datatype:'json',
            data:   {
            type    :  type,
            path    :  path,
            date    :  time,
            },
            beforeSend : function(){
            $(".mask").show();
            $(".bomb_box").show();
            },
            success: function (msg) {
            $(".mask").hide();
            $(".bomb_box").hide();
               msg = eval('('+msg+')');
               sen_data = msg.sen;
               one_data =msg.one;
               for(var p in sen_data){
               numberArr.push(parseInt(sen_data[p]));
               xArr.push(p);
                }

            mysqlSelectSortOptions.series[0].data       = numberArr;
            mysqlSelectSortOptions.xAxis.categories     = xArr;

            var chart = new Highcharts.Chart(mysqlSelectSortOptions);
            $("#mytable").empty();
            if (type == 'site') {
            var fname   = '网站名';
            }
            else{
            var fname  = '微信号';
            }
            month  = time.substr(0,2);
            day    = time.substr(3,2);
            show_time = month+'-'+day;
                $("#show_time").empty();
                $("#show_time").append('<h3>'+show_time+'详细信息</h3>');
                var text  = '<table class="table  table-bordered " id="dataTables-example">\n'+
                                        '<thead>\n'+
                                            '<tr>\n'+
                                            '<th><span class="label label-warning">序号</span></th>\n'+
                                         '<th><span class="label label-warning">'+fname+'</span></th>\n'+
                                         '<th><span class="label label-warning">访问总数</span></th>\n'+
                                        '</tr>\n'+
                                        '</thead>\n'+
                                        '<tbody>\n';
                for(var p in one_data){
                    text += '<tr class="odd gradeX">\n';
                    text +='<td>'+p+'</td>\n';
                    text += '<td>'+one_data[p].site+'</td>\n';
                    text += '<td>'+one_data[p].number+'</td>\n</tr>';
                }
                text +='</tbody>\n'+'</table>\n';

                $("#mytable").append(text);
                $('#dataTables-example').DataTable({
                    responsive: true
                });
            numberArr =  [];
            xArr      =  [];
            title     =  [];
            },

        });
    }

    /*
     *此处的作用是：
     *休眠操作1000代表一秒
     *
     */
    function sleep(milliSeconds) {
    var startTime = new Date().getTime();
    while (new Date().getTime() < startTime + milliSeconds);
    }

    function login() {

    var index_url   =  del_suffix($('#login_index').val());

     $.ajax({
            cache   : false,
            type    : "POST",
            url     : del_suffix($('#judge_login').val()),
            data    : {
            data    : $("#form").serializeArray(),
            },
            async   : false,
            error   : function (data) {

                sweetAlert(response.msg,'','error');
            },
            success : function (response) {

                if (response.flag == 1) {

                swal(response.msg,'','success');


                setTimeout(function(){
                window.location.href = index_url;
                },1000);

                }
                else{
               sweetAlert(response.msg,'','error');
               return;
                 }


            },

        });
}
     function get_query_data(){
        start_time        = $("#start_div > span >input.textbox-value").val();
        end_time          = $("#end_div > span >input.textbox-value").val();
        max_time          = $("#max_time").val();
        account     = $("#account").val();
        spread_plan     = $("#spread_plan").val();
        temp_title  = '';
        // if(start_time === '' && account === ''){
        //     sweetAlert('查询为空','','error');
        //     return;
        // }
        var pattern = /\d{2}\/\d{2}\/\d{4}/;
        if (start_time !== '') {
          if (!pattern.test(start_time)) {
            sweetAlert('开始时间格式不正确','','error');
            return;
        }
        }
        if (end_time !== '') {
          if (!pattern.test(end_time)) {
            sweetAlert('结束时间格式不正确','','error');
            return;
        }
        }

        url  = del_suffix($('#get_query_data').val());
         $.ajax({
                cache   : false,
                type    : "POST",
                url     : url,
                data    : {
                start_time    : start_time,
                end_time      : end_time,
                max_time      : max_time,
                account : account,
                spread_plan :spread_plan,
                },
                async   : false,
                error   : function (data) {

                    sweetAlert(response.msg,'','error');
                },
                success : function (response) {

                    response = eval('('+response+')');

                  query_data  = response['0'];
                    $("#mytable").empty();
                    $("#show_time").empty();

                $("#show_time").append('<h3>'+temp_title+'详细信息</h3>');
                var text  = '<table class="table  table-bordered " id="dataTables-example">\n'+
                                        '<thead>\n'+
                                            '<tr>\n'+
                                            '<th><span class="label label-warning">序号</span></th>\n'+

                                         '<th><span class="label label-warning">时间</span></th>\n'+
                                         '<th><span class="label label-warning">账户</span></th>\n'+
                                         '<th><span class="label label-warning">推广计划</span></th>\n'+
                                         '<th><span class="label label-warning">展示量</span></th>\n'+
                                         '<th><span class="label label-warning">点击量</span></th>\n'+
                                         '<th><span class="label label-warning">消费量</span></th>\n'+
                                           '<th><span class="label label-warning">点击率</span></th>\n'+
                                             '<th><span class="label label-warning">消费率</span></th>\n'+
                                        '</tr>\n'+
                                        '</thead>\n'+
                                      '<thead>\n'+
                                       '<tr style="background-color: orange">'+
                                         '<td>总和</td>\n'+
                                         '<td>——</td>\n'+
                                         '<td>——</td>\n'+
                                         '<td>——</td>\n'+
                                         '<td>'+response['1'].show_number_sum+'</td>\n'+
                                         '<td>'+response['1'].click_number_sum+'</td>\n'+
                                         '<td>'+response['1'].consume_sum+'</td>\n'+
                                         '<td>'+response['1'].CTR_sum +'%</td>\n'+
                                         '<td>'+response['1'].ave_con_sum + '%</td>\n'+
                                        '</tr>\n'+
                                        '</thead>\n'+
                                            '<tbody>\n';
                for(var p in query_data){
                    text += '<tr class="odd gradeX">\n';
                    text += '<td>'+(parseInt(p)+1)+'</td>\n';
                    text += '<td>'+query_data[p].time+'</td>\n';
                    text += '<td>'+query_data[p].account+'</td>\n';
                    text += '<td>'+query_data[p].spread_plan+'</td>\n';
                    text += '<td>'+query_data[p].show_number+'</td>\n';
                    text += '<td>'+query_data[p].click_number+'</td>\n';
                    text += '<td>'+query_data[p].consume+'</td>\n';
                    text += '<td>'+query_data[p].ctr+'%</td>\n';
                    text += '<td>'+query_data[p].ave_price+'%</td>\n</tr>';

                }

                text +='</tbody>\n'+'</table>\n';
                $("#f5").text(response['1'].show_number_sum);
                $("#f6").text(response['1'].click_number_sum);
                $("#f7").text(response['1'].consume_sum);
                $("#f8").text(response['1'].CTR_sum+'%');
                $("#f9").text(response['1'].ave_con_sum+'%');
                $("#mytable").append(text);
                $('#dataTables-example').DataTable({
                     language: {
      "sProcessing": "处理中...",
      "sLengthMenu": "显示 _MENU_ 项结果",
      "sZeroRecords": "没有匹配结果",
      "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
      "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
      "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
      "sInfoPostFix": "",
      "sSearch": "搜索:",
      "sUrl": "",
      "sEmptyTable": "表中数据为空",
      "sLoadingRecords": "载入中...",
      "sInfoThousands": ",",
      "oPaginate": {
        "sFirst": "首页",
        "sPrevious": "上页",
        "sNext": "下页",
        "sLast": "末页"
      },
      "oAria": {
        "sSortAscending": ": 以升序排列此列",
        "sSortDescending": ": 以降序排列此列"
      }
    },
                    responsive: true,
                    "aLengthMenu": [[10, 25, 50, 100, 200], ["10", "25", "50", "100", "200"]] ,
                });
                },

            });

    }