<?php
include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/lien.class.php");
$obj_lien=new lien($m_dbConn,$m_dbConnRoot);
$lienId=$_REQUEST['lienId'];

$lienDetails=$obj_lien->getLienByLienId($lienId);
$documentDetails=$obj_lien->getDocumentsByLienID($lienId,DOC_TYPE_LIEN_ID);
$unitDetails=$obj_lien->getUnitNoAndOwnerDetails($lienDetails[0]['UnitId']);

$lien_status = $lienDetails[0]['LienStatus'];
if($lienDetails[0]['LienStatus'] == LIEN_ISSUED)
{
		$lien_status = $lienDetails[0]['LienStatus']." Issued";
}
$headerDetails=$obj_lien->getHeader();
//echo "<pre>";
//print_r($documentDetails);
//echo "</pre>";
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
		$( document ).ready(function()
		{
			//document.getElementById("header").style.display="none";
		});
		var iView=1;
		function addNewRowForView()
		{	
			iView=iView+1;
			var content="<td style='text-align:right;width:40%;vertical-align:center;padding-top:1%'></td><td style='width:10%;padding-top:1%'></td><td width='50%' style='padding-top:1%' id='document"+iView+"'></td>";
			$("#data_table > tbody").append(content);
		}
		function printFunction()
		{
			//disableLink();
			//$('.doc-link').bind('click', false);
			/*var count = document.getElementById("uDocCount").value;
			for(var i=0;i<count;i++)
			{
				document.getElementById("docLink"+i).style.display="none";
			}*/
			var divElements = document.getElementById('details').innerHTML;
			//var headElements = document.getElementById('header').innerHTML;
        	var oldPage = document.body.innerHTML;
           	document.body.innerHTML = "<html><head><title></title></head><body><center>" + divElements + "</center></body></html>";
        	//Print Page
        	window.print();
        	//Restore orignal HTML
        	document.body.innerHTML = oldPage;
			/*var count = document.getElementById("uDocCount").value;
			for(var i=0;i<count;i++)
			{
				document.getElementById("docLink"+i).style.display="table-cell";
			}*/
		}
	</script>
	</head>
	<body>
		<div id="middle">
			<div class="panel panel-info" id="panel" style="display:block; margin-top:6%;width:77%;">
      			<div class="panel-heading" id="pageheader">View Lien</div>
				<form name="viewLien" id="viewLien" method="post" action="#" enctype="multipart/form-data">
                	<center>
                    <br>
                    <table  id="details" width="100%">
                    	<tr style="display:none">
                        	<td align="center">
                 				<table align='center' id="header" width="80%" style="font-size:16px">
									<tr>
										<td align="center">
											<b><?php echo $headerDetails[0]['society_name'];?><b>
										</td>
									</tr>
									<tr>
										<td align="center">
											<b><?php echo $headerDetails[0]['society_add'];?></b>
										</td>
									</tr>
									<tr>
										<td align="center">
											<b><?php echo $headerDetails[0]['registration_no'];?></b>
                             				<br>
                             				<hr>
										</td>
									</tr>
                     				<tr>
                        				<td align="center"><b>Lien / Mortgage Details</b>
                                     		<br>
                                    		<br>
                                    	</td>
                     				</tr>
				 				</table>
           					</td>
                  		</tr>
                 		<tr>
                        	<td align="center">
                 				<table align='center' width="60%">
                    				<tr>
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%;  white-space: nowrap;"><b>Unit No &nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo $unitDetails[0]['unit_no']; ?>
                        				<br>
                        				</td>
                					</tr>
                    				<tr>
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%; white-space: nowrap;"><b>Owner Name &nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo $unitDetails[0]['owner_name']; ?>
                        				<br>
                        				</td>
                					</tr>
        							<tr>
										<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%;white-space: nowrap;"><?php echo $star;?><b>Bank Name &nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo $lienDetails[0]['BankName'];?>
                         				<br>
                        				</td>
									</tr>
                					<tr>
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%;white-space: nowrap;"><?php echo $star;?><b>Loan Amount&nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo $lienDetails[0]['Amount']?>
                    					<br>
                    					</td>
                					</tr>
                					<tr>
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%;white-space: nowrap;"><b>Society NOC Date &nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo $lienDetails[0]['SocietyNOCDate']?>
                    					<br>
                    					</td>
                					</tr>
                                    <?php 
									if(strtolower($lienDetails[0]['LienStatus']) == LIEN_OPEN && !empty($lienDetails[0]['OpeningDate']) && $lienDetails[0]['OpeningDate'] <> '0000-00-00')
									{
									?>
                					<tr  id="trLoanClosingDate">
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%;white-space: nowrap;"><?php echo $star;?><b>Bank Noting Date &nbsp;:&nbsp;</b></td>
                						<td style="width:10%;padding-top:1%"><?php echo $lienDetails[0]['OpeningDate']?>
                    					<br>
                    					</td>
                					</tr>
                					<?php
									}
									?>
                					<tr>
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%;white-space: nowrap;"><?php echo $star;?><b>Loan Status &nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo $lien_status?>
                    					<br>
                    					</td>
                					</tr>
                					<?php
									if(strtolower($lienDetails[0]['LienStatus']) == LIEN_CLOSED)
									{
									?>
                					<tr  id="trLoanClosingDate">
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%;white-space: nowrap;"><?php echo $star;?><b>Loan Closing Date &nbsp;:&nbsp;</b></td>
                						<td style="width:10%;padding-top:1%"><?php echo $lienDetails[0]['CloseDate']?>
                    					<br>
                    					</td>
                					</tr>
                					<?php
									}
									?>
                					<tr>
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%;white-space: nowrap;"><b>Note &nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo $lienDetails[0]['Note']?>
                   						<br>
                    					</td>
                					</tr>
                					<tr id="trHide">
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%;white-space: nowrap;"><b>Document &nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><a id="docLink0" target="_blank" href="https://docs.google.com/viewer?srcid=<?php echo $documentDetails[0]['attachment_gdrive_id']; ?>&pid=explorer&efh=false&a=v&chrome=false&embedded=true"><?php echo $documentDetails[0]['Name']?></a>
                    					<br><input type="hidden" id="uDocCount" name="uDocCount" value="<?php echo sizeof($documentDetails);?>">
                    					</td>
                					</tr>
                					<?php 
									
									for($i=1;$i<sizeof($documentDetails);$i++) 
									{
									?>
                                    <!--https://docs.google.com/viewer?srcid=<?php //echo $documentDetails[$i]['attachment_gdrive_id'];?>&pid=explorer&efh=false&a=v&chrome=false&embedded=true-->
                					<tr>
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%;white-space: nowrap;"></td>
                    					<td width="50%" style="padding-top:1%"><a id="docLink<?php echo $i?>" target="_blank" href="https://docs.google.com/viewer?srcid=<?php echo $documentDetails[$i]['attachment_gdrive_id']; ?>&pid=explorer&efh=false&a=v&chrome=false&embedded=true"><?php echo $documentDetails[$i]['Document'];?></a>
                    					<br>
                    					</td>
                					</tr>
                					<?php
									}
									?>
	 							</table>
                       		</td>
                   		</tr>
              		</table>
        	</center>
        <center>
        <br>
        <br>
        <table>
        	<tr>
        		<td><input type="button" id="printButton" name="printButton" value="Print" onClick="printFunction()" class="btn btn-primary" />
                </td>
                <?php
				if($obj_lien->checkAccess() == 0 && ($lienDetails[0]['LienStatus'] == LIEN_ISSUED || $lienDetails[0]['LienStatus'] == LIEN_OPEN))
				{
				?>  
                <td style="padding-left:10%"><a id="addLien" href="addLien.php?method=edit&unit_id=<?php echo $lienDetails[0]['UnitId'];?>&lienId=<?php echo $_REQUEST['lienId'];?>"><input type="button" id="editButton" name="editButton" value="Edit" class="btn btn-primary" /></a>
                </td>
                <?php
				}
				?>
                <td style="padding-left:20%"><input type="button" id="btnCancel" name="btnCancel" class="btn btn-primary" value="Cancel" ></td>
            </tr>
         </table>
         <br>
        </center>
	</form>
</body>
</div>
</div>
</html>
<?php include_once "includes/foot.php"; ?>