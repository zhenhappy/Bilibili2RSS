<?php
class UA_JSON
{
  // UA_JSON::FilePath()
  public static function FilePath()
  {
    return dirname(dirname(__FILE__)) . "/cache/log.json";
  }
  // UA_JSON::Read()
  public static function Read()
  {
    return json_decode(file_get_contents(UA_JSON::FilePath()));
  }
  // UA_JSON::Create()
  public static function Create()
  {
    $file = UA_JSON::FilePath();
    if (!is_file($file)) {
      if (!file_exists(dirname($file))) {
        @mkdir(dirname($file), 0755, true);
      }
      file_put_contents($file, '{"MaxID":0,"Guests":{"8.8.8.8":{"Time":1487651979,"Agent":"RSS","ID":0}}}');
    }
  }
  // UA_JSON::Add()
  public static function Add($rate = 0.3)
  {
    $result = false;
    $data   = UA_JSON::Read();
    $Guests =& $data->Guests;
    $IP      = $_SERVER["REMOTE_ADDR"];
    $Agent   = $_SERVER["HTTP_USER_AGENT"];
    $Referer = $_SERVER["HTTP_REFERER"];
    $Url     = GetRequestUri();
    if (!isset($Guests->$IP)) {
      $data->MaxID++;
      $Guests->$IP          = json_decode('{"Time":' . time() . '}');
      $Guests->$IP->Agent   = $Agent;
      $Guests->$IP->Referer = $Referer;
      $Guests->$IP->Url     = array(
        $Url
      );
      $Guests->$IP->ID      = (int) $data->MaxID;
    } else {
      if ($Guests->$IP->Agent === $Agent && $Guests->$IP->ID <= $data->MaxID - 3) {
        $result = true;
        $data->MaxID -= $rate;
        $Guests->$IP->ID++;
        $Guests->$IP->Time = time();
      }
      if (!in_array($Url, $Guests->$IP->Url))
        $Guests->$IP->Url[] = $Url;
    }
    $file = UA_JSON::FilePath();
    file_put_contents($file, json_encode($data));
    return $result;
  }
  // UA_JSON::Del()
  public static function Del()
  {
    if (floor(time() / 60) % 3 == 0)
      return false;
    $data = UA_JSON::Read();
    $Guests =& $data->Guests;
    $time = time();
    foreach ($Guests as $k => $v) {
      if ($v->Time <= $time) {
        unset($Guests->$k);
        if ($v->Time > strtotime("-7 day"))
          $Guests->$k = $v;
        $time = $v->Time;
      }
    }
    $file = UA_JSON::FilePath();
    file_put_contents($file, json_encode($data));
  }
}
?>