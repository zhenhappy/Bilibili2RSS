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
function SaveFile($tmp,$path,$fn) {
    if (in_array(strtoupper(PHP_OS), array('WINNT', 'WIN32', 'WINDOWS'))) {
        $fn = iconv("UTF-8", "GBK//IGNORE", $fn);
    }
    @move_uploaded_file($tmp, $path . $fn);

    return $path . $fn;
}

?>