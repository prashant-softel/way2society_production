<?php
/**
 * Transfer Files Server to Server using PHP Copy
 * @link https://shellcreeper.com/?p=1249
 */

$url = "http://www.way2society.com/seperatedb/utility/script/db/";
/*$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 1000);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
$contents = curl_exec($ch);
if (curl_errno($ch)) {
  echo curl_error($ch);
  echo "\n<br />";
  $contents = '';
} else {
  curl_close($ch);
}

if (!is_string($contents) || !strlen($contents)) {
  echo "Failed to get contents.";
  $contents = '';
}
else
{
	
}

echo $contents;
*/

//$dir = "folder/*";
/*$iter = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($url, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST,
    RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
);

$paths = array($url);
foreach ($iter as $path => $dir) {
    if ($dir->isDir()) {
        $paths[] = $path;
    }
}

print_r($paths);*/

//exec('wget  -r -nH -nd -np -R index.html*'.$url.' -P backupdb/'.date("Ymd").'/');

exec('wput /var/www/html/seperatedb/utility/script/db/  http://society.attuit.in/beta/AWS_BACKUP/' , $output, $return);

var_dump($return);
if (!$return) {
    echo "db backup Created Successfully";
} else {
    echo "db backup not created";
}


?>

