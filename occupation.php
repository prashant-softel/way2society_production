<?php include_once "ses_set_mfb.php"; ?>
<?php
include_once("classes/occupation.class.php");
$obj_occupation=new occupation($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsoccupation.js"></script>
    <script language="javascript" type="application/javascript">
	
	</script>
</head>
<body>

<h2 align="center">Occupation</h2>

<form name="occupation" id="occupation" method="post" action="process/occupation.process.php">

	<?php
		$star = "<font color='#FF0000'>*</font>";
		if(isset($_REQUEST['msg']))
		{
			$msg = "Sorry !!! You can't delete it. ( Dependency )";
		}
		else if(isset($_REQUEST['msg1']))
		{
			$msg = "Deleted Successfully.";
		}
		else
		{
			//$msg = '';	
		}
	?>
    <table align='center'>
		<?php
		if(isset($msg))
		{
			if(isset($_POST["ShowData"]))
			{
		?>
				<tr height="40" id="error"><td colspan="4" align="center"><font color="red" size="2"><b><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
		<?php
			}
			else
			{
			?>
            	<tr height="40" id="error"><td colspan="4" align="center"><font color="red" size="2"><b><?php echo $msg; ?></b></font></td></tr>	   
            <?php		
			}
		}
		else
		{
		?>	
				<tr height="40" id="error"><td colspan="4" align="center"><font color="red" size="2"><b><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
        <?php
		}
		?>

		<tr>
        	<td valign="top"><?php echo $star;?></td>
			<td>Occupation</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="occupation" id="occupation1" /></td>
		</tr>

        <tr><td colspan="4">&nbsp;</td></tr>
		
        <tr>
			<td colspan="4" align="center">
            <input type="hidden" name="id" id="id">
            <input type="submit" name="insert" id="insert" value="Insert">
            </td>
		</tr>
</table>
</form>


<table align="center">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_occupation->pgnation();
echo "<br>";
echo $str = $obj_occupation->display1($str1);
echo "<br>";
$str1 = $obj_occupation->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>

</body>
</html>
