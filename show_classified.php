<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Classified Details</title>
</head>
<?php //include_once "ses_set_s.php";
include_once("includes/head_s.php"); 
include_once ("classes/dbconst.class.php");;
include_once("classes/include/dbop.class.php");
include_once("classes/addclassified.class.php");?>
<?php


$obj_classified = new classified($m_dbConnRoot,$m_dbConn);
$details = $obj_classified->member();
$baseDir = dirname( dirname(__FILE__) );
//$fburl=$baseDir.'\beta\uploads\\'.$foldername.'\\'.$url;  
//C:\wamp\www\beta\uploads\\               
$image_show=array(); 
?>
<!doctype html>
<html style="height: 100%; width: 100%;">
<head>
<script type="text/javascript" src="lib/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="lib/source/jquery.fancybox.pack.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="lib/source/jquery.fancybox.css?v=2.1.5" media="screen" />
<link rel="stylesheet" type="text/css" href="lib/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />
<script type="text/javascript" src="lib/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>

    <style type="text/css">
		.fancybox-custom .fancybox-skin {
			box-shadow: 0 0 50px #222;
		}

		
	</style>


<link rel="stylesheet" href="css/classified.css">
<link rel="stylesheet" type="text/css" href="css/pagination.css">
<meta charset="utf-8">
<title>Untitled Document</title>


</head>

<body style="height: 100%; width: 100%;">
 <?php
// "SELECT name FROM classified JOIN login on classified.login_id = login.login_id"
	 $sql="select `ad_title`,`location`,`email`,`phone`,`desp`,`act_date`,`exp_date`,c.`cat_id`,`img`,login.`name`,classified_cate.`name` as category from classified as c JOIN login on c.login_id = login.login_id join classified_cate on  classified_cate.cat_id=c.cat_id where c.status='Y' and c.id='".$_REQUEST['id']."'";
	 
	     //$sql="select cat_id ,name from classified_cate where cat_id='".$_REQUEST['cat_id']."' ";
		$res = $m_dbConnRoot->select($sql);
		//for($i=0;$i<sizeof ($res);$i++)
		//{
		// print_r( $res);
		$name=$res[0]['name'];
	    $cat=$res[0]['category'];
		$loc=$res[0]['location'];
		$email=$res[0]['email'];
		$phone=$res[0]['phone'];
		$image=$res[0]['img'];
		$title=$res[0]['ad_title'];
		$active=$res[0]['act_date'];
		$expiry=$res[0]['exp_date'];
		$discription=$res[0]['desp'];
       
	    $image_collection = explode(',', $image);
		//print_r ($image_collection);
?>
<div id="middle">

<br>

	<div class="panel panel-info" style="margin-top:0%;margin-left:3.5%; border:none;width:70%">
 
		<div class="panel-heading" style="font-size:20px">
        <?php echo $title=$res[0]['ad_title']; ?>
		</div>
	
		<br>
	<br>
<center><button type="button" class="btn btn-primary" onclick="window.location.href='classified.php'">Go Back</button></center>
	<br>

		<div id="body2" style="margin-top: 4.5%;width:100%" class="col-md-12">
   			
            <div style="box-shadow: 0 0 5px #ccc; padding: 5px;margin: 5px; float: left;width: 28vw;height: 19vw;background-color: #f8f8f8;">
			<?php if($image=='')
				{
					$image_collection[0]="nophoto.PNG";
				}?>
			<div style="float:left;"><img id="fancybox-manual-c"style="width: 100%;height: 18vw;" src="ads/<?php echo $image_collection[0]?>"/></div>
			</div>
                
                <div>
					<div style="float:left;border-bottom: solid 3px #c0cde4;font-size: 16px; margin: 10px 0px 2px 0px;    width: 47%;
					margin-left: 6px;">
					<b style="margin-top: 1%;float: left;padding-left: 40%;">Details</b>
					</div>
					<div style="float: left; padding: 2% 0% 0% 0%; width:200px;">
						<div style="width:220px;font-family: Arial, Helvetica, sans-serif; font-size: 13px;color: #525252;margin-left: -60px;
">Located In:&nbsp;&nbsp;</div>
                        <div style="width: 130px;margin-left: 80px; margin-top: -17px;font-size: 13px;max-width: 135px; word-wrap: break-word; text-align: left;text-transform: capitalize;"><b><?php echo $loc?></b></div>
					</div>
                    <div style="float: left;padding: 2% 0% 0% 0%; margin-left: -5px;width: 167px;">
						<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;color: #525252;    margin-left:18px; float:left;">Publish On:&nbsp;&nbsp;<b style="margin-left: -6px;"><?php echo getDisplayFormatDate($active)?></b></span>
					</div>
					<!--<div style="float: left;padding: 2% 0% 0% 0%; margin-left: -28px;width: 160px;">
						<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;color: #525252;    margin-left:2px;">Category:&nbsp;&nbsp;<b style="margin-left:-6px;"><?php //echo $cat?></b></span>
					</div>-->
					<!--<div style="float: left;padding: 3% 0% 0% 0%;width: 200px;">
						<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;color: #525252;margin-left: 12px;float:left;" >Publish Date:&nbsp;&nbsp;<b style="margin-left: -6px;"><?php// echo $active?></b></span>
					</div>-->
                    <div style="float: left;padding: 3% 0% 0% 0%;width: 200px;">
						<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;color: #525252;margin-left: 12px;float:left;text-transform: capitalize;"
