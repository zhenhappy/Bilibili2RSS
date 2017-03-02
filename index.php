<?php
date_default_timezone_set("Asia/Shanghai");
require './network.php';
require './networkcurl.php';
require './rss2.php';
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
foreach ($obj_json->result->episodes as $ntem) {
  $created = strtotime($ntem->update_time);
  $title   = $ntem->index_title;
  $url     = $ntem->webplay_url;
  $body    = "<p><img src=\"" . $ntem->cover . "\" alt=\"$title\" /></p><p>$title</p>";
  $rss2->addItem($title, $url, $body, $created);
}
header("Content-type:text/xml; Charset=utf-8");
echo $rss2->saveXML();
function GetVars($name, $type = 'REQUEST')
{
  $array =& $GLOBALS[strtoupper("_$type")];
  if (isset($array[$name])) {
    return $array[$name];
  } else {
    return null;
  }
}
?>