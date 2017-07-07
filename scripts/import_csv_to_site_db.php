<?php
error_reporting(0);
require dirname(__DIR__) . "/config/inc_config.php";

$db = mysql_connect($GLOBALS['config']['db']['host'], $GLOBALS['config']['db']['user'], $GLOBALS['config']['db']['pass']) or die("Counld't not connect mysql!");
mysql_select_db($GLOBALS['config']['db']['name'], $db);
mysql_set_charset("utf8", $db);

$csvFileName = dirname(__DIR__) . "/data/result_v2.csv";
$fp = fopen($csvFileName, "r");
while (!feof($fp)) {
    $data = trim(fgets($fp));
	list($imageId, $imageTitle, $imageType, $jsonImageList) = fgetcsv($fp);
	$imageTitle = str_replace("美桌网", "", $imageTitle);
	$jsonImageList = str_replace('""', '","', $jsonImageList);
	$imageList = json_decode($jsonImageList, true);
	$imageListCount = count($imageList);
	$sql = "INSERT INTO `mv_site_mv`(`image_id`,`image_page_total`,`image_title`,`image_type`,`image_url`,`image_from_url`)
	VALUES({$imageId},{$imageListCount},'".mysql_real_escape_string($imageTitle)."','".mysql_real_escape_string($imageType)."','[]','".mysql_real_escape_string($jsonImageList)."')";
	if (mysql_query($sql) === false) {
		echo $sql, PHP_EOL;
	}
}