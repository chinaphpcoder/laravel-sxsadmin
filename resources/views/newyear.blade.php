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
                                <h3></h3>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="date" class="col-sm-1 control-label">数据日期</label>
                            <div class="col-sm-11">
                                <select class="form-control" id="date" name="date">
                                    @foreach ($dates as $value)
                                        @if ($value == $date)
                                            <option value="{{ $value }}" selected="selected">{{ $value }}</option>
                                        @else
                                            <option value="{{ $value }}">{{ $value }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <table class="table table-striped">
                                    <caption >抽奖信息</caption>
                                    <thead>
                                        <tr>
                                        <th>翻牌时间</th>
                                        <th>奖品名称</th>
                                        <th>用户第几次中奖</th>
                                        <th>目前第几次</th>
                                        <th>是否中奖</th>
                                        </tr>
                                        @foreach ($datas as $value)
                                            <tr >
                                            <td>{{$value['time']}}</td>
                                            <td>{{$value['prize_name']}}</td>
                                            <td>{{$value['rule']}}</td>
                                            <td>{{$value['num']}}</td>
                                            <td>{{$value['state']}}</td>
                                            </tr>
                                        @endforeach
                                    </thead>
                                </table>
                                <table class="table table-striped">

                                    <caption >邀请信息</caption>
                                    <thead>
                                        <tr>
                                        <th>被邀请人姓名</th>
                                        <th>被邀请人手机号</th>
                                        <th>注册时间</th>
                                        <th>投资时间</th>
                                        <th>投资期限</th>
                                        <th>投资金额</th>
                                        <th>返现金额</th>
                                        <th>邀请人姓名</th>
                                        <th>邀请人手机号</th>
                                        </tr>
                                        @foreach ($allv as $value)
                                            <tr >
                                            <td>{{$value['real_name']}}</td>
                                            <td>{{$value['mobile']}}</td>
                                            <td>{{$value['time']}}</td>
                                            <td>{{$value['in_time']}}</td>
                                            <td>{{$value['deal_term']}}</td>
                                            <td>{{$value['in_money']}}</td>
                                            <td>{{$value['money']}}</td>
                                            <td>{{$value['inv_real_name']}}</td>
                                            <td>{{$value['inv_mobile']}}</td>
                                            </tr>
                                        @endforeach
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $('#date').change(function () {
                if($('#date').val() != '')
                {
                    $date = $('#date').val();
                    self.location='/tools/newyear?date='+$date;
                }
            });
        </script>
    </body>
</html>
