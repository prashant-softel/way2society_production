
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>

<title>W2S - Helpline Numbers</title>
</head>



<?php
	include_once("includes/head_s.php"); 
	include_once("classes/helpline.class.php");
	include_once("classes/dbconst.class.php");
	
	$dbConn = new dbop();
	$Objhelpline = new helpline($dbConn);
	$HelpDetails=$Objhelpline->fetchdetails();
?>
 
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="lib/js/jquery.min.js"></script>
    
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/ajax_new.js"></script>
	<script type="text/javascript" src="js/jshelpline.js"></script>
    <script type="text/javascript" src="lib/js/jquery.min.js"></script>
</head>
<body>
<br><br>
<div class="panel panel-info" id="panel" style="width:70%;display:block;margin-left: 4%;">
<div class="panel-heading" style="font-size:20px;text-align:center;">
    Helpline Numbers
</div>
<?php if(!($_SESSION['role'] && ($_SESSION['role'] <> ROLE_ADMIN && $_SESSION['role'] <> ROLE_SUPER_ADMIN )))
			{?>
 <center><button type="button" class="btn btn-primary" style="margin-top:25px" onClick="window.location.href='addhelpline.php'">Add New Helpline Number</button></center>
 	  <?php } ?>
<div class="table table-responsive condensed" id="helpdetails">
		<br/>          
                
            <table id="example" class="display" cellspacing="0" width="100%">
            <thead>
            <th style="text-align:center">Category</th>
            <th style="text-align:center">Name</th>
            <th style="text-align:center">Contact No.</th>
            <th style="text-align:center">Details</th>
            <th style="text-align:center">Edit</th>
            <th style="text-align:center">Delete</th>
            </thead>
            <?php for($i=0;$i<sizeof($HelpDetails);$i++) {?>
            <tr>
            <td style="text-align:center"><?php echo $HelpDetails[$i]['category']?></td>
             <?php if($HelpDetails[$i]['name']==""){?>
            <td style="text-align:center">-</td>
            <?php } else { ?>
            <td style="text-align:center"><?php echo $HelpDetails[$i]['name']?></td>
            <?php } ?>
             <?php if($HelpDetails[$i]['numbers']==""){?>
            <td style="text-align:center">-</td>
            <?php } else { ?>
			<td style="text-align:center"><?php echo $HelpDetails[$i]['numbers']?></td>
            <?php } ?>
            <?php if($HelpDetails[$i]['Note']==""){?>
            <td style="text-align:center">-</td>
            <?php } else { ?>
			<td style="text-align:center"><?php echo $HelpDetails[$i]['Note']?></td>
            <?php } ?>
            <td style="text-align:center"> <a href="addhelpline.php?edit=<?php echo $HelpDetails[$i]['id'];?>"><img src="images/edit.gif" /></a></td>
                        <td style="text-align:center"> <a href="helpline.php?deleteid=<?php echo $HelpDetails[$i]['id'];?>"><img src="images/del.gif" /></a></td>
            </tr>
            <?php } ?>
            </table>             
              
 </div>             
</div>
</body>

<?php
if(isset($_REQUEST['deleteid']))
	{
		?>
			<script>
				getService('delete-' + <?php echo $_REQUEST['deleteid'];?>);				
			</script>
		<?php
	}
?>
<?php include_once "includes/foot.php"; ?>
</html>
