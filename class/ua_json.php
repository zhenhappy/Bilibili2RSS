<?php
class UA_JSON
{
  public static function FilePath()
  {
    return dirname(dirname(__FILE__))."/cache/log.json";
  }
  //UA_JSON::Read()
  public static function Read()
  {
    return json_decode(file_get_contents(UA_JSON::FilePath()));
  }
  //UA_JSON::Create()
  public static function Create()
  {
    $file = UA_JSON::FilePath();
    if (!is_file($file)) {
      if (!file_exists(dirname($file))) {
        @mkdir(dirname($file), 0755, true);
      }
      file_put_contents($file, '{"Guests":{"8.8.8.8":{"Time":1487651979,"Agent":"RSS","ID":0}},"MaxID":0}');
    }
  }
  // UA_JSON::Add()
  public static function Add()
  {
    $result = false;
    $data   = UA_JSON::Read();
    $Guests =& $data->Guests;
    $IP    = $_SERVER["REMOTE_ADDR"];
    $Agent = $_SERVER["HTTP_USER_AGENT"];
    if (!isset($Guests->$IP)) {
      $data->MaxID++;
      $Guests->$IP        = json_decode('{"Time":' . time() . '}');
      $Guests->$IP->Agent = $Agent;
      $Guests->$IP->ID    = $data->MaxID;
    } else {
      if ($Guests->$IP->Agent === $Agent && $Guests->$IP->ID <= $data->MaxID - 3) {
        $result            = true;
        $Guests->$IP->Time = time();
        $Guests->$IP->ID++;
      }
    }
    $file = UA_JSON::FilePath();
    file_put_contents($file, json_encode($data));
    return $result;
  }
  // UA_JSON::Del()
  public static function Del()
  {
    $data = UA_JSON::Read();
    $Guests =& $data->Guests;
    foreach ($Guests as $k => $v) {
      if ($v->Time < strtotime("-7 day")) {
        unset($Guests->$k);
      }
    }
    $file = UA_JSON::FilePath();
    file_put_contents($file, json_encode($data));
  }
}
?>