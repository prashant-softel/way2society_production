<?php
	//include_once("includes/head_s.php");
	
include_once("header.php");
?>
<title>Home</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="stylesheet" href="css/reset.css" type="text/css" media="all">
<link rel="stylesheet" href="css/layout.css" type="text/css" media="all">
<link rel="stylesheet" href="css/style.css" type="text/css" media="all">
<link rel="stylesheet" href="css/zerogrid.css">
<link rel="stylesheet" href="css/responsive.css">
<link rel="stylesheet" href="css/responsiveslides.css" />
<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>
<script type="text/javascript" src="js/cufon-yui.js"></script>
<script type="text/javascript" src="js/cufon-replace.js"></script>
<script type="text/javascript" src="js/Swis721_Cn_BT_400.font.js"></script>
<script type="text/javascript" src="js/Swis721_Cn_BT_700.font.js"></script>
<script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="js/tms-0.3.js"></script>
<script type="text/javascript" src="js/tms_presets.js"></script>
<script type="text/javascript" src="js/jcarousellite.js"></script>
<script type="text/javascript" src="js/script.js"></script>
<script src="js/css3-mediaqueries.js"></script>
  <!--[if lt IE 9]>
  	<script type="text/javascript" src="js/html5.js"></script>
	<style type="text/css">
		.bg{ behavior: url(js/PIE.htc); }
	</style>
  <![endif]-->
	<!--[if lt IE 7]>
		<div style=' clear: both; text-align:center; position: relative;'>
			<a href="http://www.microsoft.com/windows/internet-explorer/default.aspx?ocid=ie6_countdown_bannercode"><img src="http://www.theie6countdown.com/images/upgrade.jpg" border="0"  alt="" /></a>
		</div>
	<![endif]-->
	
	<script src="js/responsiveslides.js"></script>
	<script>
		$(function () {
		  $("#slider").responsiveSlides({
			auto: true,
			pager: false,
			nav: true,
			speed: 500,
			maxwidth: 960,
			namespace: "centered-btns"
		  });
		});
	</script>
	
</head>
<body id="page1">
<div class="body1">
	<div class="body2">
		<div class="main zerogrid">
<!-- header -->
			<header>
				<?php include_once("template_headers.php");?>

</header>
</div>
</div>
</div>
	<div class="body3">
				<div class="main zerogrid">
		<!-- content -->
					<article id="content">
					<div class="wrapper row" style="width:100%">
							<section class="col-3-4"  style="width:100%">
								<div class="wrap-col">
									
									<div class="wrapper" style="text-align:justify">

	

<?php 
if($_REQUEST["id"] != "")
{
	if($_REQUEST["id"] == "aboutus")
	{
	 	?>
        		<h2 class="under">About Us</h2>
                <p align="center"><img src="images/under_constructtion.jpg" width="30%" alt="Under Construction"/></p>
                <?php
	}
	else if($_REQUEST["id"] == "servicesforyou")
	{
		?>
						        		<h2 class="under">Services for You</h2>
        								<figure class="left marg_right1"><img src="images/services2.jpg" alt=""></figure>
										<p class="pad_bot1">way2society is an initiative from Pavitra to make the life simple, however effective for the Society committee and the members. Following are the features of the way2society  : -
		</p>
		<p>
		<strong>a)	Administration of Your Society</strong>
		</p>
		<p>
		Day to day queries handling been never easy for the Office Bearers since manual record keeping was required at every step. By registering here, keeps track of all your attended/unattended queries of the members with alerts to office bearers/managers on overdue queries. Virtual notice board system will replace your society notice board with online notice board. Manual accounting administrations is now just few clicks away.
		</p>
		<p>
		<strong>b) Cloud Based Accounting</strong> 
		</p>
		<p>
		Accounting, being integral part of the System, However managing the same and maintaining transparency throughout is task for office bearers. Online billing, sms facilities, reminding members on overdue payments is basic features of this system. Advanced is the online approval system with lots of system generated entries reduces the manual intervention considerably. Different level of user management is also key features on the way2society.com 
		
		</p>
		<p>
		<strong> c) Online Payment Gateways</strong>
		</p>
		<p>
		Coming Soon
		</p>
		<p>
		
		<strong>d) Ask the Legal Experts</strong>
		</p>
		<p>
		Post your queries related to accounting, administration and legal of your society and get experts opinion from the experts of respective field. 
		
												</p>
		
