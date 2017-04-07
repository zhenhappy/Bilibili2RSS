<?php
date_default_timezone_set("Asia/Shanghai");
require './class/function.php';
UA_JSON::Create();
UA_JSON::Del();
$htmlRead = "";
if (!UA_JSON::Add()) {
  require './HyperDown/Parser.php';
  $parser = new HyperDown\Parser;
  $mdRead   = file_get_contents("./README.md");
  $htmlRead = $parser->makeHtml($mdRead);
  $htmlRead = "<h2><b>---尽量使用固定IP并邀请足够多的人使用本服务即可移除下述内容---</b></h2>$htmlRead";
}
$seasonid  = GetVars("anime", "GET") != null ? GetVars("anime", "GET") : 5800;
$http_post = Network::Create();
$http_post->open('GET', "http://bangumi.bilibili.com/jsonp/seasoninfo/$seasonid.ver");
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
  $title   = "第" . $item->index . "话 - " . $item->index_title;
  $url     = $item->webplay_url;
  $body    = "<p><img src=\"" . $item->cover . "\" alt=\"$title\" /></p><p>$title</p>" . $htmlRead;
  $rss2->addItem($title, $url, $body, $created);
}
header("Content-type:text/xml; Charset=utf-8");
echo $rss2->saveXML();
?>