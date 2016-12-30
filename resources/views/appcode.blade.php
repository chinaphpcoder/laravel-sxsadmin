<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>沙小僧API加密解密工具</title>
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
                            <div class="col-sm-offset-2 col-sm-10">
                                <h3>API加密解密调试工具</h3>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="server" class="col-sm-2 control-label">数据库</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="server" name="server">
                                    <option value="1">230服务器</option>
                                    <option value="2">线上服务器</option>
                                    <option value="3">测试服务器</option>
                                    <option value="4">测试服务器二</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="type" class="col-sm-2 control-label">加密方向</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="type" name="type">
                                    <option value="1">服务器→APP(服务器返回)</option>
                                    <option value="2">APP→服务器(APP发送)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="userid" class="col-sm-2 control-label">USER_ID</label>
                            <div class="col-sm-10">
                            <input type="text" class="form-control" id="userid" name="userid"
                                placeholder="USER_ID、AUTH_ID只填一个,USER_ID对应多条AUTH_ID时自动选择最后添加的一条" autocomplete="on">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="authid" class="col-sm-2 control-label">AUTH_ID</label>
                            <div class="col-sm-10">
                            <input type="text" class="form-control" id="authid" name="authid"
                                placeholder="USER_ID、AUTH_ID只填一个" autocomplete="on">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">加密数据</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="data1" name="data1" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">明文数据</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="data2" name="data2" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="button" class="btn btn-default" onclick="decode()">解密</button>
                                <button type="button" class="btn btn-default" onclick="encode()">加密</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            function decode() {
                var server = $("#server").val();
                var type = $("#type").val();
                var userid = $("#userid").val();
                var authid = $("#authid").val();
                var data = $("#data1").val();
                data = encodeURIComponent(data);
                //alert(type);
                //alert(authid);
                //alert(data1);
                $.ajax({
                    headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' },
                    type:"POST",
                    url:"{{ route('server_appdecode') }}",
                    data:"server="+server+"&type="+type+"&userid="+userid+"&authid="+authid+"&data="+data,
                    success:function(data){
                        $("#data2").val(data);
                    },
                    error:function(jqXHR, textStatus, errorThrown){
                        // alert(jqXHR.responseText);
                        // alert(jqXHR.status);
                        // alert(jqXHR.readyState);
                        // alert(jqXHR.statusText);
                        // alert(textStatus);
                        // alert(errorThrown);
                        $("#data2").val(jqXHR.responseText);
                    }
                });
            }
            function encode() {
                var server = $("#server").val();
                var type = $("#type").val();
                var userid = $("#userid").val();
                var authid = $("#authid").val();
                var data = $("#data2").val();
                data = encodeURIComponent(data);
                $.ajax({
                    headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' },
                    type:"POST",
                    url:"{{ route('server_appencode') }}",
                    data:"server="+server+"&type="+type+"&userid="+userid+"&authid="+authid+"&data="+data,
                    success:function(data){
                        $("#data1").val(data);
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
