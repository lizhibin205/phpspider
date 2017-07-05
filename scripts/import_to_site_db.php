<?php
error_reporting(0);
require dirname(__DIR__) . "/config/inc_config.php";

//init mysql
$db = mysql_connect($GLOBALS['config']['db']['host'], $GLOBALS['config']['db']['user'], $GLOBALS['config']['db']['pass']);
mysql_select_db($GLOBALS['config']['db']['name'], $db);
mysql_set_charset("utf8", $db);

//import
$length = 10;
$lastOffset = 0;
while (true) {
    $cursor = mysql_query("SELECT * FROM `mv_spider_mv` LIMIT {$lastOffset},{$length}");
	$preOffset = $lastOffset;
    while ($row = mysql_fetch_assoc($cursor)) {
        //var_dump($row);
        //break 2;
        list($image_id, $image_page) = explode("_", $row['mv_id']);
        preg_match("/^(.*)（\d+\/(\d+)）$/", $row["mv_title"], $matches);
        $image_title = mysql_escape_string($matches[1]);
		$image_page_total = $matches[2];
        //var_dump($matches);
        //break 2;
		$image_type = mysql_escape_string($row['mv_type']);
		$image_from_url = mysql_escape_string($row['mv_image']);
        //insert to site
        $sql = "INSERT INtO `mv_site_mv`(`image_id`,`image_page`,`image_page_total`,`image_title`,`image_type`,`image_url`,`image_from_url`)
        VALUES({$image_id},{$image_page},{$image_page_total},'{$image_title}','{$image_type}','','{$image_from_url}')";
        if (mysql_query($sql) === false) {
			echo $sql, PHP_EOL;
		}
        $lastOffset += 1;
    }
	if ($lastOffset - $preOffset < $length) {
		break;
	}
}
