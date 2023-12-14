<?php
	require_once("dompdf/dompdf_config.inc.php");
	
	$html = $_REQUEST['data'];
	
	$dompdf = new DOMPDF();
	$dompdf->load_html($html);
	$dompdf->render();
	
	/*To show SaveAs Dialog*/
	//$dompdf->stream("sample.pdf");
	
	/*To Save File on Server*/
	$output = $dompdf->output();
	file_put_contents('files/sample.pdf', $output);
	//header("Location: ../files/sample.pdf");
?>