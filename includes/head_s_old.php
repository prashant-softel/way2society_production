<?php 
if(!isset($_SESSION)){ session_start(); }
//include_once("classes/include/dbop.class.php");
include_once("classes/include/check_session.php");
include_once("classes/head_s.class.php");
include_once("header.php");
include_once("classes/tips.class.php");
$obj_tips=new tips($m_dbConnRoot,$m_dbConn);
$SessionUser=  $_SESSION['View'];
if($_REQUEST["View"] == "ADMIN")
{
	$_SESSION["View"] = "ADMIN";
	//$_SESSION[name] = "SUPER ADMIN";
}
else if($_REQUEST["View"] == "MEMBER")
{
	$_SESSION["View"] = "MEMBER";
	//$_SESSION[name] = "SUSHIN SHETTY";
	
}
if($_SESSION['role'] == 'Master Admin')
{
include_once("includes/header2.php");
}
else
{
	include_once("includes/header.php");
}
if($SessionUser <> '' && $SessionUser != "ADMIN")
{


//if(isset($SessionUser))
//{
include_once("RightPanel.php");
}
else if($SessionUser == "ADMIN")
{
	include_once("RightPanel_admin.php");
}
$m_objHead_S = new head_s($m_dbConn);

//This query is written for get member id 

$query = "SELECT member_id FROM member_main WHERE unit = '".$_SESSION['unit_id']."' and ownership_status = 1";
$result = $m_dbConn->select($query);
$Member_id = $result[0]['member_id'];

// end

include_once("datatools.php");
$bIsHide = bIsReportOrValidationPage($scriptName);
$TipsDetails = $obj_tips->RecordsCount();
?>
<script>
var aryTips=[];
var currentTip=-1;
/*localStorage.setItem('dbname', "<?php //echo $_SESSION['dbname']; ?>");
$(window).bind('storage', function (e) 
{
//alert("Old : " + e.originalEvent.key + " : New : " + e.originalEvent.newValue);
if(e.originalEvent.key != e.originalEvent.newValue)
{
	window.location.href = "initialize.php?imp";
}
    //console.log(e.originalEvent.key, e.originalEvent.newValue);
});*/

window.onfocus = function() {
    //focused = true; 
	//alert(localStorage.getItem('login'));
	if(localStorage.getItem('login') == null || localStorage.getItem('login') <= 0)
	{
		window.location.href = 'logout.php';
	}
	else
	{
		var dbName = localStorage.getItem('dbname');
		var dbNameSession = "<?php echo $_SESSION['dbname']; ?>";
		if(dbName != null && dbName.length > 0 && dbName != dbNameSession)
		{
			//alert(dbName + ":" + dbNameSession);
			window.location.href = 'initialize.php';
		}
	}
};
function Next()
	{
		//alert(currentTip);
		//alert("Next");
		currentTip++;
		//aryTips[aryTips.length] = tipsLenth;
		if( currentTip > aryTips.length -1 )
		{
			currentTip = 0;
		}
		//alert(currentTip);
		var obj = aryTips[currentTip];
		document.getElementById('view_more').innerHTML= "<a href='#' onClick='window.open(\"tips_detail.php?id="+obj['id'] + "\")'>View More >></a>";
		document.getElementById('show_tips').innerHTML="<b>"+obj['title']+" &nbsp; :</b><br>"+obj['desc'];
		
	}
	function Preview()
	{
		//alert(currentTip);
		//alert("Next");
		currentTip--;
		//aryTips[aryTips.length] = tipsLenth;
		if( currentTip < 0 )
		{
			currentTip = aryTips.length -1;
		}
		//alert(currentTip);
		var obj = aryTips[currentTip];
		document.getElementById('show_tips').innerHTML="<b>"+obj['title']+" &nbsp; :</b><br>"+obj['desc'];
		document.getElementById('view_more').innerHTML= "<a href='#' onClick='window.open(\"tips_detail.php?id="+obj['id'] + "\")'>View More >></a>";
	}
</script>
<style>
.content_div
{ 
width:100%;
float:left;
}
.marque_div{
    float: left;
    width: 100%;
    border: solid 1px #eee;
    margin-bottom: 20px;
    border-radius: 35px;
}
.blinking{
	animation:blinkingText 2s infinite;
}

