<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Add Lien</title>
</head>
<?php
include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/tenant.class.php");
include_once("classes/mem_other_family.class.php");
include_once("classes/addLien.class.php");
//include_once("disableUrl.php");
//nclude_once("classes/mem_other_family.class.php");
//$obj_mem_other_family = new mem_other_family($m_dbConn);
$obj_tenant = new tenant($m_dbConn);
$obj_mem_other_family = new mem_other_family($m_dbConn);

$unit_details = $obj_mem_other_family->unit_details($_REQUEST['mem_id']);

$society_dets = $obj_mem_other_family->get_society_details($_SESSION['society_id']);
$UnitBlock = $_SESSION["unit_blocked"];
//print_r($unit_details);
//my code
$obj_addLien=new addLien($m_dbConn,$m_dbConnRoot);
//$unitRes=$obj_addLien->getUnitDetails();
//$docRes=$obj_addLien->getDocument();
/*echo "<pre>";
print_r($docRes);
echo "</pre>";*/
$memberId = $obj_addLien->getMemberId($_REQUEST['unit_id']);
//echo "MemberId:  ".$memberId;
$lienId=$_REQUEST['lienId'];
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/tenant_20190424.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="js/lien.js"></script>
     <script type="text/javascript" src="js/addLien.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
	<script type="text/javascript">
        NocDate = new Date();
		$(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true 
        }).datepicker("setDate", NocDate)});
		$(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics_Dob").datepicker(datePickerOptions)
		});

	</script>
  
	<script type="text/javascript">
		var datePickerOptions={ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0',
            buttonImageOnly: true ,
            defaultDate: '01-01-1980'
        };
	</script>
	<script type="text/javascript">
		//Lien related script
		var DocCount=1;
		var MaxInputs=10;
		$(function () 
		{
			$("#btnAddDoc").bind("click", function () 
			{
		//	alert("Add");
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
					alert("Maximum Limit reached:\nCannot add more than 10 documents")
				}
		//$(".basics_Dob").datepicker(datePickerOptions);
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
	//used to add new row for documents
	function addNewDocRow(value) 
	{
		var content="<td width='50%'> <input type='hidden' name='docId"+DocCount+"' id='docId"+DocCount+"'><input type='text' id='docName"+DocCount+"' name='docName"+DocCount+"'/></td><td width='50%'><input type='file' name='userfile"+DocCount+"' id='userfile"+DocCount+"' /></td>";
    	return (content);
	}
	//Used to display text box for closing date
	function displayTextBox(str)
	{
		//alert ("In display text Box");
		if(str=="Closed")
		{
			//alert ("In if ");
			document.getElementById("loanStatus").checked=false;
			document.getElementById("trLoanClosingDate").style.display="table-row";
			document.getElementById("trLoanOpeningDate").style.display='none';
		}
		else if(str=="Open")
		{
			document.getElementById("loanStatus").checked=false;
			document.getElementById("trLoanOpeningDate").style.display="table-row";
			document.getElementById("trLoanClosingDate").style.display='none';
		}
		else
		{
			if(document.getElementById("trLoanClosingDate").style.display=="table-row" || document.getElementById("trLoanOpeningDate").style.display=="table-row")
			{
				document.getElementById("trLoanClosingDate").style.display="none";
				document.getElementById("trLoanOpeningDate").style.display="none";
			}
			else
			{
			}
		}//document.getElementById("loanClosingDate").className="basics";
	}
	var iCounter=1;
	//Used to display new row for uploaded documents
	function addNewRow()
	{	
		var content="<tr><td><input type='hidden' id='docId"+iCounter+"' name='docId"+iCounter+"'><input type='text' name='docName"+iCounter+"' id='docName"+iCounter+"' readOnly></td><td><a id='fileLink"+iCounter+"' href='#'><input type='text' id='fileName"+iCounter+"' name='fileName"+iCounter+"'></a></td><td width='30%' style='text-align:center'><a onclick='deleteDocument("+iCounter+")' id='btnDelete"+iCounter+"'><img src='images/del.gif' style='width:70%;height:100%' /></a></tr>";
		$("#uploadedDocDetails > tbody").append(content);
		iCounter=iCounter+1;
	}
	$( document ).ready(function()
	{
		var method="<?php echo $_REQUEST['method'];?>";
		var UnitID="<?php echo $_REQUEST['unit_id'];?>";
		var loanstatus = document.getElementsByName("loanStatus");
		var lienId="<?php echo $_REQUEST['lienId']; ?>";
		if(lienId == null || lienId == "")
		{
			loanstatus[0].checked = true;
		}
		
		if( method == "edit" )
		{
			
			$.ajax
			({
				url : "ajax/lien.ajax.php",
				type : "POST",
				datatype: "JSON",
				data : {"method":"editLienDetails","lienId":lienId},
				success : function(data)
				{	
					var a		= data.trim();	
					var arr1	= new Array();
					var arr2	= new Array();
					arr1		= a.split("@@@");
					arr2		= JSON.parse(arr1[1].split("#"));
					
					console.log("arr2",arr2);
					//alert ("arr1:"+arr1);
					//alert ("arr2:"+arr2);
					document.getElementById('unitId').value=arr2['UnitId'];
					document.getElementById('bankName').value=arr2['BankName'];
					document.getElementById('bankName').readOnly=true;
					document.getElementById('loanAmount').value=arr2['Amount'];
					document.getElementById("societyNocDate").value = arr2['SocietyNOCDate'];
					document.getElementById("societyNocDate").readOnly=true;
					document.getElementById("societyNocDate").className="myClass";
					document.getElementById("loanOpeningDate").className="myClass";
					var loanstatus = document.getElementsByName("loanStatus");
					
					if(arr2['LienStatus'] == "NOC")
					{
						loanstatus[0].checked = true;
					}
					else if(arr2['LienStatus'] == "Open")
					{
						loanstatus[1].checked = true;
						loanstatus[0].disabled = true;
						document.getElementById('loanAmount').readOnly=true;
						document.getElementById("trLoanOpeningDate").style.display="table-row";
						document.getElementById('loanOpeningDate').value=arr2['OpeningDate'];
						document.getElementById('societyNocDate').readOnly=true;
						$("#societyNocDate").datepicker({minDate:-1,maxDate:-2}).attr('readonly','readonly');
						$("#loanOpeningDate").datepicker({minDate:-1,maxDate:-2}).attr('readonly','readonly');
					}
					
					document.getElementById('note').value=arr2['Note'];
					document.getElementById('lienId').value=arr2['Id'];
					document.getElementById('btnSubmit').value = "Update";
					document.getElementById('unitId').disabled;	
					document.getElementById("docTable").style.display="table";
					document.getElementById("heading").style.display="block";
					document.getElementById("hr").style.display="block";	
					$.ajax
					({
						url : "ajax/lien.ajax.php",
						type : "POST",
						datatype: "JSON",
						data : {"method":"editDocumentDetails","lien_ID":arr2['Id']},
						success : function(data1)
						{
							//alert ("res :"+data1);
							var a		= data1.trim();	
							var arr1	= new Array();
							var arr2	= new Array();
							arr1		= a.split("@@@");
							arr2		= arr1[1].split("#");
							//alert ("arr1:"+arr1);
							//alert ("arr2:"+arr2);
							var i=0,j=1;
							//alert ("arr2:"+arr2.length);
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
					});				
				}
			});
		}
	});
	
	
	function SetUnitValue()
	{
		document.getElementById('unitId').value =  document.getElementById('sunitId').value;
	}
	
	//Validation
	function validateForm()
	{
		var closingDate = document.forms["addLien"]["loanClosingDate"].value;
		var bankName = document.forms["addLien"]["bankName"].value;
		var amt = document.forms["addLien"]["loanAmount"].value;
		var NOCDate = document.forms["addLien"]["societyNocDate"].value;
		var ConDate = document.forms["addLien"]["loanOpeningDate"].value;
		var loanStatus = document.getElementsByName("loanStatus");
		if(loanStatus[1].checked)
		{
			if(ConDate == "")
			{
				alert ("Opening Date must be filled out.");
				return false;
			}
		}
		else if(loanStatus[2].checked)
		{
			if(closingDate == "")
			{
				alert ("Closing Date must be filled out.");
				return false;
			}
		}
		if(bankName == "")
		{
			alert ("Bank Name must be filled out..");
			return false;
		}
		/*if( amt == "")
		{
			alert ("Loan Amount must be filled out.")
			return false;
		}*/
		if( NOCDate == "")
		{
			alert ("Society NOC Date must be filled out.");
			return false;
		}
		var method = document.forms["addLien"]["btnSubmit"].value;
		if( method == "Submit")
		{
			var i = 1;
			var doc;
			var docCount = document.forms["addLien"]["doc_count"].value;
			for(i = 1; i <= docCount; i++)
			{
				var ChooseFile = document.forms["addLien"]["userfile"+i].value;
				
				if(ChooseFile != '')
				{
					doc = document.forms["addLien"]["docName"+i].value;
					if( doc == "")
					{
						alert ("Document Name must be filled out.");
						return false;
					}	
				} 
				
			}
		}
		
		$('#btnSubmit').css({"background-color":"gray","color":"black"});
		   
	}
	/*function checkLoanStatus() 
	{
    	var gender = document.getElementsByName("loanStatus");
    	var genValue = false;
		var i = 0;
    	for(i=0; i<gender.length;i++)
		{
            if(gender[i].checked == true)
			{
                genValue = true;    
            }
        }
        if(!genValue)
		{
            alert("Please Choose the Loan Status.");
            return false;
        }
		return true;
	}​*/
	</script>
    	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/solid.css" integrity="sha384-TbilV5Lbhlwdyc4RuIV/JhD8NR+BfMrvz4BL5QFa2we1hQu6wvREr3v6XSRfCTRp" crossorigin="anonymous">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/fontawesome.css" integrity="sha384-ozJwkrqb90Oa3ZNb+yKFW2lToAWYdTiF1vt8JiH5ptTGHTGcN7qdoR1F95e0kYyG" crossorigin="anonymous">
	</head>
	<body>
	<div id="middle">
		<div class="panel panel-info" id="panel" style="display:block; margin-top:6%;width:77%;">
      		<?php if($_REQUEST['method']=="edit")
	  		{
			?>
      			<div class="panel-heading" id="pageheader">Update Lien</div>
      		<?php 
	 		}
	  		else
			{?>
        		<div class="panel-heading" id="pageheader">Add Lien</div>
        	<?php 
			}?>
			<br>
            <center>
			<button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;margin-left:10%" id="btnBack"><i class="fa fa-arrow-left"></i>
    		</button>
			<?php 
			if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_MEMBER)
			{?>
				<input type="button" class="btn btn-primary" onClick="window.location.href='view_member_profile.php?prf&id=<?php echo $memberId;?>'"  style="float:right;" value="Go to profile view">
	
			<?php
            }
			else
			{ ?>
				<input type="button" class="btn btn-primary" onClick="window.location.href='view_member_profile.php?scm&id=<?php echo $memberId;?>&tik_id=<?php echo time();?>&m'"  style="" value="Go to profile view">
			<?php
			} 
			?>
            </center>
			<br>
			<?php //if(isset($_REQUEST['ter']))
			//{
				?>
<!--<p style="font-size:12px; color:red; font-weight:bold;">Please update Lease end date to new date on which you want to terminate the lease.</p>
		<?php 
			//}
		?>
		<br />
   <button type="button" class="btn btn-primary" onClick="window.location.href='view_member_profile.php'">Go to profile view</button>
	<br />-->
    
    	<?php 
			if(isset($_POST['ShowData']) || isset($_REQUEST['msg']))
			{ 
		?>
			<body onLoad="go_error();">
		<?php 
			} 
		?>
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
		<form name="addLien" id="addLien" method="post" action="process/addLien.process.php" enctype="multipart/form-data"  onSubmit="return validateForm();">
			<table align='center' id="data_table" width="100%">
           		 <tr>
                 	<input type="hidden" id="lienId" name="lienId">
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Select Unit No &nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                    	<select id="sunitId" name="sunitId" <?php if($_REQUEST['unit_id'] != "") { ?> disabled <?php } ?> onChange="SetUnitValue()">
                		<?php
							echo $obj_addLien->comboboxForUnitDetails("select u.unit_id, CONCAT(CONCAT(u.unit_no,' - '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' AND ownership_status = 1 ORDER BY u.sort_order",$_REQUEST['unit_id']);
						?>	
                        </select>
                        <input type="hidden" value="<?php echo $_REQUEST['unit_id'];?>" id="unitId" name="unitId"/>
            		</td>
                </tr>
                 <!--<tr>
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><b>Member Application No &nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<input type="text" id="memberApplicationNo" name="memberApplicationNo"/>        
            		</td>
                </tr>-->
        		<tr>
					<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Bank Name &nbsp;:&nbsp;</b></td>
                    <td width="10%" style="padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<input type="text" id="bankName" name="bankName"/>        
            		</td>
				</tr>
                <tr>
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Society NOC Date &nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<input type="text" id="societyNocDate" name="societyNocDate" class="basics" value = "" />        
            		</td>
                </tr>
                <tr>
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><b>Loan Amount&nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<input type="text" id="loanAmount" name="loanAmount" placeholder="Rs."/>        
            		</td>
                </tr>
               <tr>
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Loan Status &nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<input type="radio" name="loanStatus" id="loanStatus" value="NOC" onChange="displayTextBox(this.value)"/>&nbsp;<b>NOC</b>&nbsp;&nbsp;
                		<input type="radio" name="loanStatus" id="loanStatus" value="Open" onChange="displayTextBox(this.value)"/><b>Open</b>&nbsp;&nbsp;
  						<input type="radio" name="loanStatus" id="loanStatus" value="Closed" onChange="displayTextBox(this.value)"/><b>Closed</b></div> 
            		</td>
                </tr>
                <tr id="trLoanOpeningDate" style="display:none">
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Bank Noting Date &nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<input type="text" id="loanOpeningDate" name="loanOpeningDate" class="basics"/>        
            		</td>
                </tr>
                <tr id="trLoanClosingDate" style="display:none">
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Loan Closing Date &nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
                	<td style="width:10%;padding-top:1%"><input type="text" id="loanClosingDate" name="loanClosingDate" class="basics" onBlur="checkClosingDate()"/></td>
                </tr>
                <tr>
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><b>Note &nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<textarea id="note" name="note" cols="70" rows="4"></textarea>   
            		</td>
                </tr>
                <tr id="trHide" style="display:none">
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><b>Document &nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%" id="document1">  
            		</td>
                </tr>
	 	</table>
        <center>
        <table width="50%" id="uploadedDocDetails" style="display:none">
        	<tr>
            	<td colspan="3"><hr></td>
            </tr>
        	<tr>
            	<td style="text-align:center" colspan="3"><b>-: UPLOADED DOCUMENTS :-</b></td>
            </tr>
            <tr>
            	<td width="30%">Document Name:</td>
                <td width="30%">File Name:</td>
                <td width="30%">Delete</td>
            </tr>
        </table>
        </center>
        <div id="hr"><hr></div>
        <center>
        <div id="heading"><strong><u><i class="fas fa-file-alt" style="font-size: 12px;"></i>&nbsp; LIEN RELATED DOCUMENTS &nbsp;<i class="fas fa-file-alt" style="font-size: 12px;"></i></u></strong></div>
        <br>
       	<table width="60%" style="text-align:center" id="docTable">
            <tr>
            	<td width="30%" style="padding-top:1%;text-align:left"><b>Enter document Name &nbsp;:&nbsp;</b></td>
                <td width="40%" style="padding-top:1%;text-align:left"><b>Choose File &nbsp;:&nbsp;</b></td>
                <td width="10%"></td>
            </tr>
            <tr>
            	<input type="hidden" name="doc_count" id="doc_count" value="1">
                <td width="30%" styl="text-align:left">
          	    	<input type="text" id="docName1" name="docName1"/>
                    <input type="hidden" name="docId1" id="docId1">
                </td>
                <td width="20%"><input type="file" name="userfile1" id="userfile1" /></td>
                <td width="10%"><input id="btnAddDoc" type="button" value="Add More" /></td>
            </tr>
        </table>
        <br>
        <input type="button" id="printButton" name="printButton" style="display:none" value="Print" onClick="printFunction()" class="btn btn-primary" />
        <input type="submit" id="btnSubmit" name="btnSubmit" value="Submit" class="btn btn-primary"/><input type="button" id="btnCancel" name="btnCancel" class="btn btn-primary" value="Cancel" style="width:10%;margin-left:10%"/>
         <br>
        </center>
	</form>
</body>
</div>
</div>
</html>
<?php include_once "includes/foot.php"; ?>