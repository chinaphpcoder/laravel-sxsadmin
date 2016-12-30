<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>沙小僧API调试工具</title>
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
                                <h3>API调试工具</h3>
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
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="apiurl" class="col-sm-2 control-label">api地址</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="apiurl" name="apiurl">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="type" class="col-sm-2 control-label">请求类型</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="type" name="type">
                                    <option value="GET">GET</option>
                                    <option value="POST" selected="true">POST</option>
                                    <option value="PUT">PUT</option>
                                    <option value="DELETE">DELETE</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="authid" class="col-sm-2 control-label">AUTH_ID</label>
                            <div class="col-sm-10">
                            <input type="text" class="form-control" id="authid" name="authid"
                                placeholder="如果auth_id为空，则不加密" autocomplete="on">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">传入参数</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="data1" name="data1" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">返回结果</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="data2" name="data2" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="button" class="btn btn-default" onclick="apitest()">提交请求</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            function apitest() {
                var server = $("#server").val();
                var apiurl = $("#apiurl").val();
                var type = $("#type").val();
                var authid = $("#authid").val();
                var data = $("#data1").val();
                data = encodeURIComponent(data);
                apiurl = encodeURIComponent(apiurl);
                //alert(type);
                //alert(authid);
                //alert(data1);
                $.ajax({
                    headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' },
                    type:"POST",
                    url:"{{ route('server_appdebug') }}",
                    data:"server="+server+"&apiurl="+apiurl+"&type="+type+"&authid="+authid+"&data="+data,
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

        </script>
    </body>
</html>
