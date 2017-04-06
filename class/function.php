<?php
require './class/network.php';
require './class/networkcurl.php';
require './class/rss2.php';
require './class/ua_json.php';
function GetVars($name, $type = 'REQUEST')
{
  $array =& $GLOBALS[strtoupper("_$type")];
  if (isset($array[$name])) {
    return $array[$name];
  } else {
    return null;
  }
}
function GetGuestIP()
{
  return GetVars("REMOTE_ADDR", "SERVER");
}
function GetGuestAgent()
{
  return GetVars("HTTP_USER_AGENT", "SERVER");
}
?>