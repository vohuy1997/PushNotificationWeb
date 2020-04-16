<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Web</title>
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  <style type="text/css">
    .layout_push{
      margin: 20px;
    }

    .table_device{
      margin-top: 20px;
      margin-right: : 20px;
      margin-left: : 20px;
    }
    div.page{
      margin-right: -15px;
      float: right;
    }
  </style>
</head>
<body >
  <div class="layout_push">
    <div class="container">
      <div class="form-group" style="margin-left: -15px;">
        <label for="sel1">Tên app:</label>
        <select class="form-control" id="sel1" style="width: auto; background: #006699; color: #ffffff;">
          <option>com.example.pushIos</option>
          <option>com.example.pushAndroid</option>
        </select>
      </div>
      <div class="row">
        <h2>Thông tin push</h2>
        <form class="form-horizontal">
          <div class="form-group">
            <label class="control-label col-xs-2">Tiêu đề:</label>
            <div class="col-xs-10">
              <input type="text" class="form-control" placeholder="Tiêu đề">
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-xs-2">Nội dung:</label>
            <div class="col-xs-10">
              <input type="text" class="form-control" placeholder="Nội dung">
            </div>   
          </div>
        </form>
      </div>
      <div class="page">
        <button id="btn_push" type="button" class="btn btn-info">Push All</button>
      </div>
    </div>
  </div>
  <div class="table_device">
    <div class="container">
      <div class="row">
        <h2>Thông tin các thiết bị đã kết nối</h2>
        <input class="form-control" id="myInput" type="text" placeholder="Search..">
        <br>
        <div class="table-responsive">    
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>STT</th>
                <th>Loại</th>
                <th>Tên thiết bị</th>
                <th>Hệ điều hành</th>
                <th>Phiên bản</th>
                <th>Ngày</th>
                <th style="text-align: center;">Action</th>
              </tr>
            </thead>
            <tbody id="myTable">
              <tr class="active">
                <td style="vertical-align: middle;">1</td>
                <td style="vertical-align: middle;">IOS</td>
                <td style="vertical-align: middle;">Iphone X</td>
                <td style="vertical-align: middle;">ios 13.2</td>
                <td style="vertical-align: middle;">1.0.0</td>
                <td style="vertical-align: middle;">19/02/2020</td>
                <td style="text-align: center;">
                  <button type="button" class="btn btn-info">Push</button>
                </td>
              </tr>
              <tr class="success">
                <td style="vertical-align: middle;">2</td>
                <td style="vertical-align: middle;">IOS</td>
                <td style="vertical-align: middle;">Iphone XR</td>
                <td style="vertical-align: middle;">ios 13.3</td>
                <td style="vertical-align: middle;">1.0.0</td>
                <td style="vertical-align: middle;">19/03/2020</td>
                <td style="text-align: center;">
                  <button type="button" class="btn btn-info">Push</button>
                </td>
              </tr>
              <tr class="active">
                <td style="vertical-align: middle;">3</td>
                <td style="vertical-align: middle;">Android</td>
                <td style="vertical-align: middle;">Samsung galaxy 20</td>
                <td style="vertical-align: middle;">os 10</td>
                <td style="vertical-align: middle;">1.0.1</td>
                <td style="vertical-align: middle;">29/02/2020</td>
                <td style="text-align: center;">
                  <button type="button" class="btn btn-info">Push</button>
                </td>
              </tr>
              <tr class="success">
                <td style="vertical-align: middle;">4</td>
                <td style="vertical-align: middle;">IOS</td>
                <td style="vertical-align: middle;">Iphone X</td>
                <td style="vertical-align: middle;">ios 13.2</td>
                <td style="vertical-align: middle;">1.0.0</td>
                <td style="vertical-align: middle;">19/02/2020</td>
                <td style="text-align: center;">
                  <button type="button" class="btn btn-info">Push</button>
                </td>
              </tr>
              <tr class="active">
                <td style="vertical-align: middle;">5</td>
                <td style="vertical-align: middle;">Android</td>
                <td style="vertical-align: middle;">Samsung galaxy 20</td>
                <td style="vertical-align: middle;">os 10</td>
                <td style="vertical-align: middle;">1.0.1</td>
                <td style="vertical-align: middle;">29/02/2020</td>
                <td style="text-align: center;">
                  <button type="button" class="btn btn-info">Push</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>   
      </div>
    </div>
    <div class="container">
      <div class="page">
        <ul class="pagination">
          <li><a href="#">&laquo;</a></li>
          <li class="active"><a href="#">1</a></li>
          <li class="disabled"><a href="#">2</a></li>
          <li class="disabled"><a href="#">3</a></li>
          <li class="disabled"><a href="#">4</a></li>
          <li class="disabled"><a href="#">5</a></li>
          <li><a href="#">&raquo;</a></li>
        </ul>
      </div>
    </div>
  </div>
  <script>
    $(document).ready(function(){
      $("#myInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });
    });
  </script>
</body>
</html>
