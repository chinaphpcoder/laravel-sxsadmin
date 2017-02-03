<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>OFO密码共享</title>
        <!-- Latest compiled and minified CSS -->
        <link href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

        <!-- Optional theme -->
        <link href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" rel="stylesheet">

        <script src="//cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>

        <!-- Latest compiled and minified JavaScript -->
        <script src="//cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span12">
                    <form class="form-horizontal" role="form" action="" method="post">
                        <div class="form-group">
                            <div class="col-xs-12">
                                <h3>OFO密码共享</h3>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="bikenum" class="col-xs-3 control-label">车牌号</label>
                            <div class="col-xs-9">
                            <input type="text" class="form-control" id="bikenum" name="bikenum"
                                placeholder="车牌号" autocomplete="on">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bikepwd" class="col-xs-3 control-label">密码</label>
                            <div class="col-xs-9">
                            <input type="text" class="form-control" id="bikepwd" name="bikepwd"
                                placeholder="密码" autocomplete="on">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-6"><button type="button" class="btn btn-primary btn-block" onclick="getpwd()">获取密码</button></div>
                            <div class="col-xs-6"><button type="button" class="btn btn-primary btn-block" onclick="setpwd()">存储密码</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            function getpwd() {
                var num = $("#bikenum").val();
                $.ajax({
                    headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' },
                    type:"POST",
                    url:"{{ route('server_ofogetpwd') }}",
                    data:"num="+num,
                    success:function(data){
                        $("#bikepwd").val(data);
                    },
                    error:function(jqXHR, textStatus, errorThrown){
                        $("#bikepwd").val(jqXHR.responseText);
                    }
                });
            }
            function setpwd() {
                var num = $("#bikenum").val();
                var pwd = $("#bikepwd").val();
                $.ajax({
                    headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' },
                    type:"POST",
                    url:"{{ route('server_ofosetpwd') }}",
                    data:"num="+num+"&pwd="+pwd,
                    success:function(data){
                        if ( data == '1') {
                            alert('存储密码成功');
                        } else {
                            alert(data);
                        }
                    },
                    error:function(jqXHR, textStatus, errorThrown){
                        // alert(jqXHR.responseText);
                        // alert(jqXHR.status);
                        // alert(jqXHR.readyState);
                        // alert(jqXHR.statusText);
                        // alert(textStatus);
                        // alert(errorThrown);
                        $("#data1").val(jqXHR.responseText);
                    }
                });
            }
            $('#userid').blur(function () {
                if($('#userid').val() != '')
                {
                    $('#authid').attr("disabled",true);
                } else {
                    $('#authid').attr("disabled",false);
                }
                //alert($(this).val());
            });

            $('#authid').blur(function () {
                if($('#authid').val() != '')
                {
                    $('#userid').attr("disabled",true);
                } else {
                    $('#userid').attr("disabled",false);
                }
                //alert($(this).val());
            });
//                $.ajax({
//                    url : url,
//                    async : false, // 注意此处需要同步，因为返回完数据后，下面才能让结果的第一条selected
//                    type : "POST",
//                    dataType : "json",
//                    success : function(fields) {
//                        var rf = new Array();
//                        for ( var f in fields) {
//                            rf.push(fields[f].fieldName);
//                        }
//                        requiredCols = rf.join(",");
//                    }
//                });

               // alert(requiredCols );// (2)

           // }
        </script>
    </body>
</html>
