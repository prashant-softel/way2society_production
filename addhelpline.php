<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Add New Helpline Details</title>
</head>

<?php
	include_once("includes/head_s.php"); 
	include_once("classes/helpline.class.php");
	include_once("classes/dbconst.class.php");
	$dbConn = new dbop();
	$Objhelpline = new helpline($dbConn);
	
	$HelpDetails=$Objhelpline->fetchdetails();
	//var_dump($HelpDetails); 
	if(isset($_REQUEST['edit']))
    {
	if($_REQUEST['edit']<>"")
	{ 	
		$help = $Objhelpline->getViewDetails($_REQUEST['edit']);
		//var_dump($help);
	}
	}

	if(isset($_REQUEST['Help']))
	{
	$category="";
	if($_POST['cat']=="")
	{
		
    echo '<script type="text/javascript">alert("Please Select Category");';
	echo 'window.location="addhelpline.php';
	echo '"</script>';
	}
	else
	{
	if($_POST['cat']=="Other")
	{
		$category=$_POST['catother'];
	}
	$insert=$Objhelpline->insertrecord($_POST['cat'],$category,$_POST['name'],$_POST['contacthelpline'],$_POST['details']);
	if($insert=="0")
	{?>
    <script>alert('Record Added Successfully');
    window.location="helpline.php";
    </script>
<?php 				
	}
	}
	}
	if(isset($_REQUEST['Editable']))
	{
	$id=$_POST['hid'];
	if($_POST['cat']=="")
	{
		
    echo '<script type="text/javascript">alert("Please Select Category");';
	echo 'window.location="addhelpline.php?edit=';
	echo $id ;
	echo '"</script>';
	}
	else
	{
	$category="";
	
	if($_POST['cat']=="Other")
	{
		$category=$_POST['catother'];
	}
	//var_dump($_POST['cat'] . " " .$_POST['name']);
	$update=$Objhelpline->updaterecord($_POST['hid'],$_POST['cat'],$category,$_POST['name'],$_POST['contacthelpline'],$_POST['details']);
	if($update=="0")
	{?>
    <script>alert('Record Updated Successfully');
     window.location="helpline.php";
    </script>
<?php 				
	}
	}
	}
?>



<html>
<head>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/ajax_new.js"></script>
	<script type="text/javascript" src="js/jshelpline.js"></script>
    <script type="text/javascript" src="lib/js/jquery.min.js"></script>
</head>
<body>

<br><br>
<div class="panel panel-info" id="panel" style="width:70%;display:block;margin-left: 4%;">
<div class="panel-heading" style="font-size:20px;text-align:center;">
   Add New Helpline Details
