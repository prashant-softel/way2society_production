
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Change Log History</title>
</head>



<?php
include_once("includes/head_s.php");
include_once ("check_default.php");
include_once("classes/changelog.class.php");
include_once("classes/utility.class.php");
include_once("classes/dbconst.class.php");

$m_dbConnRoot = new dbop(true);
$obj_changeLog = new changeLog($m_dbConn);
$obj_utility = new utility($m_dbConn, $m_dbConnRoot);


$minGlobalCurrentYearStartDate = getDisplayFormatDate($obj_utility->getSocietyCreatedOpeningDate());
$startDate = date('d-m-Y', strtotime('-1 month'));
$endDate = date('d-m-Y');
$allUsers = $obj_utility->getSocietyAllLoginDetails(true, true);

$module = $logModulesArr;

?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/jsChangeLog.js"></script>
    <script type="text/javascript" src="js/populateData.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
    $(function(){

    minGlobalCurrentYearStartDate   = '<?php echo $minGlobalCurrentYearStartDate;?>'; 
    
    $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true,
			minDate: minGlobalCurrentYearStartDate,
			maxDate: '0',
        })});
	
	</script>
    
<style>
.desc{ text-align:left;}
.custom-position{
    
    position:relative;
    right: 10%;
}
.basics{
    width: 90% !important;
}

.error_msg{
    display: block;
    text-align: center;
    color: red;
    margin: 0px auto;
}


</style>    
</head>

<body>
<br>
<div id="middle">
<div class="panel panel-default">
<div class="panel-heading" id="pageheader">Change Log History</div>
<center><br>
<form name="ChengeLog" id="ChengeLog" method="post" action="">
<input type="hidden" name="ssid" value="<?php echo $_GET['ssid'];?>">
<table width="80%" style="border:1px solid black; background-color:transparent;border-collapse:collapse; padding:30px;">
<tr> <td colspan="4"><br/> </td></tr>
<tr> <td colspan="4"><span class="error_msg" ></span> </td></tr>
<tr> <td colspan="4"><br/> </td></tr>
<tr>
    <td style="text-align:left;width:10%;padding-left:20px">&nbsp; Start Date : &nbsp; </td><td class="custom-position"><input type="text" class="basics" name="start_date" id="start_date" value="<?php echo $startDate;?>" required></td>
    <td style="text-align:left;width:10%;">&nbsp; End Date : &nbsp; </td><td class="custom-position"><input type="text" class="basics" name="end_date" id="end_date" value="<?php echo $endDate;?>" required></td>
    
</tr>
<tr> <td colspan="4"><br/> </td></tr>
<tr>
	<td style=" text-align:left;width:40%;padding-left:20px"> &nbsp; Module : &nbsp; </td><td class="custom-position"><select name="module_name" id="module_name">
    <option value="0">All Modules</option>
    <?php 
    
    foreach ($module as $key => $value) {
        ?>
        <option value="<?=$key?>"><?=$value?></option>
    <?php }
    
    ?>
    </select> </td>
    <td style=" text-align:left;width:40%;"> &nbsp; User Name : &nbsp; </td><td class="custom-position"><select name="user_name" id="user_name">
    
    <option value="0">All Users</option>
     <?php
     
     foreach ($allUsers as $login_id => $user_name) { ?>
        <option value="<?=$login_id?>"><?=$user_name?></option> 
     <?php }
     
     
     ?>       
    </select> </td>
    
    
</tr>
<tr> <td colspan="4"><br/> </td></tr>
<tr> <td colspan="4" class="text-center"><input type="button" value="Fetch Log"  class="btn btn-primary" id="submit" name="submit" onClick="SubmitForm();"><td></tr>
<tr> <td colspan="4"><br/> </td></tr>

</table>    
    
</form>
<br>
<div id="FilterData">   
</div> 
<script>document.getElementById('example').style.width='90%';</script>    
</center>
</div>
</div>
<?php include_once "includes/foot.php"; ?>
<script>
$(document).ready(function() {
    $('#example').dataTable().fnDestroy();
			$('#example').DataTable( {
				dom: 'T<"clear">lfrtip',
				"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
				aaSorting : [],
				 "aoColumns": [
				{ "width": "12%","sClass" : "desc"},
				{ "width": "10%","sClass" : "desc"},
				{ "width": "20%","sClass" : "desc"},
				{ "width": "10%","sClass" : "desc" },
				{ "width": "10%"}
			  ]
				 
			} );
	
    } );
</script>
