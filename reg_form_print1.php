<?php include_once "ses_set_common.php"; ?>
<?php 
include_once("classes/include/dbop.class.php");
include_once("classes/service_prd_reg_edit.class.php");
$m_dbConnRoot = new dbop(true);
$obj_service_prd_reg = new service_prd_reg($m_dbConn, $m_dbConnRoot);

if(isset($_REQUEST['id']))
{
	if($_REQUEST['id']<>"")
	{
		$edit = $obj_service_prd_reg->reg_edit($_REQUEST['id']);
		$soc_name = $obj_service_prd_reg->soc_name($edit[0]['society_id']);
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>

<style>
body {
	margin:0;
	padding:0;
  	color:#000;
	font-family:tahoma,arial,sans-serif;
	font-size:14px;
	}

</style>

<script language="javascript" type="application/javascript">
function print_form()
{
	document.getElementById('prt1').style.display = 'none';	
	document.getElementById('prt2').style.display = 'none';	
	
	window.print();
}
</script>

<!------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------->

	<!-- Add jQuery library -->
	<script type="text/javascript" src="lib/jquery-1.7.2.min.js"></script>

	<!-- Add mousewheel plugin (this is optional) -->
	<script type="text/javascript" src="lib/jquery.mousewheel-3.0.6.pack.js"></script>

	<!-- Add fancyBox main JS and CSS files -->
	<script type="text/javascript" src="source/jquery.fancybox.js?v=2.0.6"></script>
	<link rel="stylesheet" type="text/css" href="source/jquery.fancybox.css?v=2.0.6" media="screen" />

	<!-- Add Button helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="source/helpers/jquery.fancybox-buttons.css?v=1.0.2" />
	<script type="text/javascript" src="source/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>

	<!-- Add Thumbnail helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="source/helpers/jquery.fancybox-thumbs.css?v=1.0.2" />
	<script type="text/javascript" src="source/helpers/jquery.fancybox-thumbs.js?v=1.0.2"></script>

	<!-- Add Media helper (this is optional) -->
	<script type="text/javascript" src="source/helpers/jquery.fancybox-media.js?v=1.0.0"></script>
    
    <script type="text/javascript">
		$(document).ready(function() {			
		
		//Simple image gallery. Uses default settings
		$('.fancybox').fancybox();
		$('.fancybox1').fancybox();
	
	
		//Button helper. Disable animations, hide close button, change title type and content
		$('.fancybox-buttons').fancybox({
		openEffect  : 'none',
		closeEffect : 'none',

		prevEffect : 'none',
		nextEffect : 'none',

		closeBtn  : false,

		helpers : {
			title : {
				type : 'inside'
			},
			buttons	: {}
		},

			afterLoad : function() {
				this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
			}
		});
		});
	</script>
	<style type="text/css">
		.fancybox-custom .fancybox-skin 
		{
			box-shadow: 0 0 50px #222;
		}
	</style>
<!------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------->

</head>

<body>
<table align="center" border="0" width="970">
<tr>
	<td align="center"><font size="3"><b><?php echo $soc_name;?></b></font></td>
</tr>
<tr>
	<td align="center"><font size="+2"><b>Service Provider Profile View</b></font></td>
</tr>
</table>


<center><a href="javascript:void(0);" onclick="print_form();" id="prt1"><img src="images/print.png" width="40" height="40" /></a></center>


<table align="center" border="0">
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
</table>

<table align="center" border="1" style="width:75%;">
<tr bgcolor="#CCCCCC" align="left">
	<th colspan="2" height="35"> 
        <div style="float:left;"> &nbsp; <?php echo stripslashes($edit[0]['full_name']);?>  </div> 
    	<div style="float:right;">  Contacts : <?php echo stripslashes($edit[0]['cur_con_1']);?>  <?php if($edit[0]['cur_con_2']<>""){echo ' & '.stripslashes($edit[0]['cur_con_2']);}?></div>
   	</th>
</tr>
<tr>
<td>
    <table align="center" border="0">
    <tr>
        <td rowspan="4" align="center"><a href="<?php echo substr($edit[0]['photo'], 3);?>" class="fancybox"><img src="<?php echo substr($edit[0]['photo_thumb'], 3);?>" height="100" width="100" /></a></td>   
        <td><b>Date of Birth</b></td>
        <td>:</td>
        <td><?php echo strtoupper($edit[0]['dob']);?></td>
    </tr>
    <tr>
        <td><b>Age</b></td>
        <td>:</td>
        <td><?php echo strtoupper($edit[0]['age']);?> Year</td>
    </tr> 
    <tr>
        <td><b>Education</b></td>
        <td>:</td>
        <td><?php echo stripslashes($edit[0]['education']);?></td>
     </tr>
     <tr>
        	<td><b>Married</b></td>
            <td>:</td>
            <td><?php echo stripslashes($edit[0]['marry']);?></td>
   	</tr> 
    </table>
</td>
<td valign="top">
	<?php 
	//echo $combo_cat_id = $obj_service_prd_reg->combobox1111("select cat_id,cat from cat where status='Y' order by cat","cat_id[]","cat_id","select cat_id from spr_cat where service_prd_reg_id='".$_REQUEST['id']."' and status='Y'"); 		
	?>
    <table>
        <tr>
            <td><b>Working As</b></td>
            <td>:</td>            
        	<td>
        <?php
			$categories = $obj_service_prd_reg->fetchSelectedCategories($_REQUEST['id']);
			for($i = 0; $i < sizeof($categories); $i++)
			{
				echo $categories[$i]['cat'];
				if($i < sizeof($categories) - 1)
				{
					echo ', ';
				}
			}
		?>
        	</td>
       	</tr>
        <tr>
        	<td width="165"><b>Working Since</b></td>
            <td width="10">:</td>
            <td width="200"><?php if($edit[0]['father_name']<>""){echo stripslashes($edit[0]['since']);}else{ echo 'Not Mentioned';}?></td>           
        </tr>
        <tr>
        	<td><b>Identification Marks</b></td>
            <td>:</td>
            <td align="justify"><?php echo stripslashes($edit[0]['identy_mark']);?></td>
        </tr> 
        <tr>
        	<td width="165"><b>Residence Address</b></td>
            <td width="10">:</td>
            <td width="450"><?php echo stripslashes($edit[0]['cur_resd_add']);?></td>
        </tr>
    </table>
</td>
</tr>
<tr>
	<td colspan="2" align="left">
    	<table align="left" border="0" style="width:100%;">    	
        <tr>
        	<td style="width:20%;"><b>Native Address</b></td>
            <td style="width:5%;">:</td>
            <td style="width:25%;"><?php echo stripslashes($edit[0]['native_add']);?></td>        	
            <td style="width:20%;"><b>Native Contact No.</b></td>
            <td style="width:5%;">:</td>
            <td style="width:25%;"><?php echo stripslashes($edit[0]['native_con_1']);?> <?php if($edit[0]['native_con_2']<>""){echo ' & '.stripslashes($edit[0]['native_con_2']);}?></td>            
        </tr>             
        <tr>        	
        	<td><b>Reference Name</b></td>
            <td>:</td>
            <td><?php echo stripslashes($edit[0]['ref_name']);?></td>                      
            <td><b>Reference Contact No.</b></td>
            <td>:</td>
            <td><?php echo stripslashes($edit[0]['ref_con_1']);?> <?php if($edit[0]['ref_con_2']<>""){echo ' & '.stripslashes($edit[0]['ref_con_2']);}?></td>            
        </tr>      
        <tr>
        	<td><b>Reference Address</b></td>
            <td>:</td>
            <td colspan="4"><?php echo stripslashes($edit[0]['ref_add']);?></td>
        </tr>       
        </table>
    </td>
</tr>
<tr bgcolor="#CCCCCC" align="left">
	<th colspan="2" height="35"> &nbsp; Family Details</th>
</tr>
<tr>
	<td colspan="2">
    <table align="center" border="1">
        <tr height="30" bgcolor="#A4A4FF">
        	<th width="150">Relation</td>
            <th width="200">Full Name</td>
            <th width="170">Occupation</td>
        </tr>
        
        <tr height="25">
        	<td align="center"><b>Father</b></td>
            <td align="center"><?php if($edit[0]['father_name']<>""){echo stripslashes($edit[0]['father_name']);}else{ echo 'Not Mentioned';}?></td>
            <td align="center"><?php if($edit[0]['father_occ']<>""){echo stripslashes($edit[0]['father_occ']);}else{ echo 'Not Mentioned';}?></td>
        </tr>
        
        <tr height="25">
        	<td align="center"><b>Mother</b></td>
            <td align="center"><?php if($edit[0]['mother_name']<>""){echo stripslashes($edit[0]['mother_name']);}else{ echo 'Not Mentioned';}?></td>
            <td align="center"><?php if($edit[0]['mother_occ']<>""){echo stripslashes($edit[0]['mother_occ']);}else{ echo 'Not Mentioned';}?></td>
        </tr>
        
        <tr height="25">
        	<td align="center"><b>Husband / Wife</b></td>
            <td align="center"><?php if($edit[0]['hus_wife_name']<>""){echo stripslashes($edit[0]['hus_wife_name']);}else{ echo 'Not Mentioned';}?></td>
            <td align="center"><?php if($edit[0]['hus_wife_occ']<>""){echo stripslashes($edit[0]['hus_wife_occ']);}else{ echo 'Not Mentioned';}?></td>
        </tr>
        
        <tr height="25">
        	<td align="center"><b>Son / Daughter</b></td>
            <td align="center"><?php if($edit[0]['son_dou_name']<>""){echo stripslashes($edit[0]['son_dou_name']);}else{ echo 'Not Mentioned';}?></td>
            <td align="center"><?php if($edit[0]['son_dou_occ']<>""){echo stripslashes($edit[0]['son_dou_occ']);}else{ echo 'Not Mentioned';}?></td>
        </tr>
        
        <tr height="25">
        	<td align="center"><b>Other</b></td>
            <td align="center"><?php if($edit[0]['other_name']<>""){echo stripslashes($edit[0]['other_name']);}else{ echo 'Not Mentioned';}?></td>
            <td align="center"><?php if($edit[0]['other_occ']<>""){echo stripslashes($edit[0]['other_occ']);}else{ echo 'Not Mentioned';}?></td>
        </tr>
        </table>
    </td>
</tr>
<tr bgcolor="#CCCCCC" align="left">
	<th height="35" style="width:50%;"> &nbsp; Supporting Document Attached</th>
    <th height="35"> &nbsp; Units where service provider works </th>
</tr>
<tr>
	<td valign="top">
    	<table>
    <?php //echo $combo_cat_id = $obj_service_prd_reg->combobox1111("select document_id,document from document where status='Y' order by document","document[]","document","select document_id from spr_document where service_prd_reg_id='".$_REQUEST['id']."' and status='Y'"); 
	$documents = $obj_service_prd_reg->fetchSelectedDocs($_REQUEST['id']);	
		for($i = 0; $i < sizeof($documents); $i++)
		{
	?>	   
       		<tr> 
            	<td> <?php echo $documents[$i]['document']; ?> </td>                 
			</tr>
     <?php
		} ?>
       </table> 	
    </td>
    <td valign="top">
    <?php echo $combo_cat_id = $obj_service_prd_reg->combobox_units($_REQUEST['id']); ?>
    </td>
</tr>
<!-- <tr bgcolor="#CCCCCC" align="left">
	<th colspan="2" height="35">Units where service provider works</th>
</tr>
<tr>
	<td>
    <?php echo $combo_cat_id = $obj_service_prd_reg->combobox_units($_REQUEST['id']); ?>
    </td>
</tr>  -->
</table>

<br />

<center><a href="javascript:void(0);" onclick="print_form();" id="prt2"><img src="images/print.png" width="40" height="40" /></a></center>

</body>
</html>