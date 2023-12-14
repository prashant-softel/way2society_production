<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Parking Type</title>
</head>

<?php include_once "ses_set_s.php"; ?>
<?php
if(isset($_SESSION['admin']))
{
  include_once("includes/header.php");
}
else
{
  include_once("includes/head_s.php");
}
?>
<?php
include_once("classes/utility.class.php");
include_once("classes/parkingType.class.php");
include_once("classes/include/dbop.class.php");
$obj_parkingType = new parkingType ($m_dbConn,$m_dbConnRoot);
$obj_utility = new utility($m_dbConn,$m_dbConnRoot);
/*echo "<pre>";
print_r($_SESSION);
echo "</pre>";*/
?> 

<html>
  <head>
    <title>lien</title>
    <link rel="stylesheet" type="text/css" href="css/pagination.css" >
      <script type="text/javascript" src="js/validate.js"></script>
      <script type="text/javascript" src="js/populateData.js"></script>
      <script type="text/javascript" src="js/ajax.js"></script>
      <script type="text/javascript" src="js/parkingType.js"></script>
   	  <script type="text/javascript">
       $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker
			({ 
            	dateFormat: "dd-mm-yy", 
            	showOn: "both", 
            	buttonImage: "images/calendar.gif", 
            	buttonImageOnly: true, 
				yearRange: '-10:+10', // Range of years to display in drop-down,
        	})
		});
	</script>
      <style>
        select.dropdown
        {
          position: relative;
          width: 100px;
          margin: 0 auto;
          padding: 10px 10px 10px 30px;
          appearance:button;
          /* Styles */
          background: #fff;
          border: 1px solid silver;
          /* cursor: pointer;*/
          outline: none;
        }
        @media print
        {    
          .no-print, .no-print *
          {
            display: none !important;
          }
          div.tr, div.td , div.th 
          {
            page-break-inside: avoid;
          }
        }
		#hide
        {
          display: none;
		  /*text-align: center;*/
        }
		/* Style the tab */
		<style>
		body {font-family: Arial;}

/* Style the tab */
		.tab 
		{
			float:left;
    		border: 1px solid #ccc;
			background-color:#337ab7;
		}

/* Style the buttons inside the tab */
		.tab button 
		{
    		background-color: inherit;
    		float: left;
    		border: 1px thin white;
    		outline: none;
    		cursor: pointer;
    		padding: 14px 16px;
    		transition: 0.3s;
    		font-size: 17px;
		}

/* Change background color of buttons on hover */
		.tab button:hover
		{
    		background-color: #ddd;
			color:#000000;
		}

/* Create an active/current tablink class */
		.tab button.active
		{
    		background-color: #ccc;
		}

/* Style the tab content */
	.tabcontent
	{
    	display: none;
    	padding: 6px 12px;
    	border: 1px solid #ccc;
    	border-top: none;
	}
    </style>
    
  </head>
  <body>
    <center>
        <br>
        <div class="panel panel-info" id="panel" style="display:none">
          <div class="panel-heading" id="pageheader">Parking Type</div>
            <form name="lien" id="lien" method="post" action="process/lien.process.php">
              <br>
              <div style="position:absolute, top: 10px, right:10px">
              	<?php
				if($obj_utility->checkAccess()==0)
				{
				?>
              	<button type="button" class="btn btn-primary" onClick="window.location.href='addParkingType.php'" style="float:right;margin-right:5%">Add Parking Type</button>
                <br>
                <br>
               	<?php
				}
				?>
          	</form>
            <br>
            <br>
            <div style="width:70%">
            <?php
				echo $obj_parkingType->pgnation();				
            ?>
            </div>
      	</div>
        </div>
<?php include_once "includes/foot.php"; ?>