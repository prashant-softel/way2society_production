<?php include_once("includes/head_s.php");?>
<?php include_once("classes/include/dbop.class.php");
include_once ("classes/dbconst.class.php");
include_once("classes/approval_module.class.php");
include_once("classes/utility.class.php");
include_once( "classes/include/fetch_data.php");
$dbConnRoot =new dbop(true);
$dbConn =  new dbop();
$obj_approval_module = new approval_module($dbConn, $dbConnRoot);
$objFetchData = new FetchData($m_dbConn);
$IsNotify = 1;
$committeeCount = $obj_approval_module->CMemeberCount();

?>
 

<html>
<head>
<style>
.submitButton
{
	color: #fff !important;
    background-color: #337ab7 !important ;
    border-color: #2e6da4 !important;
	padding: 6px 12px !important;
    font-size: 14px;
    font-weight: 400;
}
</style>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/approval_module.js"></script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script language="javascript" type="application/javascript">
function go_error()
{
	$(document).ready(function()
	{
		$("#error").fadeIn("slow");
	});
	setTimeout('hide_error()',8000);	
}
function hide_error()
{
	$(document).ready(function()
	{
		$("#error").fadeOut("slow");
	});
}
</script>
<script type="text/javascript">
$(function()
{
	$.datepicker.setDefaults($.datepicker.regional['']);
	$(".basics").datepicker({ 
	dateFormat: "dd-mm-yy", 
	showOn: "both", 
	buttonImage: "images/calendar.gif", 
	buttonImageOnly: true 
})});

$(document).ready(function(){
 //alert("test");
	
	var allselect =document.getElementById('approval').value;
	if(allselect == 0)
	{
		document.getElementById('total').value='<?php echo $committeeCount?> ';
	}
	else
	{
	}
	
});
function changeclick()
{
	allselect =document.getElementById('approval').value;
	if(allselect == 0)
	{
		document.getElementById('total').value='<?php echo $committeeCount?> ';
	}
	else
	{
		var options = document.getElementById('approval').options, count = 1;
		//alert(options);
		for(var i=0; i < options.length; i++) 
		{
 			if (options[i].selected) 
			{	
				var k=count++;
				document.getElementById('total').value=k;
			}
		}
	}
}
function clickmin()
{
	var fieldVal = Number(document.getElementById('min').value);
	var total=Number(document.getElementById('total').value);
	
	//alert(typeof(total));
	//alert(typeof(fieldVal));
	//alert((total > fieldVal));
	if(total >= fieldVal)
	{
		return true;
	}
	else
	{
		alert("Minimum Approvals cannot be greater than Total Approvals");
	}
	
	/*if(fieldVal > total)
	{
    	return true;
	}
	else
	{
  		alert("Minimum Approvals cannot be greater than Total Approvals");
	}*/
}
</script>
<script type="text/javascript">
var DocCount=1;
var MaxInputs=5;
$(function () 
{
	
	$("#btnAddDoc").bind("click", function () 
	{
		//alert("Add");
		if(DocCount < MaxInputs) //max file box allowed
        {
			DocCount++; 
			document.getElementById('doc_count').value=DocCount;
			var div = $("<tr />");
        	div.html(addNewDocRow(""));
        	$("#docTable").append(div);	 
		}
		else
		{
			alert("Maximum Limit reached:\nCannot add more than 5 documents")
		}
	});
	$("#btnGet").bind("click", function ()
	{
       var values = "";
       $("input[name=upload]").each(function ()
		{
            values += $(this).val() + "\n";
        });
        alert(values);
    });
    $("body").on("click", ".remove", function ()
	{
      $(this).closest("div").remove();
    });
});
function addNewDocRow(value) 
{
	var content="<td align=left'> <input type='hidden' name='docId_"+DocCount+"' id='docId_"+DocCount+"'><input type='text' id='doc_name_"+DocCount+"' name='doc_name_"+DocCount+"'/></td><td align='left'><input type='file' name='userfile_"+DocCount+"' id='userfile_"+DocCount+"' style='width:200px;' /></td>";
    return (content);
}
</script>
</head>
<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body>
<?php }else{ ?>
<body>
<?php } ?>

