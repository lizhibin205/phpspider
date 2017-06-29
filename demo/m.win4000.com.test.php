<?php 
ini_set("memory_limit", "1024M");
require dirname(__FILE__).'/../core/init.php';

$html = requests::get("http://m.win4000.com/meinv124947_2.html");

//get title
$result = selector::select($html, "//div[contains(@class,'title')]//h2");
print_r($result);

//get type
$result = selector::select($html, "//div[contains(@class,'title')]//p//span//a");
print_r($result);

//get image
$result = selector::select($html, "//div[contains(@class,'wallpaper-container')]//a//img");
print_r($result);

$url = "http://m.win4000.com/meinv125004.html";
if (preg_match("/(\d+)(_\d+)?\.html$/", $url, $matches) === 1) {
    $child = isset($matches[2]) ? $matches[2] : "_1";
    $data = "{$matches[1]}{$child}";
} else {
    $data = "0_0";
}
print_r($data);