<?php
	if(isset($_REQUEST['pdffile']) && $_REQUEST['pdffile'] <> '')
	{
		if(file_exists($_REQUEST['pdffile']))
		{
			echo "<a href='" . $_REQUEST['pdffile'] . "' target='_blank'><img src='images/pdficon.png' /></a>";
		}
		else 
		{
			//PDF File does not exist
		}
	}
?>