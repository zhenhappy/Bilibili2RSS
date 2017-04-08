<?php
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
// function SaveFile($tmp, $path, $fn)
// {
  // if (in_array(strtoupper(PHP_OS), array(
    // 'WINNT',
    // 'WIN32',
    // 'WINDOWS'
  // ))) {
    // $fn = iconv("UTF-8", "GBK//IGNORE", $fn);
  // }
  // @move_uploaded_file($tmp, $path . $fn);
  // return $path . $fn;
// }
function GetValueInArray($array, $name)
{
  if (is_array($array)) {
    if (array_key_exists($name, $array)) {
      return $array[$name];
    }
  }
}
function GetRequestUri()
{
  $url = '';
  if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) {
    $url = $_SERVER['HTTP_X_ORIGINAL_URL'];
  } elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
    $url = $_SERVER['HTTP_X_REWRITE_URL'];
    if (strpos($url, '?') !== false) {
      $querys = GetValueInArray(explode('?', $url), '1');
      foreach (explode('&', $querys) as $query) {
        $name  = GetValueInArray(explode('=', $query), '0');
        $value = GetValueInArray(explode('=', $query), '1');
        $name  = urldecode($name);
        $value = urldecode($value);
        if (!isset($_GET[$name])) {
          $_GET[$name] = $value;
        }
        if (!isset($_GET[$name])) {
          $_REQUEST[$name] = $value;
        }
        $name  = '';
        $value = '';
      }
    }
  } elseif (isset($_SERVER['REQUEST_URI'])) {
    $url = $_SERVER['REQUEST_URI'];
  } elseif (isset($_SERVER['REDIRECT_URL'])) {
    $url = $_SERVER['REDIRECT_URL'];
    if (isset($_SERVER['REDIRECT_QUERY_STRIN'])) {
      $url .= '?' . $_SERVER['REDIRECT_QUERY_STRIN'];
    }
  } else {
    $url = $_SERVER['PHP_SELF'] . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '');
  }
  return $url;
}
?>