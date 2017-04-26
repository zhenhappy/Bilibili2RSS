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
    $IP    = $_SERVER["REMOTE_ADDR"];
    $Key   = join(".", array_slice(explode(".", $IP), 0, -1)) . ".*";
    $Agent = $_SERVER["HTTP_USER_AGENT"];
    if ($Agent == "curl")
      $Key = $Agent;
    $Referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER["HTTP_REFERER"] : null;
    $Url     = GetRequestUri();
    $Time    = time();
    if (!isset($Guests->$Key)) {
      $data->MaxID++;
      $Guests->$Key          = json_decode('{"Time":' . $Time . '}');
      $Guests->$Key->ID      = (int) $data->MaxID;
      $Guests->$Key->Agent   = $Agent;
      $Guests->$Key->Referer = $Referer;
      $Guests->$Key->Logs    = array();
    } else {
      if ($Guests->$Key->Agent === $Agent && $Guests->$Key->ID <= $data->MaxID - 3) {
        $result = true;
        $data->MaxID -= $rate;
        $Guests->$Key->ID++;
        $Guests->$Key->Time = $Time;
      }
    }
    if (!isset($Guests->$Key->Logs)) //5月移除
      $Guests->$Key->Logs = array();
    if (count($Guests->$Key->Logs) > 25) {
      $intTray = $Guests->$Key->ID;
      unset($Guests->$Key);
      // UA_JSON::Add();
      // $Guests->$Key->ID = $intTray;
      $data->MaxID += $data->MaxID - $intTray;
    }
    $Guests->$Key->Logs[] = array(
      "Time" => date("Y-m-d H:i:s", $Time),
      "IP" => $IP,
      "Url" => $Url
    );
    if ($Referer != null)
      $Guests->$Key->Logs["Referer"] = $Referer;
    if ($Guests->$Key->Agent !== $Agent)
      $Guests->$Key->Logs["Agent"] = $Agent;
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
  // UA_JSON::EndNothing()
  // public static function EndNothing() {
  // $rss2 = new Rss2("Bilibili2RSS", "http://Bilibili2RSS.bid", "将B站的番剧更新转制为RSS输出");
  // $rss2->addItem($title, $url, $body, $created);
  // header("Content-type:text/xml; Charset=utf-8");
  // echo $rss2->saveXML();
  // }
}
?>