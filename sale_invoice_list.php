<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Invoices (Sales)</title>
</head>

<?php include_once("includes/head_s.php");
   include_once("classes/dbconst.class.php");
   include_once("classes/genbill.class.php");
   include_once("classes/include/dbop.class.php");
   $dbConn = new dbop();
   $invoice_unit_data=[];

   
   $obj_genbill = new genbill($dbConn);
   
   //Fetching All the details for sale invoioce from sale invoice table and unit
   if(!isset($_REQUEST['Note']))
   {
   	
   	$list_sale_invoice = $obj_genbill->getSaleInvoicORDebitCreditNoteDetail();
   }
   else
   {
   	$list_sale_invoice = $obj_genbill->getSaleInvoicORDebitCreditNoteDetail(true);
   }
   
   
   ?>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
<style>
   .link{display:inline}
   .link {float: left}
   .disabled {
   pointer-events: none;
   cursor: default;
   }
</style>
<body onLoad="go_error();">
   <br>
   <center>
      <div class="panel panel-info" id="panel" style="display:none">
         <div class="panel-heading" id="pageheader"><?php if(!isset($_REQUEST['Note'])){ echo "Invoices (Sales)";}else{ echo "Debit / Credit (Note)";} ?></div>
         <center>
            <br>
            <?php if($_SESSION['is_year_freeze'] == 0 && ($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN) || $_SESSION['profile'][PROFILE_CREATE_INVOICE] == '1')
               {
               if(!isset($_REQUEST['Note'])) {
                 //**** Create Invoice Button only Show to super admin and admin only ?>
            <button type="button" class="btn btn-primary" onClick="window.open('Invoice.php?add', '_blank'); return true;" style="float:center;margin-right:0%">Create New Invoice</button>
            <button type="button" class="btn btn-primary" onClick="window.open('import_invoice.php', '_blank'); return true;" style="float:center;margin-right:0%">Import Invoice</button>
             <button type="button" class="btn btn-primary" onClick="open_range()" style="float:center;margin-right:0%">Export PDF</button>
            <br><br>
            <div id="loader" style="display: none;"><img id="loading-image" src="images/loader/page-loader_2.gif" alt="Loading..." /></div>
            <button type="button" class="btn btn-primary" id="download_invoice" onClick="download_invoice()" style="float:center;margin-right:0%;display: none;">Download Exported PDF</button>
            <?php	  }
               else
               { ?>
            <font color="#0033CC">Note : </font><font color="#ff2845"><b>If you want immediate effect on ledger balance then create Credit / Debit Note OR  You want ledger effect in next bill then <a href="reverse_charges.php?&uid=0" target="_blank">create Reverse/ Debit charge (Fine)</a> .</b></font><br/><br/><br/>	
            <button type="button" class="btn btn-primary" onClick="window.open('Invoice.php?add_credit&NoteType=<?php echo CREDIT_NOTE;?>', '_blank'); return true;" style="float:center;margin-right:0%">Add Credit Note</button>
            <button type="button" class="btn btn-primary" onClick="window.open('Invoice.php?add_debit&NoteType=<?php echo DEBIT_NOTE;?>', '_blank'); return true;" style="float:center;margin-right:0%">Add Debit Note</button>
            <button type="button" class="btn btn-primary" onClick="window.open('import_invoice.php?Note', '_blank'); return true;" style="float:center;margin-right:0%">Import Credit Note</button>
            <?php }
               }?>
            <table align="center" border="0" id="example" class="display" style="width:100%;padding-top:20px;">
               <thead>
                  <tr>
                     <th>Sr No.</th>
                     <?php if(!isset($_REQUEST['Note']))
                        {?>
                     <th>Invoice No.</th>
                     <th>Invoice Date</th>
                     <?php }
                        else{?>
                     <th>Voucher Number</th>
                     <th>Date</th>
                     <th>Bill Type</th>
                     <th>Note Type</th>
                     <?php }?>
                     <th style="width:10%;text-align:right;">Bill To</th>
				<th>GSTIN No.</th>
                     <th style="text-align:center;"><?php if(!isset($_REQUEST['Note'])) { echo "Member/Ledger" ;}else {echo "Member/Ledger"; }?></th>
                     <th>Sub Total</th>
                     <th>CGST</th>
                     <th>SGST</th>
                     <th>Total Amt</th>
                     <th>Edit</th>
                     <th>Delete</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     $counter="";
                     $EncodeUrl;
                        			for($i = 0; $i < sizeof($list_sale_invoice); $i++)
                     {
						  $TaxLedger = $list_sale_invoice[$i]['LedgerIDS'];
						 if($TaxLedger <> '')
						 {
							$getTaxLedger = $obj_genbill->GetTaxLedgerName($TaxLedger); 
						 }
                     		if(sizeof($list_sale_invoice[$i]['UnitID']) > 0)
                     		{
                     			$EncodeUnitArray = json_encode($list_sale_invoice[$i]['UnitID']);
                     			$EncodeUrl = urlencode($EncodeUnitArray);
                     		}
                     		$counter=$i+1;	
                     	
                     	//*** Link to member ledger report by passing parameter UnitID and Invoice Numner
                     	 
                     	$Url = "member_ledger_report.php?&uid=".$list_sale_invoice[$i]['UnitID']."&Cluster=".$EncodeUrl;
                     	$LedgerURL = "view_ledger_details.php?lid=".$list_sale_invoice[$i]['LedgerID']."&gid=".$list_sale_invoice[$i]['group_id'];
                     	?>
                  <tr valign="center">
                     <td align="center"><?php echo $counter;?></td>
                     <?php if(!isset($_REQUEST['Note']))
                        {

                        $invoice_unit_data['inv_no_'.$list_sale_invoice[$i]['Inv_Number'].'']=$list_sale_invoice[$i]['UnitID'];

                        array_push($invoice_unit_data,$invoice_unit_data['inv_no_'.$list_sale_invoice[$i]['Inv_Number'].'']);

                       ?>
                     <td align="center" id="inv_no_<?php echo $list_sale_invoice[$i]['Inv_Number'];?>" inv_unit_no="<?php echo $list_sale_invoice[$i]['UnitID'];?>" ><?php echo PREFIX_INVOICE_BILL.' - '.$list_sale_invoice[$i]['Inv_Number'];?></td>
                     <?php }else{?>
                     <td align="center"><?php 
                        if($list_sale_invoice[$i]['Note_Type'] == CREDIT_NOTE)
                        {
                        	echo PREFIX_CREDIT_NOTE.'-';
                        }
                        else if($list_sale_invoice[$i]['Note_Type'] == DEBIT_NOTE)
                        {
                        	echo PREFIX_DEBIT_NOTE.'-';
                        }
                        echo $list_sale_invoice[$i]['Note_No'];?></td>
                     <?php }?>                         
                     <td><?php echo getDisplayFormatDate($list_sale_invoice[$i]['Date']);?></td>
                     <?php if(isset($_REQUEST['Note'])){?>
                     <td><?php if($list_sale_invoice[$i]['BillType'] == 0){ echo 'Maintenance';}else{ echo  'Supplementry';}?></td>
                     <td><?php if($list_sale_invoice[$i]['Note_Type'] == CREDIT_NOTE){ echo 'Credit Note';}else { echo 'Debit Note';}?></td>
                     <?php }?>
                     <?php //*** Link to ViewMemberProfile with MemberID ?>
                     <?php if(sizeof($list_sale_invoice[$i]['group_id']) <> 0)
                        { ?>
                     <td style="width:10%;text-align:center;">
                        <a href="" onClick="window.open('<?php echo $LedgerURL; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');">
                           <?php echo $list_sale_invoice[$i]['LedgerID']?>
                     </td>
                     <td style="width:20%;text-align:center;"><a href="" onClick="window.open('<?php echo $LedgerURL; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');"><?php echo $getTaxLedger?></td>
                     <?php }
                        else {?>
                     <td style="width:10%;text-align:center;">
                     <?php if($list_sale_invoice[$i]['member_id'] != '')
					 {?>
                    	 <a href="view_member_profile.php?scm&id=<?php echo $list_sale_invoice[$i]['member_id'];?>&tik_id=<?php echo time();?>&m&view" target="_blank"><?php echo $list_sale_invoice[$i]['unit_no'];?></a>
                         <?php 
						 }
						 else
						 {?>
							<a href="#"><?php echo $list_sale_invoice[$i]['unit_no'];?></a>
						 <?php  }
						 ?></td>
							<td><?php echo (empty($list_sale_invoice[$i]['GSTIN_No']))?'--':$list_sale_invoice[$i]['GSTIN_No'];?></td>
                     <td style="text-align:center;"><a href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');"><?php echo $getTaxLedger;?></a></td>
                     <?php }?>
                     <td align="center"><?php echo $list_sale_invoice[$i]['SubTotal'];?></td>
                     <td align="center"><?php echo $list_sale_invoice[$i]['CGST'];?></td>
                     <td align="center"><?php echo $list_sale_invoice[$i]['SGST'];?></td>
                     <?php if(!isset($_REQUEST['Note'])){?>
                     <td align="center"><a href="Invoice.php?UnitID=<?php echo $list_sale_invoice[$i]['UnitID'];?>&inv_number=<?php echo $list_sale_invoice[$i]['Inv_Number'];?>&id=<?php echo $list_sale_invoice[$i]['ID'];?>"  target="_blank"><?php echo $list_sale_invoice[$i]['TotalPayable'];?></a></td>
                     <?php 
					 if($_SESSION['is_year_freeze'] == 0)
					 {?>
					 	<td align="center"><a href="Invoice.php?UnitID=<?php echo $list_sale_invoice[$i]['UnitID'];?>&inv_number=<?php echo $list_sale_invoice[$i]['Inv_Number'];?>&id=<?php echo $list_sale_invoice[$i]['ID'];?>&edt"  target="_blank"><img src="images/edit.gif" /></a></td>
                     <td align="center"><img src="images/del.gif" onClick="deleteInvoice('<?php echo $list_sale_invoice[$i]['ID'];?>','<?php echo $list_sale_invoice[$i]['UnitID'];?>','<?php echo $list_sale_invoice[$i]['Date'];?>')"/></td>
					 <?php 
					 }
					 else
					 {?>
					  <td align="center" ></td>
                      <td align="center" ></td>
					  <?php 
					  }?>
                     
                     <?php }
                        else
                        { ?>
                     		<td align="center"><a href="Invoice.php?debitcredit_id=<?php echo $list_sale_invoice[$i]['ID'];?>&UnitID=<?php echo $list_sale_invoice[$i]['UnitID'];?>&NoteType=<?php echo $list_sale_invoice[$i]['Note_Type'];?>"  target="_blank"><?php echo $list_sale_invoice[$i]['TotalPayable'];?></a></td>
                      		<?php 
					 		if($_SESSION['is_year_freeze'] == 0)
					 		{?>
                     			<td align="center"><a href="Invoice.php?UnitID=<?php echo $list_sale_invoice[$i]['UnitID'];?>&debitcredit_id=<?php echo $list_sale_invoice[$i]['ID'];?>&NoteType=<?php echo $list_sale_invoice[$i]['Note_Type'];?>&edt"  target="_blank"><img src="images/edit.gif" /></a></td>
                     			<td align="center"><img src="images/del.gif" onClick="deleteDebitorCredit('<?php echo $list_sale_invoice[$i]['ID'];?>','<?php echo $list_sale_invoice[$i]['Note_Type'];?>')" /></td>
                     		<?php 
					 		}
					 		else
					 		{?>
					 			 <td align="center" ></td>
                     			 <td align="center" ></td>
							<?php 
							}?>
                  <?php }?>
                     <!-- This is Delete Button only for Super Admin and Admin only From here you delete the Invoice-->
                  </tr>
                  <?php	}?>
               </tbody>
            </table>
         </center>
      </div>
   </center>
   <div class="container">
      <!-- Modal -->
      <div class="modal fade" id="Range_Modal" role="dialog">
         <div class="modal-dialog modal-sm">
            <div class="modal-content">
               <div class="modal-header" >
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <center>
                     <h4 class="modal-title" style="color: #43729F;"><b>Export Invoice PDF</b></h4>
                  </center>
               </div>
               <form>
                  <div class="modal-body" id="modal_body">
                     <span>Invoice Range </span> <input type="text" name="inv_range"  id="inv_range" required="" placeholder="eg. 1-10 ">
                     <br>
                     <br>
                     <span id="range_error" style="display: none;color: red;"></span>
                  </div>
                  <div class="modal-footer">
                     <button type="submit" class="btn btn-primary" name="submit_exportpdf" id="submit_exportpdf" data-dismiss="modal">Submit</button>
                     <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
   <div id="frame_div" style="display: none;"></div>
   <script src="js/jsCommon_20190326.js"></script>
