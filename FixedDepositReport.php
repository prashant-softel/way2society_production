
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Fixed Deposit Report</title>
</head>




<?php include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
include_once("classes/FixedDeposit.class.php");
$obj_FixedDeposit = new FixedDeposit($m_dbConn);
$fdAccountArray = $obj_FixedDeposit->FetchFdCategories();
$fdAccountArray = implode(',', $fdAccountArray);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <link href="css/messagebox.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/ajax.js"></script>
   	<script type="text/javascript" src="js/ajax_new.js"></script>
	<script type="text/javascript" src="js/jsFixedDeposit.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
	<script language="javascript" type="application/javascript">
	function go_error()
    {
		document.getElementById('error').style.display = 'block';
        setTimeout('hide_error()',2000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	</script>
    
    <script>
	jQuery.expr[':'].Contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};

function ShowSearchElement()
{
		document.getElementById('msgDiv').style.display = 'none';
   		var w =  $('#searchbox').val();
        if (w)
		 {
				if($('#ledger_id li:Contains('+w+')').length == 0)
				{
					$('#ledger_id li').hide();
					document.getElementById('msgDiv').style.display = 'block';
					document.getElementById('msgDiv').innerHTML = '<font style="color:#F00;"><b>No Match Found...</b></font> ';
				}
				else
				{
					$('#ledger_id li').hide();
					$('#ledger_id li:Contains('+w+')').show();	
				}
		} 
		else 
		{
			 $('#ledger_id li').show();                  
        }
}

function uncheckDefaultCheckBox(id)
{
	if(document.getElementById(id).checked  == true)
	{
		document.getElementsByClassName('chekAll')[0].checked = false;
	}
	else
	{
			document.getElementsByClassName('chekAll')[0].checked = true;
	}
	
}


	</script>
 <style>
 .input-group.input-group-unstyled input.form-control {
    -webkit-border-radius: 4px;
       -moz-border-radius: 4px;
            border-radius: 4px;
}
.input-group-unstyled .input-group-addon {
    border-radius: 4px;
    border: 0px;
    background-color: transparent;
}
 </style>   
</head>


<body>
<br>
<br>
<div id="middle">

<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Fixed Deposits Report</div>
<center><br>
<button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;margin-bottom: 10px;"  id="btnBack"><i class="fa  fa-arrow-left"></i></button>
<button type="button" class="btn btn-primary" onClick="window.location.href='FixedDeposit.php'" id="btnViewReport" style="margin-bottom: 10px;">Manage Fixed Deposits</button>
</center>
<center>

<form name="FixedDepositReport" id="FixedDepositReport" method="post">

<table align='center'>
	   
       <tr>
			<td>Category Name<?php echo $star; ?></td>
			<td><select name="category_id" id="category_id" onChange="get_ledger(this.value,document.getElementById('status').value);">
                <?php echo $combo_parentID = $obj_FixedDeposit->combobox("select `category_id`, `category_name` from `account_category` where `is_fd_category`= '1' ", 0); ?>
                </select></td>
		</tr>      
        
          <tr>
        		<td>FD Status</td>
                <td>
                <?php $attr = 'selected="selected"'; ?>
                	<select name="status" id="status"  style="width:100px;"  onChange="get_ledger(document.getElementById('category_id').value,this.value);">
                        <OPTION VALUE="0"  <?php echo $_REQUEST['status'] == '0' ? $attr : ''; ?> >All</OPTION>
                        <OPTION VALUE="1"  <?php echo $_REQUEST['status'] == '1' ? $attr : ''; ?>>Pending</OPTION>
                        <OPTION VALUE="2"  <?php echo $_REQUEST['status'] == '2' ? $attr : ''; ?>>Active</OPTION>
                        <OPTION VALUE="3"  <?php echo $_REQUEST['status'] == '3' ? $attr : ''; ?>>Renewed</OPTION>
                        <OPTION VALUE="4"  <?php echo $_REQUEST['status'] == '4' ? $attr : ''; ?>>Closed</OPTION>
                    </select>
                </td> 
        </tr>
         <tr>
        	<td valign="middle"><?php echo $star;?>Fixed Deposit Name</td>
			<td>
            	 <div class="input-group input-group-unstyled">
    				<input type="text" class="form-control" style="width:300px; height:30px;"  id="searchbox" placeholder="Search Fixed Deposit Account"   onChange="ShowSearchElement();"  onKeyUp="ShowSearchElement();" />
    			</div>
               </td>
		</tr> 
         <tr>
        	<td ></td>
			<td>
            	<div style="overflow-y:scroll;overflow-x:hidden;width:300px; height:150px; border:solid #CCCCCC 2px;" name="ledger_id[]" id="ledger_id"  >
                	<p id="msgDiv" style="display:none;"></p>
                    <div  id="ledgerDiv">
                	 <?php //echo $fd_purpose = $obj_FixedDeposit->comboboxForReport("select `id`,concat_ws(' - ', ledgertable.ledger_name,ledgertable.id) as ledger_name from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where  ledgertable.categoryid IN (".$fdAccountArray.") and society_id = '".$_SESSION['society_id']."' ","id"); ?>
					</div>
                </div>
            </td>
		</tr> 
        
        <tr><td  align="center" colspan="2"> <br/></td> </tr>     
		
        <tr>
        		<td  align="center"  colspan="2" >  
                	<input type="button" name="Fetch" id="Fetch" value="Fetch"  class="btn btn-primary"  onclick="FetchFDSummary();"   style="width:100px;" /> 
                	 <input  type="button" id="btnExport" value="Export To Excel"   class="btn btn-primary" onclick="Export()"  style="display:none;"/>
                     <input  type="button" id="Print" onClick="PrintPage()" name="Print!" value="Print" class="btn btn-primary" style="display:none;"/>	 
                </td>
          </tr>
        
</table>
</form>

<script>
	get_ledger(0,0);
</script>
<div id='showTable' style="font-weight:lighter;"></div>
</center>
</div>
</div>
<style>
#showTable{
    font-weight: lighter;
    width: 100%;
    overflow: hidden;
    overflow-x: overlay;
   
    overflow-y: overlay;
}
</style>
<?php include_once "includes/foot.php"; ?>
