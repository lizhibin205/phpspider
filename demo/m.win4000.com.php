<?php
ini_set("memory_limit", "1024M");
require dirname(__FILE__).'/../core/init.php';

/* Do NOT delete this comment */
/* 不要删除这段注释 */

$configs = [
    'name' => 'm.win4000.com',
    'log_show' => false,
    'log_file' => PATH_DATA .'/phpspider.log',
    'input_encoding' => 'utf-8',
    'output_encoding' => 'utf-8',
    'tasknum' => 1,
    'multiserver' => false,
    'serverid' => 1,
    'save_running_state' => false,
    'interval' => 5,
    'timeout' => 5,
    'max_try' => 1,
    //深度，每个页面的抓取次数
    'max_depth' => 20,
    'max_fields' => 0,
    'user_agent' => phpspider::AGENT_IOS,
    'export' => [
        'type' => 'sql',
        'file' => PATH_DATA . '/result_v2.csv',
        'table' => 'mv_spider_mv'
    ],
    //核心爬虫配置部分
    'domains' => ['m.win4000.com'],
    'scan_urls' => ['http://m.win4000.com/'],
    //内容页规则
    'content_url_regexes' => [
        "http://m.win4000.com/meinv\d+.html",
        "http://m.win4000.com/meinv\d+_\d.html",
    ],
    //列表页规则
    'list_url_regexes' => [
        "http://m.win4000.com/meinvtag\d+.html"
    ],
    //定义内容页的抽取规则
    'fields' => [
        [
            'name' => 'mv_id',
            'selector' => '//title',
            'selector_type' => 'xpath',
            'required' => true,
            'repeated' => false,
        ],
        [
            'name' => 'mv_title',
            'selector' => "//div[contains(@class,'title')]//h2",
            'selector_type' => 'xpath',
            'required' => true,
            'repeated' => false,
        ],
        [
            'name' => 'mv_type',
            'selector' => "//div[contains(@class,'title')]//p//span//a",
            'selector_type' => 'xpath',
            'required' => true,
            'repeated' => false,
        ],
        [
            'name' => 'mv_image',
            'selector' => "//div[contains(@class,'wallpaper-container')]//a//img",
            'selector_type' => 'xpath',
            'required' => true,
            'repeated' => false,
        ]
    ],
];
$spider = new phpspider($configs);

$spider->on_content_page = function ($page, $content, $phpspider) {
    preg_match("/(\d+)(_\d+)?\.html$/", $page['url'], $matches);
    $imageId = $matches[1];
    $urlPage = isset($matches[2]) ? substr($matches[2], 1) : 1;
    $nowPage = selector::select($content, "//div[contains(@class,'title')]//h2/i");
    if ($urlPage == $nowPage) {
        return true;
    } else {
        $nextPage = $nowPage + 1;
        $phpspider->add_url("http://m.win4000.com/meinv{$imageId}_{$nextPage}.html");
        return false;
    }
};
$spider->on_extract_field = function ($fieldname, $data, $page) {
    switch ($fieldname) {
        case 'mv_id':
            if (preg_match("/(\d+)(_\d+)?\.html$/", $page['url'], $matches) === 1) {
                $child = isset($matches[2]) ? $matches[2] : "_1";
                $data = "{$matches[1]}{$child}";
            } else {
                $data = "0_0";
            }
            break;
        case 'mv_title':
            $data = strip_tags($data);
            break;
        default:
    }
    return $data;
};
$spider->start();
