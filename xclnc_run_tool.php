<!DOCTYPE html>
<html lang="en">
<head>
  <title>RUN SQL TOOL</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css/bootstrap.min.css">
	<script src="/js/bootstrap.min.js"></script>
	<script src="/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="/js/jquery-3.2.1.min.js" ></script>
</head>
<body>

<div class="container">
  <h2>SQL</h2>
  <strong>RUN TO ==> 
  <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="shopid" name="shopid">
				  <option></option>
				  <option value="PRN">พิมพ์</option>
				  <option value="LMN">เคลือบ</option>
				  <option value="DRY">ดราย</option>
				  <option value="SLT">ผ่า</option>
				  <option value="MGN">ทำซอง</option>
	</select>
  </strong>
  <form>
    <div class="form-group">
      <label for="comment">SQL:</label>
      <textarea class="form-control" rows="10" id="sqlcmd"></textarea>
	  </br>
	  <button type="submit"  name="submit" class="btn btn-primary">RUN</button>
    </div>
  </form>
</div>

  <!-- <script type="text/javascript">	 -->
	<!-- $.getJSON('https://devwood.herokuapp.com/xclnc_run_tool_srv.php', function(data)  -->
	<!-- { -->
		<!-- alert("666"); -->
	<!-- }); -->
  <!-- </script> -->
   <div class="mypanel"></div>

    <script>
    $.getJSON('https://devwood.herokuapp.com/xclnc_run_tool_srv.php', function(data) {
        
        var text = `Date: ${data.date}<br>
                    Time: ${data.time}<br>
                    Unix time: ${data.milliseconds_since_epoch}`
                    
        
        $(".mypanel").html(text);
		alert("123");
    }).fail(function() {
    alert('11111')
  });
    </script>
</body>
</html>
