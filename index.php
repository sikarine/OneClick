<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<style type="text/css">
.bs-example {
    border-color: #e5e5e5 #eee #eee;
    border-style: solid;
    border-width: 1px 0;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.05) inset;
    margin: 0 -15px 15px;
    padding: 45px 15px 15px;
    position: relative;
}
.bs-example {
    background-color: #fff;
    border-color: #ddd;
    border-radius: 4px;
    border-width: 1px;
    box-shadow: none;
    margin-left: 0;
    margin-right: 0;
    margin-top: 40px;
}
</style>
<div class="container">
    <div class="bs-example">
    <form class="form-horizontal" enctype="multipart/form-data" method="post" action="report.php">
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Plan</label>
            <div class="col-sm-10">
                <input type="input" class="form-control" name="plan" value="">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Cost</label>
            <div class="col-sm-10">
                <input type="input" class="form-control" name="cost" value="">
            </div>
        </div>
        <div class="form-group">
            <label for="exampleInputFile" class="col-sm-2 control-label">CSV</label>
            <div class="col-sm-10">
                <input type="file" name="report">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-default">Submit</button>
            </div>
        </div>
    </form>
</div>
	<br><br>
</div>
