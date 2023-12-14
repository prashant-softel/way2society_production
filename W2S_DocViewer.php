<?php //include_once "ses_set_s.php"; 
?>

<html>
<head>
	<title>
		Way2Society Document Viewer
	</title>
	</head>
	<body>
		<?php
			$sPath = "";
			$sError = "";
			//print_r($_GET);
			if(isset($_GET["url"]) && isset($_GET["doc_version"]))
			{
				
				
				if($_GET["url"] != "")
				{
					$version = $_GET["doc_version"];
					if($version == "1")
					{
						$sPath = $_GET["url"];
					}
					else if($version == "2")
					{
						include_once("classes/include/check_session.php");

						$sURL = $_GET["url"];
						$sPath = "https://docs.google.com/viewer?srcid=" . $sURL . "&amp;pid=explorer&amp;efh=false&amp;a=v&amp;chrome=false&amp;embedded=true";
						//$sDocID = $_GET["doc_id"];
						//$sPath;	
					}
				}
				else
				{	
					$sError = "Error while loading document. URL not provided.";
				}
				
			} 
			else if(!isset($_POST["path"]))
			{

				$sError = "Error while loading document. URL not provided.";
				//echo "<script type='text/javascript'>alert('Invalid source of document');window.location.href = 'dashboard.php?View=MEMBER'</script>";

			}
			else
			{
				$sPath = $_POST["path"];
			}
			if($sError != "")
			{
				echo $sError;
			}
			else
			{
				//echo $sPath;
			}
		?>
		<!-- <div style="width: 100%;height: 100%;position: relative;">
		<iframe src="https://drive.google.com/file/d/19pPgaouHKzIvER7Z5YPA_lYBEOalv_Sk/preview" style="height: 100%;width: 100%;border: 0; top: 0; left: 0; position: absolute;" seamless="" allowfullscreen="allowfullscreen"></iframe>
		<div style="width: 80px;width: 80px;position: absolute;opacity: 0;right: px;top: 0px;"></div> -->
		<div style="height:100%; width:100%; border:0px; padding:0px; margin:0px">
		<?php 
		if($sError != "")
			{
				echo $sError;
			}
			else
			{
				?>

 <iframe 
src="<?php echo $sPath?>" 

 width="100%" height="100%" frameborder="0" scrolling="no" seamless>    </iframe>    <div style="width: 80px; height: 80px; position: absolute; opacity: 0; right: 0px; top: 0px;">&nbsp;</div>    
			<?php
			}
			?>
			</div>
	</div>
	</body>
</html>
