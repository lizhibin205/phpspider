<?php
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
    while ($row = mysql_fetch_assoc($cursor)) {
        //var_dump($row);
        //break 2;
        list($image_id, $image_page) = explode("_", $row['mv_id']);
        preg_match("/^([^（]*)（/", $row["mv_title"], $matches);
        $image_title = $matches[1];
        var_dump($matches);
        break 2;
        //insert to site
        $sql = "INSERT INtO `mv_site_mv`(`image_id`,`image_page`,`image_title`,`image_type`,`image_url`,`image_from_url`)
        VALUES({$image_id},{$image_page},'{$image_title}','{$image_type}','','{$image_from_url}')";
        mysql_query($sql);
        $lastOffset += 1;
    }
}