>Category:&nbsp;&nbsp;<b style="margin-left:-6px;"><?php echo $cat?></b></span>
					</div>
                    
					<div style="float: left; padding: 3% 0% 0% 0%;margin-left: -5px; width: 167px;">
					<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;color: #525252;margin-left:5px;">Expiry On:&nbsp;&nbsp;<b style="margin-left:-6px;"><?php echo getDisplayFormatDate($expiry)?></b></span>
				</div>
				</div>

				<div>
					<div style="float:left;border-bottom: solid 3px #c0cde4;font-size:16px;margin: 8px 0px 2px 0px; width: 47%;border-top: solid 3px #c0cde4; margin-left: 6px;">
						<b style="margin-top: 2px;float: left;margin-left: 33%;">Contact Details</b>
					</div>
					
					<div style="float: left;width: 170px;margin-left: 1%;margin-top: 1%;background: #E2F8D3;border: solid 1px #d6e0f2;    height: 100px;">
						<i class="fa fa-user fa-43 btn-lg" style="font-size: 3.0em;float: left;margin-left: -10px; margin-top: 5px;" ></i>
						<div style="float: left; margin-top: -35px; margin-left: 37px; font-size: 13px;font-weight: bold;width: 135px; word-wrap: break-word;text-align: left;"><?php echo $name?></div>
					</div>
					<div style="float: left;width: 170px;margin-left: 1%;margin-top: 1%;background: #E2F8D3;border: solid 1px #d6e0f2;height: 100px;">
						<i class="fa fa-mobile fa-44 btn-lg"  style="font-size: 3.0em;float: left;margin-top: -8px; margin-left: -8px;"></i>
						<p style="float: left; margin-top: 11px; margin-left: -8px; font-size: 16px;font-weight: bold;"><?php echo $phone?><?php //echo $details[$i]['mob'];?></p>
					
					<div style="float:left; "><i class="fa fa-envelope fa-45 btn-lg" style="font-size: 2.2em;float: left;margin-left: -12px; margin-top: -25px;"></i>
						<span style="float: left; margin-top: -33px; margin-left: 31px; font-size: 12px;width: 130px; word-wrap: break-word;text-align: left;"><?php echo $email?><?php //echo $details[$i]['email'];?></span>
					</div>
					</div>
				</div>
                
                
              
                
               
			<div style="float:left; width:100%">
				<div style="float:left;border-bottom: solid 3px #c0cde4;font-size: 16px;float:left;margin: 2% 0% 1% 0%; width: 100%;border-top: solid 3px #c0cde4;">
					<b style="margin-top: 1%;float: left;">Description :</b>
				</div>
				<span style="font-size: 12px;text-align: justify;"><?php echo $discription?></span>
			</div>
<?php // } ?><div class="clear"></div>
		</div>
        
		</div>
	</div>
	</div>
</div>
</div>
<script type="text/javascript">
		$(document).ready(function() {
			
			$('.fancybox').fancybox();
			
			$(".fancybox-effects-c").fancybox({
				wrapCSS    : 'fancybox-custom',
				closeClick : true,

				openEffect : 'none',

				helpers : {
					title : {
						type : 'inside'
					},
					overlay : {
						css : {
							'background' : 'rgba(238,238,238,0.85)'
						}
					}
				}
			});
			$(".fancybox-effects-d").fancybox({
				padding: 0,

				openEffect : 'elastic',
				openSpeed  : 150,

				closeEffect : 'elastic',
				closeSpeed  : 150,

				closeClick : true,

				helpers : {
					overlay : null
				}
			});
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


			/*
			 *  Thumbnail helper. Disable animations, hide close button, arrows and slide to next gallery item if clicked
			 */

			$('.fancybox-thumbs').fancybox({
				prevEffect : 'none',
				nextEffect : 'none',

				closeBtn  : false,
				arrows    : false,
				nextClick : true,

				helpers : {
					thumbs : {
						width  : 50,
						height : 50
					}
				}
			});

			/*
			 *  Media helper. Group items, disable animations, hide arrows, enable media and button helpers.
			*/
			$('.fancybox-media')
				.attr('rel', 'media-gallery')
				.fancybox({
					openEffect : 'none',
					closeEffect : 'none',
					prevEffect : 'none',
					nextEffect : 'none',

					arrows : false,
					helpers : {
						media : {},
						buttons : {}
					}
				});
				$("#fancybox-manual-c").click(function() {
				$.fancybox.open([ 
				<?php 
				for($i=0;$i<sizeof($image_collection);$i++)
				{
				?>
					{ 
						href : 'ads/<?php echo $image_collection[$i]?>',
						//title : 'My title'
					},
				<?php }?>/*{
					href : '2_b.orjpg',
						title : '2nd title'
					}, {
						href : '3_b.jpg'
					}*/
				], {
					helpers : {
						thumbs : {
							width: 75,
							height: 50
						}
					}
				});
			});


		});
	</script>
<?php include_once "includes/foot.php"; ?>