<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - SMS Allotment</title>
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
include_once("classes/smsQuota.class.php");
include_once("classes/include/dbop.class.php");
$obj_smsQuota = new smsQuota($m_dbConn,$m_dbConnRoot);
/*echo "<pre>";
print_r($_SESSION);
echo "</pre>";*/
$clientId = $_SESSION['client_id'];
?> 

<html>
  <head>
    <title>viewSMSQuota</title>
    <link rel="stylesheet" type="text/css" href="css/pagination.css" >
      <script type="text/javascript" src="js/validate.js"></script>
      <script type="text/javascript" src="js/populateData.js"></script>
      <script type="text/javascript" src="js/ajax.js"></script>
      <script type="text/javascript" src="js/viewSMSQuota.js"></script>
      <script type="text/javascript">
				
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
          <div class="panel-heading" id="pageheader">SMS Allotment</div>
            <form name="viewSMSQuota" id="viewSMSQuota" method="post" action="#">
              <br>
              <?php
			  	//if($_SESSION['client_id'] <> 0)
				if($_SESSION['login_id'] == "4")
				{
			  ?>
              		<div style="position:absolute, top: 10px, right:10px"><button type="button" class="btn btn-primary" onClick="window.location.href='addSMSQuota.php'" style="float:right;margin-right:5%">Add SMS Quota</button> </div>
              <?php
				}
				?>
			  <br>
              <br>
              <div>
              	<table>
                	<tr>
                    	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Select Society&nbsp;:&nbsp;</b></td>
						<td width="10%" style="padding-top:1%"></td>
						<td width="50%" style="padding-top:1%">
                        	<input type="hidden" id="socId" name="socId" value="<?php echo $societyId;?>"/>
                			<select id="societyId" name="societyId" onChange="getSMSQuotaDetails()">
                            	<?php
								if($_SESSION['login_id'] == "4")
								{
									echo $obj_smsQuota->comboboxForSociety("SELECT `society_id`, `society_name` FROM `society`","0");
								}
								else
								{
									echo $obj_smsQuota->comboboxForSociety("SELECT `society_id`, `society_name` FROM `society` where `client_id` =".$clientId,"0");
								}
								?>
                            </select>
            			</td>
                    </tr>
                </table>
                </div>
        	</form>
            <br>
           	<div id="smsDetails">
           		<table id="example" class="display" cellspacing="0" width="100%">
            		<?php
					if(isset($_SESSION['SMSSocietyId']))
					{
						echo $obj_smsQuota->pgnation($_SESSION['SMSSocietyId']);
					}
					else
					{
						echo $obj_smsQuota->pgnation("0");
					}
					?>
              </table>        	
           	</div>
            <br>
       	</div>
   </center>
 </body>
</html>      
<?php include_once "includes/foot.php"; ?>