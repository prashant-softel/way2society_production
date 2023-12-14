<?php include_once("classes/dbconst.class.php");
$bIsHide = bIsReportOrValidationPage($scriptName);?>
<link href="dist/css/sb-admin-2.css" rel="stylesheet">


   
  <div id="mySidenav" class="sidenav">
<?php if($_SESSION["role"] == ROLE_ADMIN_MEMBER || $_SESSION["role"] == ROLE_MANAGER)
		{ ?>
        
         <a target="_blank" href="dues_advance_frm_member_report.php?&sid=<?php echo $_SESSION['society_id']; ?>" id= "about"><span class="fa fa-arrow-up fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;Dues from Member</span>
         </a>
  <?php }
  	 else
		{?>
          <a href="#" OnClick="window.open('ledger.php','QuickLedgerLink','type=fullWindow,fullscreen,scrollbars=yes')" id="about"><span class="fa fa-L fa-5x" style="font-size:10px;font-size:2.5vw;float:left;margin-left: 2%;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;&nbsp;Ledger</span></a>
          
 
  <?php }?>
  <?php if($_SESSION["role"] == ROLE_ADMIN_MEMBER || $_SESSION["role"] == ROLE_MANAGER)
		{ ?>
        
         <a href="list_member.php" id="blog" target="_blank"><span class="fa fa-list-ol fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;View Members List</span></a>               
                          
  <?php }
  		else
		{?>
  		                   
           <a href="genbill.php" id="blog" target="_blank"><span class="fa fa-edit fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;Generate Bill</span></a>
  <?php }?>
  
  
  	 <a href="BankAccountDetails.php" id="projects"target="_blank"><span class="fa fa-bank fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float:left;">&nbsp;&nbsp;&nbsp;Bank and Cash</span></a>
  
  
   <?php if($_SESSION["role"] == ROLE_SUPER_ADMIN || $_SESSION["role"] == ROLE_ADMIN || $_SESSION["role"] == ROLE_MANAGER || $_SESSION["role"] == ROLE_ACCOUNTANT )
		{ ?>
        	
          <a href="financial_reports.php" target="_blank" id="contact"><span class="fa fa-signal fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;Financial Reports</span></a>
          
          <a href="#" OnClick="window.open('view_ledger_details.php?lid=<?=$_SESSION['default_suspense_account']?>&gid=<?=LIABILITY?>','QuickLedgerLink','type=fullWindow,fullscreen,scrollbars=yes')" id="suspenseSideId"><span class="fa fa-S fa-5x" style="font-size:10px;font-size:2.5vw;float:left;margin-left: 2%;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;&nbsp;Suspense Ledger</span></a>

          <a href="#" OnClick="window.open('view_ledger_details.php?lid=<?=$_SESSION['default_tds_payable']?>&gid=<?=LIABILITY?>','QuickLedgerLink','type=fullWindow,fullscreen,scrollbars=yes')" id="tdsSideId"><span class="fa fa-T fa-5x" style="font-size:10px;font-size:2.5vw;float:left;margin-left: 2%;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;&nbsp;TDS Payable</span></a>
  <?php }?>
  <?php if(($_SESSION["role"] == ROLE_MANAGER) && $_SESSION['profile'][PROFILE_GENERATE_BILL] == 1)
  {?>
  	<a href="genbill.php" id="Genbillid" target="_blank"><span class="fa fa-edit fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;Generate Bill</span></a>
  <?php }
  if(($_SESSION["role"] == ROLE_MANAGER ) && $_SESSION['profile'][PROFILE_GENERATE_BILL] == 0 )
  {?>
  <a href="genbill.php" id="Genbillid" target="_blank"><span class="fa fa-edit fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;View Bills</span></a>
<?php }
  ?>
  	 <?php if($_SESSION["role"] == ROLE_ADMIN_MEMBER)
	   {?>
      
                            
       <a href="#" OnClick="window.open('ledger.php','QuickLedgerLink','type=fullWindow,fullscreen,scrollbars=yes')" id="contact"><span class="fa fa-L fa-5x" style="font-size:10px;font-size:2.5vw;float:left;margin-left: 5%;"></span><span style="float: left;"> &nbsp;&nbsp;&nbsp;Manage Ledger</span></a>
<?php if($_SESSION['profile']['genbill.php'] == 1)
	   {?> 
         <a href="genbill.php" id="contact1" target="_blank"><span class="fa fa-edit fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;Generate Bill</span></a>
       <?php }?>
       
 <?php }?>
 
  <!--<a href="#" id="contact">Contact</a>-->
</div>                  
