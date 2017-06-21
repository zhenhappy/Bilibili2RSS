<?php
date_default_timezone_set("Asia/Shanghai");
header("Content-type:text/xml; Charset=utf-8");
require './class/function.php';
UA_JSON::Create();
UA_JSON::Del();
$htmlRead = "";
$seasonid  = GetVars("anime", "GET") != null ? GetVars("anime", "GET") : 5800;
$fileCache = dirname(__FILE__) . "/cache/" . date("Ymd") ."/$seasonid.xml";
if(!UA_JSON::Add() && is_file($fileCache)){
  require './HyperDown/Parser.php';
  $parser   = new HyperDown\Parser;
  $mdRead   = file_get_contents("./README.md");
  $htmlRead = $parser->makeHtml($mdRead);
  $htmlRead = "<h2><b>---尽量使用固定IP并邀请足够多的人使用本服务即可移除下述内容---</b></h2><p><b>如果你能看到这段文字，说明这是缓存，请降低你的抓取频率！</b></p><p>群号：189574683</p><p>点击加入：<a target=\"_blank\" href=\"//shang.qq.com/wpa/qunwpa?idkey=f2701214fb5c70ce08107e7206a282927e13ab91ec0780af640c2ad6bd9895c8\"><img src=\"//pub.idqqimg.com/wpa/images/group.png\" alt=\"576k5ZCN5LuA5LmI6ay8\" title=\"576k5ZCN5LuA5LmI6ay8\"></a></p>$htmlRead";
  $xmlCache = file_get_contents($fileCache);
  $xmlCache = str_replace("&lt;!--xnxf--&gt;",htmlspecialchars($htmlRead),$xmlCache);
  echo $xmlCache;
  die();
}
$http_post = Network::Create();
$http_post->open('GET', "http://bangumi.bilibili.com/jsonp/seasoninfo/$seasonid.ver?callback=seasonListCallback");
$http_post->send();
$responseText = str_replace("seasonListCallback(", "", $http_post->responseText);
$responseText = str_replace("});", "}", $responseText);

$obj_json     = json_decode($responseText);
if (!isset($obj_json->message) || $obj_json->message !== "success") {
  echo $responseText;
  die();
}

$rss2 = new Rss2($obj_json->result->bangumi_title . "_番剧_bilibili_哔哩哔哩弹幕视频网", "http://bangumi.bilibili.com/anime/" . $seasonid, $obj_json->result->brief);
foreach ($obj_json->result->episodes as $item) {
  $created = strtotime($item->update_time);
  // $title   = "第" . $item->index . "话 - " . $item->index_title;
  $title   = "第" . sprintf("%02d", $item->index) . "话 - " . $item->index_title;
  $url     = $item->webplay_url;
  $img     = str_replace("http://","//",$item->cover);
  $body    = "<p><img src=\"" . $img . "\" alt=\"$title\" /></p><p>$title</p>" . "<!--xnxf-->";
  $rss2->addItem($title, $url, $body, $created);
}
@mkdir(dirname($fileCache));
file_put_contents($fileCache,$rss2->saveXML());
echo $rss2->saveXML();
?>