</body>
<?php include_once "includes/foot.php"; ?>
<script type="text/javascript">
   function open_range()
   {
        $("#Range_Modal").modal('show');
   
   }
   
   $('#submit_exportpdf').click(function(){
    
    $('#download_invoice').css('display','none');
    $('#frame_div').empty();
    localStorage.removeItem('invoice_range_from');
    localStorage.removeItem('invoice_range_to');
    
    invoice_download_data = [];
    var inv_range=$('#inv_range').val();
    if(inv_range.length==0)
    {
      $('#range_error').text('Please Enter Invoice Range');
     $('#range_error').css('display','block');
     return false;
    }
    var check_inv_range= inv_range.match(/-/g);
    console.log(inv_range);
    console.log(check_inv_range);
   
    if( check_inv_range!=null && check_inv_range.length==1){
   
      var data=inv_range.split('-');
      var inv_from=parseInt(data[0]);
      var inv_to=parseInt(data[1]);
      if(data.length==2 && Number.isInteger(inv_from) && Number.isInteger(inv_to)) {
      
      if(isNaN(inv_from) || isNaN(inv_to))
      {
        $('#range_error').text('Please Enter Invoice Range');
        $('#range_error').css('display','block');
        return false;
   
      }
      else if(inv_from==0 || inv_to==0)
      {
        $('#range_error').text('Please Enter Range Greater Than 0');
        $('#range_error').css('display','block');  
        return false;
   
      }
      else if( inv_from > inv_to)
      {
        $('#range_error').text('Please enter proper range ');
        $('#range_error').css('display','block');  
        return false;
   
      }
      else
      {   
          $('#range_error').text('');
          $('#range_error').css('display','none'); 
          $('#loader').css('display','block');

          localStorage.setItem('invoice_range_from',inv_from); 
          localStorage.setItem('invoice_range_to',inv_to); 

         var total_inv=(inv_to-inv_from)+1;
         for(var inv_no=inv_from; inv_no<=inv_to; inv_no++){
              expoert_invoice_pdf(inv_no,total_inv);
          }

   
      }
    }
    else
    {
     $('#range_error').text('Please enter range eg. "1-10" ');
     $('#range_error').css('display','block');  
     return  false
    }
   
   }
   else
   {
   $('#range_error').text('Please enter range eg. "1-10" ');
   $('#range_error').css('display','block');  
   return  false
   }
    
   });