<div id="middle">
	<br>
	<div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:2.5%;width:95%;">
	<div class="panel-heading" id="pageheader">Approval Module</div>
	<br>
	<br>
		<center><button type="button" class="btn btn-primary" onClick="window.location.href='approvals.php?type=active'">Go Back</button></center>
        
		<form name="events" id="events" method="post" action="process/approval_module.process.php" enctype="multipart/form-data" onSubmit="return val();">
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
		else{}
		?>
		<table align='center' style="width: 100%;">
		<?php
		if(isset($msg))
		{
			if(isset($_POST['ShowData']))
			{
			?>
				<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
			<?php
			}
			else
			{
			?>
				<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $msg; ?></b></font></td></tr>
			<?php
			}
		}
		else
		{
		?>
				<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
		<?php
		}
		?>
		<br>
		<br>
        <tr align="left">
        <td colspan="6">
        </td>
        <td colspan="6">
        <table style="width:100%">
        <tr align="left"><td style="width:30%"></td><td>
        For Committee Members</td>
        </tr>
        </table>
        </td>
        </tr>
        <tr align="left">
        <td colspan="6">
        	<table style="width:100%">
        	<tr align="left">
     		<td valign="middle"><?php echo $star;?></td>
   			<th><b>Created By</b></th>
	    	<td>&nbsp; : &nbsp;</td>
        	<td><input type="text" name="issueby" id="issueby" value="<?php echo $_SESSION['name']; ?>"/></td>        	
			</tr>
            <tr><td colspan="6">&nbsp;</td></tr>
            <tr align="left">
     		<td valign="middle"><?php echo $star;?></td>
   			<th><b>Post Date</b></th>
	    	<td>&nbsp; : &nbsp;</td>
        	<td><input type="text" name="post_date" id="post_date" value="<?php echo date('d-m-Y');?>" class="basics" size="10" readonly  style="width:100px;"/></td>        	
			</tr>
            <tr><td colspan="6">&nbsp;</td></tr>
            <tr align="left">
     		<td valign="middle"><?php echo $star;?></td>
   			<th><b>Approve By Date</b></th>
	    	<td>&nbsp; : &nbsp;</td>
        	<td><input type="text" name="exp_date" id="exp_date"  class="basics" size="10"  style="width:100px;"/></td>        	
			</tr>
            <tr><td colspan="6">&nbsp;</td></tr>
            <tr align="left">
     		<td valign="left"><?php echo $star;?></td>
   			<th><b>Subject</b></th>
	    	<td>&nbsp; : &nbsp;</td>
        	<td><textarea name="subject" id="subject" style="width:200px; resize:none;"></textarea> <font size="1" color="#CC0000">
				<span id="textCounter" style="display:none;">You have <input type="text"  name="countdown" id = "countdown" size="3" value="100" style="width:35px;text-align:center;border:none;box-shadow:none" readonly /> characters left.</td>        	
			</tr>
             <tr><td colspan="6">&nbsp;</td></tr>
            <!--<tr align="left">
     		<td valign="left"><?php //echo $star;?></td>
   			<th><b>Attachment</b></th>
	    	<td>&nbsp; : &nbsp;</td>
        	<td>
            <input type="file" name="userfile[]" multiple id="userfile" style="width:200px"/ ><img src="images/plus.png" style="width: 25px;
    height: 25px;">
           <input name="userfile[]" id="userfile" type="file" style="width:200px"/ ></td>        	
			</tr>-->
        </table>
        </td>
        <td colspan="6">
        	<table style="width:100%">
        	<tr align="left">
        	<td align="right">&nbsp;<?php //echo $star;?>&nbsp;</td>
			<td align="right"><?php echo $star;?>&nbsp;<b>Get Approval From </b>&nbsp;</td>
            <td>&nbsp; : &nbsp;</td>
			<td><select name="approval[]" id="approval" multiple="multiple"  style=" width:300px; height:170px;" class="dropdown" onChange="changeclick();" >
    <?php echo $combo_unit = $obj_approval_module->combobox2 ("select C.member_id as MemberId, M.other_name from mem_other_family as M, commitee as C where M.status = 'Y' and M.mem_other_family_id = C.member_id",'0')?>
    </select></td>
    		</tr>
        	</table>
        </td>
        </tr>
        <tr><td><br></td></tr>
        <tr align="left">
         <td colspan="6">
        	<table style="width:90%">
        	<tr align="left">
   			<th style="width: 20%;"><b>Notify Members By</b></th>
	    	<td style="width: 3%;">&nbsp; : &nbsp;</td>
        	<td style="width: 6%;"><b>Email</b></td>
            <td style="width: 2%;">&nbsp;<b>:<b>&nbsp;</td>     
       		<td style="width: 6%;"><input type="checkbox" name="notify" id="notify" value="1" onChange="uploadText(this.checked);">
            <input type="checkbox" name="sms_notify" id="sms_notify" value="1"  style="display:none"></td>
            </td>  
            <!-- <td style="width: 6%;"><b>SMS</b></td>  
        	<td style="width: 2%;">&nbsp;<b>:<b>&nbsp;</td>     
       		<td style="width: 10%;"><input type="checkbox" name="sms_notify" id="sms_notify" value="1" ></td>   --> 
            <td style="width: 3%;"><b>Mobile</b></td>  
       	 	<td style="width: 3%;">&nbsp;<b>:<b>&nbsp;</td>     
        	<td style="width: 10%;"><input type="checkbox" name="mobile_notify" id="mobile_notify" value="1" checked>   </td>  	
			</tr>
            </table>
            </td>
        	<td colspan="6">
        	<table style="width:100%">
        	
            <tr align="left">
            <td align="left"></td>
			<td align="right" style="width:26%" >&nbsp;<b>Total Approvals Selected</b>&nbsp;</td>
            <td style="width:4%">&nbsp; : &nbsp;</td>
			<td><input id="hidden" type="text" name="hidden" value="1" readonly hidden />
            <input type="text" name="total" id="total"  style="width:50px;"  value="0" readonly/></td>
            </tr>
            <tr><td colspan="6">&nbsp;</td></tr>
            <tr align="left">
            <td align="left"></td>
			<td align="right" style="width:26%" >&nbsp;<b>Minimum Approvals Required</b>&nbsp;</td>
            <td style="width:4%">&nbsp; : &nbsp;</td>
			<td><input type="text" name="min" id="min"  style="width:50px;" onChange="clickmin();"/></td>
            </tr>
             <tr><td colspan="6">&nbsp;</td></tr>
              <tr><td colspan="6">&nbsp;</td></tr>
        	</table>
             
        </td>
       
       </tr>
      </table>
       <div id="hr"><hr></div>
       <center>
       <table style="width:50%;text-align:center" id="docTable">
       <tr><td align="center" colspan="3"><b style="font-size: 12px;"><u>Attachments</u></b></td></tr>
       <tr><td colspan="3"><br></td></tr>
       <?php if(isset($_REQUEST['editid']) && $_REQUEST['editid'] <> '')
	   {
		   $result=$obj_approval_module->View_attachments($_REQUEST['editid']);
		  // var_dump($result);
		   for($i=0;$i<sizeof($result);$i++)
		   {
			   ?>
			   <tr><td colspan="3" align="center"><b><a href="<?php echo $result[$i]['Url'] ;?>" target="_blank"><?php echo $result[$i]['Doc_name'];?></b></a></td></tr>
		   <?php 
		   }
		   
	   }
	   else
	   {?>
       <tr>
       <th style="width:30%;">Enter Attachment Name  : </th>
       <th style="width:30%;">Choose File  :</th>
       <th style="width:40%;">&nbsp;</th>
       </tr>
       <tr>
       <input type="hidden" name="doc_count" id="doc_count" value="1">
       <td align="left"> <input type="text" name="doc_name_1" id="doc_name_1"/><input type="hidden" name="docId_1" id="docId_1"></td>
       <td align="left"> <input type="file" name="userfile_1" id="userfile_1" style="width:200px"/ ></td>
       <td align="left"><input id="btnAddDoc" type="button" value="Add More"></td>
       </tr>
       <?php }
	   ?>
       </tr>
       </table>
       </center>
       
       <table style="width: 100%;">
       <tr align="left">
    	<td><b>&nbsp;&nbsp;Approval Description :</b></td>
        </tr>
        <tr>
        	<td align="center">&nbsp;&nbsp;&nbsp;&nbsp;<textarea name="description" id="description" rows="5" cols="50"></textarea></td>
    	</tr>
       </table>
       <script>
			CKEDITOR.config.extraPlugins = 'justify,table';
			CKEDITOR.replace('description', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] },
								{ name: 'insert', items: [ 'Table' ] },
								{ name: 'insert_2', items: [ 'PageBreak' ] }
   								 ],
								 height: 300,
        						 width: 740,
								 uiColor: '#14B8C4'
								 });
		</script>
        
        
     <input type="hidden" id="userMobileNo" name="userMobileNo" value="<?php echo $Mobile[0]['mob'] ?>">
	<input type="hidden" id="id" name="id" value="">
    <input type="hidden" id="approval_Id" name="approval_Id" value="<?php echo $_REQUEST['editid']; ?>">
	
	<?php
	if(isset($_REQUEST['editid']) && $_REQUEST['editid'] <> '')
	{
		?>
			<script>
			
				getapproval_module('edit-' + <?php echo $_REQUEST['editid'];?>);	
				/* -------------------  Get Documents ---------------- */
				/*var approvalId ='<?php //echo $_REQUEST['editid']?>';
				$.ajax
					({
						url : "ajax/approval_module.ajax.php",
						type : "POST",
						//datatype: "JSON",
						data : {"method":"editDocumentDetails","approvalId":approvalId},
						success : function(data1)
						{
						
							//alert ("res :"+data1);
							var a		= data1.trim();	
							var arr1	= new Array();
							var arr2	= new Array();
							arr1		= a.split("@@@");
							arr2		= arr1[1].split("#");
							alert ("arr1:"+arr1);
							alert ("arr2:"+arr2);
							/*var i=0,j=1;
							if(arr2.length>1)
							{
								document.getElementById("uploadedDocDetails").style.display="table";
							}
							
							while(i<arr2.length)
							{
								if(arr2[i]!="")
								{	
									addNewRow();
									document.getElementById('docId'+j).value=arr2[i]
									document.getElementById('docId'+j).readOnly = "true";
									document.getElementById('fileLink'+j).href = "https://docs.google.com/viewer?srcid="+arr2[10]+"&pid=explorer&efh=false&a=v&chrome=false&embedded=true"; 
									document.getElementById('fileLink'+j).setAttribute('target','_blank');
									document.getElementById('docName'+j).value=arr2[i+1];
									document.getElementById('docName'+j).readOnly = "true";
									document.getElementById('fileName'+j).value=arr2[i+6];
									document.getElementById('fileName'+j).readOnly = "true";
									//alert ("before i:"+i);
									i=i+13;
									//alert ("After i:"+i);
									j=j+1;
								}
								else
								{
									break;
								}
							}
						}
					});	*/			
			</script>
		<?php
	}
	
	if(isset($_REQUEST['deleteid']) && $_REQUEST['deleteid'] <> '')
	{
		?>
			<script>
				getapproval_module('delete-' + <?php echo $_REQUEST['deleteid'];?>);				
			</script>
		<?php
	}
