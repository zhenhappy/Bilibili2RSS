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
  return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
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

function SetHttpStatusCode($number) {
    static $status = '';
    if ($status != '') {
        return false;
    }

    $codes = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',

        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        451 => 'Unavailable For Legal Reasons',

        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
    );

    if (isset($codes[$number])) {
        header('HTTP/1.1 ' . $number . ' ' . $codes[$number]);
        $status = $number;

        return true;
    }

}

function Redirect($url) {
    SetHttpStatusCode(302);
    header('Location: ' . $url);
    die();
}

function Redirect301($url) {
    SetHttpStatusCode(301);
    header('Location: ' . $url);
    die();
}
?>