</script>
<script type="text/javascript">
  var invoice_download_data = [];
  var invoice_exported=0;

   function expoert_invoice_pdf(inv_no,total_inv) {   
       
       var invoice_unit_data='<?php echo json_encode($invoice_unit_data); ?>';
       var unit_data=JSON.parse(invoice_unit_data);
       var td_inv_id='inv_no_'+inv_no ;
       var unit_id=unit_data[td_inv_id];

       if(unit_id !=null && unit_id !='' && unit_id !='undefined'){
    
      var inv_data = {};
      inv_data['inv_no'] = inv_no;
      inv_data['unit_id'] = unit_id;

      invoice_download_data.push(inv_data);


       var downLoadLink = "Invoice.php?UnitID="+unit_id+"&inv_number="+inv_no+"";
       var sTarget = "pdfexport_" + inv_no;
   
       $('#frame_div').append('<iframe src="'+downLoadLink+'" id='+sTarget+'></iframe>');
    
       var iframe = $('#pdfexport_'+inv_no);
   
       iframe.on('load', function() {
                // iframe content
                var iframeContents = $('#pdfexport_'+inv_no).contents();
   
                // find #message in iframe contents
                var element = iframeContents.find('#bill_main');
   
                // get element value
                var sData = element.html();
                 var sHeader = '<html><head>';
                  sHeader += '<style> ';
                  sHeader += 'table { border-collapse: collapse; } ';
                  sHeader += 'table, th, td { border: 0px solid black; text-align: left; padding-top:0px; padding-bottom:0px; } ';
                  sHeader += '</style>';  
                  sHeader +=  '</head><body>';
                  
                  var sFooter =  '</body></html>';
                  
                  sData = sHeader + sData + sFooter;
                  var sFileName="Inv-<?php echo $_SESSION['society_id'] ?>-"+unit_id+"-"+inv_no;
                 $.ajax({
                    url : "viewpdf.php",
                    type : "POST",
                    data: { 
                           "data":sData, 
                           "filename":sFileName, 
                           "society": "<?php echo $_SESSION['society_id'] ?>",
                           "BT" : "<?php echo TABLE_SALESINVOICE; ?>","bDownload":'1'
                         } ,
                    success : function(data)
                    { 
                      invoice_exported++;
                      if(invoice_exported==total_inv)
                      {
                        invoice_exported=0;

                        $('#download_invoice').css('display','block');
                        $('#loader').css('display','none');

                      }
                    },
                      
                    fail: function()
                    {
                    },
                    
                    error: function(XMLHttpRequest, textStatus, errorThrown) 
                    {
                    }
                  });                
            });
      }
      else
      {
        // alert(inv_no);
        invoice_exported++;

      }
   }
</script>
<script type="text/javascript">
  function download_invoice()
  {
    var invoice_data=JSON.stringify(invoice_download_data);
    console.log(invoice_data.length);
    if(invoice_data=="[]")
    {
      return false;
    }
    
    var invoice_range_from = localStorage.getItem('invoice_range_from');
    var invoice_range_to = localStorage.getItem('invoice_range_to'); 

    var downLoadZipLink = "download_invoice.php?society_id="+<?php echo $_SESSION['society_id'] ?>+"&invoice_range_from="+invoice_range_from+"&invoice_range_to="+invoice_range_to+"&invoice_data="+invoice_data;

    invoice_download_data=[];
  
    window.open(downLoadZipLink, '_blank');


 
  }
</script>