?>
	<?php
	/*if(isset($_REQUEST['id']) && $_REQUEST['id'] <> '')
	{?>
	<input type="hidden" name="updaterowid" id="updaterowid" value="<?php echo $_REQUEST['id']; ?>" />
    <input type= "hidden" id="NoticeSubject" name="NoticeSubject" value="">
    <?php	
	}
	else if(isset($_REQUEST['deleteid']) && $_REQUEST['deleteid'] <> '')
	{?>
	<input type="hidden" name="updaterowid" id="updaterowid" value="<?php echo $_REQUEST['deleteid']; ?>" />
	<?php	
	}
	else
	{ ?>
	<input type="hidden" name="updaterowid" id="updaterowid" value="0" />
    <?php }*/?>
 
<tr><td><br><br></td></tr>    
</table>
</center>
		<tr>
        <center><!--<button type="submit" name="insert" id="insert"class="btn btn-primary btn-raised">Submit</button>-->
        <td colspan="10" align="center"><input type="submit" name="insert" id="insert" value="Submit"  class="submitButton"><input type="button" name="insertMeeting" id="insertMeeting" value="Submit"  class="btn btn-primary" onClick="changeMeetingStatus()" style="display:none"></td>
        </center>
        </tr>
        <tr><td><br><br></td></tr>    
        </table>
</form>
</div>
</div>
<?php include_once "includes/foot.php"; ?>
<!--<table align="center">
<tr>
<td>
<?php
/*echo "<br>";
$str1 = $obj_approval_module->pgnation();
echo "<br>";
echo $str = $obj_approval_module->display1($str1);
echo "<br>";
$str1 = $obj_approval_module->pgnation();
echo "<br>";*/
?>
</td>
</tr>
</table>-->

</body>
</html>


