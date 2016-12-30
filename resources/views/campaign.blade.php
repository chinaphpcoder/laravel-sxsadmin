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
        <div class ="tableContainer">
            <input id ="currentPage" type ="hidden" value ="@ViewData['currentPage']"/>
            <input id ="totalPages" type ="hidden" value ="@ViewData['totalPages']" />
            <table class ="table table-hover table-striped">
                <thead>
                    <tr>
                        <th class ="col-md-4 text-center"> 乘车码 </th>
                        <th class ="col-md-4 text-center"> 订单号 </th>
                        <th class ="col-md-4 text-center"> 订单日期 </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach( var item in Model)
                    {
                        <tr>
                            <td> @item.BusNo </ td>
                            <td> @item.OrderId </ td>
                            <td> @item.OrderDate </ td>
                        </tr>
                    }
                </tbody>
            </table>
            <ul id ="example"></ul>
        </div>
    </body>
</html>
