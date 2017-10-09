<?php
class UA_JSON
{

  public static function FilePath()
  {
    return dirname(dirname(__FILE__)) . "/cache/log.json";
  }
  // UA_JSON::GuestIP()
  public static function GuestIP()
  {
    $IP = GetVars('aff-ip', 'COOKIE');
    if ($IP == null)
      $IP = GetGuestIP();
    return $IP;
  }
  //UA_JSON::Read()
  public static function Read()
  {
    $objD = json_decode(file_get_contents(UA_JSON::FilePath()));
    if ($objD == null)
      $objD = (object)array();
    return $objD;
  }
  //UA_JSON::Create()
  public static function Create()
  {
    $file = UA_JSON::FilePath();
    if (!is_file($file)) {
      if (!file_exists(dirname($file))) {
        @mkdir(dirname($file), 0755, true);
      }
      file_put_contents($file, '{"8.8.8.8":{"Time":1487651979,"Agent":"XNXF","Click":-1}}');
    }
  }
  // UA_JSON::Add()
  public static function Add($aff = "")
  {
    global $zbp;
    $result = false;
    $time   = time();
    $Guests = UA_JSON::Read();
    $IP     = UA_JSON::GuestIP();
    $Agent  = GetGuestAgent();
    $Key    = md5($IP);
    if (!isset($Guests->$Key)) {
      $Guests->$Key        = (object)array("Time"=>$time);
      $Guests->$Key->Agent = $Agent;
      $Guests->$Key->Click = -1;
      if ($aff !== "" && isset($Guests->$aff)) {
        $Guests->$Key->Aff = $aff;
        $Guests->$aff->Click++;
        if ($Guests->$aff->Time < $time)
          $Guests->$aff->Time = $time;
        if ($Guests->$aff->Time < strtotime("+7 hours"))
          $Guests->$aff->Time += ($Guests->$aff->Click % aff_CFG("divide") + 1) * aff_CFG("unit") * 60;
      }
    } else {
      if ($Guests->$Key->Agent === $Agent && $Guests->$Key->Time > $time) {
        $result = true;
      }
    }
    $aff_time = date("m-d H:i:s", $Guests->$Key->Time);
    $aff_time = $Guests->$Key->Time > $time ? $aff_time : "<b style=\"color:red;\">{$aff_time}</b>";
    // setcookie("aff-time", $aff_time, time() + 3600);
    // setcookie("aff-click", $Guests->$Key->Click + 1, time() + 3600);
    $file = UA_JSON::FilePath();
    file_put_contents($file, json_encode($Guests));
    return $result;
  }
  // UA_JSON::Del()
  public static function Del()
  {
    $Guests = UA_JSON::Read();
    foreach ($Guests as $k => $v) {
      if ($v->Time < strtotime("-7 day")) {
        unset($Guests->$k);
      }
    }
    $file = UA_JSON::FilePath();
    file_put_contents($file, json_encode($Guests));
  }
}
?>