</div>
<form name="helpno" method="post" action= "<?php if(!isset($_REQUEST['edit'])){?>addhelpline.php?Help <?php } else { ?> addhelpline.php?Editable <?php } ?> ">
<center>
<?php if(!isset($_REQUEST['edit'])){?>
<table id="inserthelpline"  align="center" width="40%">
<br><br>
   
 	<tr>
    
        <td align="left" style="font-size:12px;font-weight:bold"><span style="color:red">*</span>Category</td>
        <td align="center"> :  </td>
      	<td align="center">
        <select name="cat" id="cat"  onChange="category()">
             <?php $qry1 = "SELECT id,category FROM `helpline` WHERE `status`='Y' group by category order by category ";
			
			echo $Objhelpline->combobox($qry1,0);?>
			</select>
       </td>
     </tr>
     <tr>
     	<td colspan="3"><br></td>
        <!--<td><br></td>-->
     </tr>
      <tr id="categoryother">
         <td align="left" style="font-size:12px;font-weight:bold">Add Category </td> 
         <td align="center">: </td>
         <td align="center"><input type="text" name="catother" placeholder="Enter Category"  id="catother" /> </td>
	</tr>
     <tr>
     	<td colspan="3"><br></td>
        <!--<td><br></td>-->
     </tr>
    <tr>
      	<td align="left" style="font-size:12px; font-weight:bold;">Name </td>
        <td align="center"> :</td>
        <td align="center"><input type="text" name="name" placeholder="Enter Name"  id="name"  /></td>
     </tr>
      <tr>
     	<td colspan="3"><br></td>
        <!--<td><br></td>-->
     </tr>
    <tr>
      	<td align="left" style="font-size:12px;font-weight:bold">Contact No.</td>
        <td align="center"> :</td>
        <td align="center"><input type="text" name="contacthelpline" placeholder="Enter Contact Numbers"  id="contacthelpline"  /></td>
    </tr>
    <tr>
     	<td colspan="3"><br></td>
        <!--<td><br></td>-->
     </tr>
	 <tr>
		<td  align="left" style="font-size:12px; font-weight:bold">Details </td>
        <td align="center">:</td>
  		<td align="right"><textarea name="details" id="details" rows="4" cols="40" ></textarea></td>
    </tr>
     <tr>
     	<td colspan="3"><br></td>
        <!--<td><br></td>-->
     </tr>
    </table>
 
    <input type="submit" name="insert" id="insert" class="btn btn-primary" value="Submit" style="width: 150px; height: 30px; background-color: #337ab7; color:#FFF"; >
<?php } else
{ ?>
<input type="hidden"  name="hid" id="hid"  value="<?php echo $help[0]['id'];?>"/>
<table id="inserthelpline"  align="center" width="40%">
<br><br>
   
 	<tr>
    
        <td align="left" style="font-size:12px;font-weight:bold"><span style="color:red">*</span>Category</td>
        <td align="center"> :  </td>
      	<td align="center">
        <select name="cat" id="cat"  onChange="category()" value="<?php echo $help[0]['category'];?>">
       
             <?php $qry1 = "SELECT id,category FROM `helpline` WHERE `status`='Y' group by category order by category ";
			
			echo $Objhelpline->combobox($qry1,0);?>
			</select>
       </td>
     </tr>
     <tr>
     	<td colspan="3"><br></td>
        <!--<td><br></td>-->
     </tr>
      <tr id="categoryother">
         <td align="left" style="font-size:12px;font-weight:bold">Add Category </td> 
         <td align="center">: </td>
         <td align="center"><input type="text" name="catother" placeholder="Enter Category"  id="catother" /> </td>
	</tr>
     <tr>
     	<td colspan="3"><br></td>
        <!--<td><br></td>-->
     </tr>
    <tr>
      	<td align="left" style="font-size:12px; font-weight:bold;">Name </td>
        <td align="center"> :</td>
        <td align="center"><input type="text" name="name" placeholder="Enter Name"  id="name" value="<?php echo $help[0]['name'];?>"  /></td>
     </tr>
      <tr>
     	<td colspan="3"><br></td>
        <!--<td><br></td>-->
     </tr>
    <tr>
      	<td align="left" style="font-size:12px;font-weight:bold">Contact No.</td>
        <td align="center"> :</td>
        <td align="center"><input type="text" name="contacthelpline" placeholder="Enter Contact Numbers"  id="contacthelpline" value="<?php echo $help[0]['numbers'];?>"  /></td>
    </tr>
    <tr>
     	<td colspan="3"><br></td>
        <!--<td><br></td>-->
     </tr>
	 <tr>
		<td  align="left" style="font-size:12px; font-weight:bold">Details </td>
        <td align="center">:</td>
  		<td align="right"><textarea name="details" id="details" rows="4" cols="40"  ><?php echo $help[0]['Note'];?></textarea></td>
    </tr>
     <tr>
     	<td colspan="3"><br></td>
        <!--<td><br></td>-->
     </tr>
    </table>
 
    <input type="submit" name="insert" id="insert" class="btn btn-primary" value="Update" style="width: 150px; height: 30px; background-color: #337ab7; color:#FFF"; >
 <?php } ?>
 </center>
</center>
</form>
<br><br>
</div>
<script>
document.getElementById("categoryother").style.display="none";
function category() {
  var x = document.getElementById("cat").value;
  if(x=="Other")
  {
	 document.getElementById("categoryother").style.display="table-row";
  }
  else
  {
	  document.getElementById("categoryother").style.display="none";
  }
}
</script>
</body>
<?php
	if(isset($_REQUEST['edit']) && $_REQUEST['edit'] <> '')
	{
		?>
			<script>
				getService('edit-' + <?php echo $_REQUEST['edit'];?>);				
			</script>
		<?php
	}
	?>

<?php include_once "includes/foot.php"; ?>
</html>