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
        'type' => 'csv',
        'file' => PATH_DATA . '/result_v2.csv',
        'table' => 'mv_spider_mv'
    ],
    //核心爬虫配置部分
    'domains' => ['m.win4000.com'],
    'scan_urls' => ['http://m.win4000.com/'],
    //内容页规则
    'content_url_regexes' => [
        "http://m.win4000.com/meinv\d+.html",
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

$spider->on_extract_field = function ($fieldname, $data, $page) {
    switch ($fieldname) {
        case 'mv_id':
            if (preg_match("/(\d+)(?:_\d+)?\.html$/", $page['url'], $matches) === 1) {
                $data = $matches[1];
            } else {
                $data = "0";
            }
            break;
        case 'mv_title':
			preg_match("/^(.*)（\d+\/(\d+)）$/", strip_tags($data), $matches);
			$data = isset($matches[1]) ? $matches[1] : '未知标题';
            break;
		case 'mv_image':
			$imageList = [$data];
			$index = 1;
			if (preg_match("/(\d+)(?:_\d+)?\.html$/", $page['url'], $matches) === 1) {
                $imageId = $matches[1];
				while (true) {
					$index += 1;
					$html = requests::get("http://m.win4000.com/meinv{$imageId}_{$index}.html");
					$result = selector::select($html, "//div[contains(@class,'wallpaper-container')]//a//img");
					if (!empty($result)) {
						$imageList[] = $result;
					} else {
						break;
					}
				}					
            } 
			$data = json_encode($imageList);
			break;
        default:
    }
    return $data;
};
$spider->start();