@keyframes blinkingText{
	0%{		color: #ff0000;	}
	49%{	color: transparent;	}
	100%{	color: #ff0000;	}
}
</style>
</head>
<body>
<?php
if(isset($_REQUEST['hm'])){$cls0 = 'first-current';}else{$cls0 = '';}
if(isset($_REQUEST['prm'])){$cls01 = 'current';}else{$cls01 = '';}
if(isset($_REQUEST['mm'])){$cls1 = 'current';}else{$cls1 = '';}
if(isset($_REQUEST['imp'])){$cls11 = 'current';}else{$cls11 = '';}
if(isset($_REQUEST['scm'])){$cls2 = 'current';}else{$cls2 = '';}
if(isset($_REQUEST['grp'])){$cls22 = 'current';}else{$cls22 = '';}
if(isset($_REQUEST['srm'])){$cls3 = 'current';}else{$cls3 = '';}
if(isset($_REQUEST['ev'])){$cls33 = 'current';}else{$cls33 = '';}
 if(isset($_REQUEST['as'])){$cls4 = 'current';}else{$cls4 = '';}

if(!isset($_SESSION['admin'])){$cls5 = 'last-current';}else{$cls5 = '';}

?>


<!-- header -->

	<div id="header">
		<div class="container" >
        
<?php
if($_SESSION['View'] == "ADMIN")
{
?>
			
               
		
			</div>
		</div>
	
<!-- content -->

	<div id="content">
		<div class="container">
			<div class="section">

			<div class="box">
            
					<div class="border-top">
						<div class="border-right">
							<div class="border-bot">
								<div class="border-left">
                                <?php 
								$societylogo = $m_objHead_S->GetSocietyLogo($_SESSION['society_id']);
								?>
                          <div style="width: 15%;height: 75px;float:left"><img src="<?php echo $societylogo ;?> " style="margin-top: 16px;margin-left: 5px;width: 90%; height: auto;" onerror="this.src='images/no-imgae.png';"> </div>
                                 <!--<div style="width: 175px;height: 70px;float:left"><img src="../beta_aws9/images/Paytm_logo.png" style="margin-top: 5px;margin-left: 5px;width: 80%;"> </div>-->
									<div class="left-top-corner">
										<div class="right-top-corner">
											<div class="right-bot-corner">
												<div class="left-bot-corner">
													<div class="inner">
													
                                                     
                                                    


<?php
}
else
{
	?>

	<?php $societylogo = $m_objHead_S->GetSocietyLogo($_SESSION['society_id']);
	?>
    <div style="width: 15%;height: 75px;float:left"><img src="<?php echo $societylogo ;?> " style="margin-top: 16px;margin-left: 5px;width: 90%; height: auto;" onerror="this.src='images/no-imgae.png';"> </div>
               
<?php }
?>




 <div class="panel-body" <?php if($bIsHide == true){ echo 'style="display:none;"';}else{echo 'style="width:50%;height:40px;margin-top:0px;margin-left:40px;;margin-left:4vw;width:50.00vw;height:4.vw" ';} ?>>
                            <!-- Nav tabs -->
                            
                           
                            
                            <ul class="nav nav-pills" style="height:20px;height:2vw">
                                 <?php 
								// echo $_SESSION["View"] ;
								 if($_SESSION["View"] == "MEMBER")
								{
									?>
									<li class="active">
								<?php 
								}
								else if($_SESSION["View"] == "ADMIN")
								{
									?>
									
									<li>
								<?php
								}
								
								if($_SESSION['role'] <> 'Master Admin')
								{
								?> 
                                <a href="#home-pills" data-toggle="tab" onClick="ShowMemberView()" id="0">My Society</a>
                                <?php } ?>
                                </li>
                                <?php
								//print_r($_SESSION); 
                                if($_SESSION["unit_id"] == "0" || $_SESSION['role'] == "Admin Member")
								{
									?>
                                	<?php if($_SESSION["View"] == "ADMIN")
												{
													?>
													<li class="active">
												<?php 
												}
												else if($_SESSION["View"] == "MEMBER")
												{
													?>
                                                    
													<li>
												<?php
                                                }
												
										if($_SESSION['role'] <> 'Master Admin')
										{
												?> 
                                    <a href="#profile-pills" data-toggle="tab" onClick="ShowAdminView()" id="1">Accounting / Admin</a>
                                    <?php } ?> 
                                	</li>
                                    <?php 
								}
								?>
                                <?php if($_SESSION['society_id'] == 288 && $IsthisMemberDashBoard == true && ($_SESSION['role'] == ROLE_MEMBER || $_SESSION['role'] == ROLE_ADMIN_MEMBER)){?>
                               	<span style="float:right;margin-top:15px;"><a style="font-size:15px;cursor:pointer;text-decoration:none" title="Renew Parking Registration" href="view_member_profile.php?scm&id=<?php echo $Member_id;?>&tik_id=<?php echo time();?>&m&view&renew" target="_blank"><!--<i style="padding-right:5px;margin-top:2px;" class="fa fa-undo"></i>--><b>Renew Parking Registration</b><sup class="blinking" style="color:red; padding-left:5px;"><b>News</b></sup></a></span>
                            	<?php }?>
                            </ul>
                            
</div>
<?php 
if(sizeof($TipsDetails) > 0)
{
$script   = $_SERVER['SCRIPT_NAME'];
$pos = strrpos($script, '/');
	$scriptName = substr($script, ($pos + 1));
	if($scriptName=='home_s.php' || $scriptName=='Dashboard.php' )
	{
  if($_SESSION['View']==ADMIN)
  {?>
  <br>
<div style="width:98%; margin-left:1%; float:left">
<?php }
else {?>
<br>
<br><br>
<div style="width:75%; margin-left:1%;">
<?php }
?>

<div class="col-lg-12" >
        <div  style="font-size: small;">
            <div class="panel panel-info" id="panel" style="font-size: small; background-color: #F2FBFC; padding-right:15px">
            <table id="tips">
            <tr align="right"> <td   align="right" colspan="3">
              
               <i class="fa fa-angle-double-left" style="font-size:10px;font-size:1.35vw" onClick="Preview(this.value)"></i>
               &nbsp;&nbsp;&nbsp;
               <i class="fa fa-angle-double-right" style="font-size:10px;font-size:1.35vw" onClick="Next(this.value)"></i>
               </td></tr>
            <tr><td style="width:80px;">
            <!--<i class="fa fa-lightbulb-o" style="font-size:10px;font-size:3.75vw"></i>-->
            <img src="images/bulb.png" style="width:50px; margin-top: -15px;">
            </td>
            
            <td style="margin-bottom:none" colspan="2">
            <?php for($i=0;$i<sizeof($TipsDetails);$i++)
			{
				//$TipsDetails[$i]['desc'] = preg_replace("/<img [^>]+\>/i ", "", $TipsDetails[$i]['desc']);
					$TipsDetails[$i]['desc'] = strip_tags($TipsDetails[$i]['desc']); 
				if(strlen($TipsDetails[$i]['desc']) >220)
				{
				$TipsDetails[$i]['desc']= substr($TipsDetails[$i]['desc'],0,220) . '...'; 
				 }
				else
				{
					$TipsDetails[$i]['desc']= $TipsDetails[$i]['desc'];
				}
				$tipAry = json_encode(array("id" =>  $TipsDetails[$i]['id'], "title" =>  $TipsDetails[$i]['atr_title'], "desc" =>  $TipsDetails[$i]['desc']));
				?>
            <script>
							//var obj=[];
							//obj['id']='<?php// echo $TipsDetails[$i]['id'];?>'; 
							//obj['atr_title']='<?php //echo $TipsDetails[$i]['atr_title'];?>'; 
							//obj['disc']='<?php //echo $TipsDetails[$i]['disc'];?>'; 
						 //aryTips.push(obj);
						 aryTips.push((<?php echo $tipAry;?>));
						 console.log(aryTips[0]);
							//aryTips.push(obj);
			</script>
            <?php }
			?>
              <p style="margin-left:1%;text-align:justify;padding-bottom:0px; margin-top:-15px;" id="show_tips"></p>
              <!--  <p id="view_more"></p>-->
               
              </td></tr>
              <tr><td></td>
               <td align="left"></td>
              <td id="view_more" align="right" style="float: right;margin-top: -30px;margin-right: 1%;">
               <script>Next();</script>
               </td></tr>
                            </table>
            </div>
     </div>
  </div>
  </div>
<?php }
}?>
<br />
<script type="text/javascript">
	cssdropdown.startchrome("chromemenu");
	 
	function ShowMemberView(SelectedTab)
	{
		
		window.location.href = "Dashboard.php?View=MEMBER";
		//location.reload(true);
	}
	function ShowAdminView(SelectedTab)
	{
		
		
		window.location.href = "home_s.php?View=ADMIN";
		
		//location.reload(true);
	}
</script>