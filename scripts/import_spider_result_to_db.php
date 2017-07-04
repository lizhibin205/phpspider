<?php
require dirname(__DIR__) . "/config/inc_config.php";

//init mysql
$db = mysql_connect($GLOBALS['config']['db']['host'], $GLOBALS['config']['db']['user'], $GLOBALS['config']['db']['pass']);
mysql_select_db($GLOBALS['config']['db']['name'], $db);
mysql_set_charset("utf8", $db);

$csvFileName = dirname(__DIR__) . "/data/result.csv";
$fp = fopen($csvFileName, "r");
while (!feof($fp)) {
    $sql = trim(fgets($fp));
    $rs = mysql_query($sql, $db);
    if ($rs === false) {
        file_put_contents(dirname(__DIR__) . "/data/insert_error.log", "{$sql}" . PHP_EOL, FILE_APPEND);
    }
}
