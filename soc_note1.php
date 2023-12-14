<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Accounting Notes</title>
</head>
<?php
include_once("includes/head_s.php");
include_once ("classes/dbconst.class.php");

include_once("classes/soc_note1.class.php");
include_once("classes/doc.class.php");
//include_once( "classes/include/fetch_data.php");
//$objFetchData = new FetchData($m_dbConn);
//$objFetchData->GetSocietyDetails($_SESSION['society_id']);
//$Mobile =$objFetchData->getMobileNumber($_SESSION['unit_id']);
$objdoc = new document($m_dbConn);
$obj_society_notes = new soc_note1($m_dbConn);
$res_display=$obj_society_notes->AccountsNotes();
$res_update=$obj_society_notes->addNotes();
/*if(isset($_REQUEST['id']))
{
	//print_r($_REQUEST['id']);
	if($_REQUEST['id']<>"")
	{
		$edit = $obj_soc_note1->Note_edit($_REQUEST['id']);
		
			$uploadFile=$edit[0]['Uploaded_file'];
			//$image_collection = explode(',', $image);	
		
	}
}*/

?>


<div id="middle">

<br>
<div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:3.5%;width:90%;">
<div class="panel-heading" id="pageheader">Accounting Notes</div>
<br>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsevents20190504.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }	
	
	 //$( document ).ready(function() {
		var isblocked = '<?php echo $UnitBlock ?>';
		if(isblocked==1)
		{
			//alert("We are sorry,but your access has been blocked for this feature . Please contact your Managing Committee for resolution .");
			
			window.location.href='suspend.php';
		}
    //});
	
	</script>
    
    <!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
	<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
    <script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
    <script type="text/javascript" src="javascript/ui.core.js"></script>
    <script type="text/javascript" src="javascript/ui.datepicker_event.js"></script>-->
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true, 
			yearRange: '-0:+10', // Range of years to display in drop-down,
        })});
            
    </script>
     <script language="javascript" type="text/javascript">
     	$(document).ready(function()
     	{
     		document.getElementById("Document_type").selectedIndex = "1";
     	})
		function EnableNoteType(value)
		{								
			if (value == 1) 
			{				
				$('#upload').hide();
				//$('#create').show();
				$('#desc').show();								
			}            
       		else if(value == 2)
			{				
				//$('#create').hide();
				$('#desc').show();
				$('#upload').show();				
				//CKEDITOR.replace( 'description', {toolbarStartupExpanded : false} );            	
			}
			else if(value == 0)
			{									
				$('#upload').hide();
				//$('#create').hide();
				$('#desc').hide();				
			}
		}
		
	document.body.onload =	function()
		{			
			go_error();
			<?php 
			if(!isset($_REQUEST['id'])  <> '')
			{
			?>
			
			EnableNoteType(0);
			
			<?php
			 }?>
		}
	</script>	
 
</head>

<body>
<form name="note" id="note" method="post" action="process/soc_note1.process.php" enctype="multipart/form-data" onSubmit="return val();" >
<table align='center'>
<?php $star = "<font color='#FF0000'>*&nbsp;</font>";?>
<table align='center' style="width:90%">
	<?php
		if(isset($_POST["ShowData"]))
			{
	?>
				<tr height="30"><td colspan="8" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
	<?php   }
			else
			{?>
    			<tr height="30"><td colspan="8" align="center"><font color="red" style="size:11px;"><b id="error"></b></font></td></tr>
                <input type="hidden" id = "mId" name = "mId" value = "<?php echo $_REQUEST['mId']?>"/>
          <?php } ?>
     <tr>
     	<td></td>
        <td></td>
        <td></td>
<tr><td colspan="4">&nbsp;</td></tr>
		<tr align="left" id="">
			<td valign="top" align="left"><b>Note Description</b></td>
            <td valign="top" align="left">&nbsp; : &nbsp;</td>
			<td colspan="6" align="left"><textarea name="note_desc" id="note_desc" rows="40" cols="100"><?php print_r($res_display); ?>
</textarea></td>
		</tr>
 	<script>
			//CKEDITOR.config.height = 100;
			//CKEDITOR.config.width = 500;
			CKEDITOR.config.extraPlugins = 'justify,table';
		//CKEDITOR.config.extraPlugins = 'table';
			CKEDITOR.config.entities_processNumerical = true;
			CKEDITOR.config.entities_processNumerical = 'force';
			//CKEDITOR.config.extraPlugins = 'table';
			CKEDITOR.replace('note_desc', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] },
								{ name: 'insert', items: [ 'Table' ] },
								{ name: 'insert_2', items: [ 'PageBreak' ] }
   								 ],
								 height: 300,
        						 width: 650,
								 uiColor: '#14B8C4'
								 });
		</script>
              <tr><td colspan="4">&nbsp;</td></tr>
              <tr><td>
              <input type="hidden" name="id" id="id">
           <?php if($_REQUEST['id'] <> 0)
			{?><input type="hidden" id="updateid" name="updateid" value="<?php echo $_REQUEST['id']; ?>">
  			<?php }else
			{?>
            <input type="hidden" id="updateid" name="updateid" value="0">
			<?php 	}?>
            
             <input type="submit" name="insert" id="insert" value="Save" class="btn btn-primary" style="color:#FFF; width:100px;background-color:#337ab7;margin-left:350%" >
            </td>
              </tr>
              <tr><td><br></td></tr>
              
</table>
</form>
</body>
</html>
</div>
</div>
</body>
</html>

<?php include_once "includes/foot.php"; ?>