<?php 
	}
	else if($_REQUEST["id"] == "laws")
	{
		?>
        <h2 class="under">Bye-Laws</h2>
        <p><a href="docs/Model-bye-laws.pdf" target="_blank">Click here to Society view Bye-Laws</a>
        <?php
	}
	else if($_REQUEST["id"] == "forms")
	{
		?>
        <h2 class="under">Common Forms</h2>
        <p align="center"><img src="images/under_constructtion.jpg" width="30%" alt="Under Construction"/></p>
        <?php
	}
	else if($_REQUEST["id"] == "blog")
	{
		?>
        <h2 class="under">Blogs</h2>
        						<figure class="left marg_right1"><img src="images/services.jpg" alt=""></figure>
        <p class="pad_bot1">Societies are advised to ensure that  uploading the appointed auditors details and consent on Mahasahakar site by 31st October 2015. If society fails to do so, the penalty is laviable u/s 147 of Rs.5000/-on society and it will be deemed that no auditor is appointed and the right to appoint the auditor for that financial year gets transferred to the Registrar and the appointment of auditor who has been appoined in AGM stands cancelled.</p>
        <?php
	}
	else if($_REQUEST["id"] == "tnc")
	{
		?>
        <h2 class="under">Terms and Conditions</h2>
        <p align="center"><img src="images/under_constructtion.jpg" width="30%" alt="Under Construction"/></p>
        <?php
	}
	else if($_REQUEST["id"] == "privacy")
	{
		?>
        <h2 class="under">Privacy Policy</h2>
        <p align="center"><img src="images/under_constructtion.jpg" width="30%" alt="Under Construction"/></p>
        <?php
	}
	else if($_REQUEST["id"] == "testimonials")
	{
		?>
        <h2 class="under">Testimonials</h2>
        <p align="center"><img src="images/under_constructtion.jpg" width="30%" alt="Under Construction"/></p>
        <?php
	}
	else if($_REQUEST["id"] == "faq")
	{
		?>
        <h2 class="under">FAQ</h2>
        <p align="center"><img src="images/under_constructtion.jpg" width="30%" alt="Under Construction"/></p>
        <?php
	}
	else if($_REQUEST["id"] == "contactus")
	{
		?>
        <h2 class="under">Contact Us</h2>
        <!--<table>
        <tr>
        <td  style="float:left;">-->
        <div style="float:left;">
        <p>
        <h3>Our Address</h3>
        <br/>

      <!--  Pavitra
        <br/>
        G-6, Shagun, Dindoshi, Malad East, Mumbai – 400 097
        <br/>
        Tel : 022 450 44 699 
        <br/>
        Mob : 09833765243
        <br/>
		Email – info@way2society.com
        <br/>-->
		
         Way2Society
        <br/>
        401 Accord Commercial Complex,
        <br/>
        Opp Goregaon Station East,Above Vodafone Gallery 
         <br/>
         Mumbai -400 063.
         <br/>
		Email – info@way2society.com
        <br/>
        
        </p>
        </div>
       <!-- </td>
        <td style="float:right;">-->
       <div style="float:right;">
        <p>
        <h3>Fill this Form and submit. We will contact you shortly.</h3>
        <!-- Do not change the code! -->
            <a id="foxyform_embed_link_696280" href="http://www.foxyform.com/"></a>
            <script type="text/javascript">
            (function(d, t){
               var g = d.createElement(t),
                   s = d.getElementsByTagName(t)[0];
               g.src = "http://www.foxyform.com/js.php?id=696280&sec_hash=684b1007162&width=550px";
               s.parentNode.insertBefore(g, s);
            }(document, "script"));
            </script>
            <!-- Do not change the code! -->
        </p>
        </div>
        <!--</td>
        </tr>
        </table>-->
        <?php
	}
	
else
{
	
}
}
?>
								</div>
								</div>
							</section>
						
		</div>
		</div>
		</article>
		</div>
		</div>
		</div>
	<?php
include_once "includes/foot.php"; ?>