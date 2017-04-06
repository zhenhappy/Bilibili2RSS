<?php
header("Content-type:text/json");
require_once 'function.php';
// if(GetVars("check","GET") === GetVars("torrentName","COOKIE"))
  // echo 65537;
if (count($_FILES) == 0)
  die();
$fileTorrent = "";
if ($_FILES["torrent"]["error"] > 0) {
  echo "{error: '" . $_FILES["torrent"]["error"] . "'}";
  die();
} elseif ($_FILES["torrent"]["type"] == "application/x-bittorrent") {
  setcookie("torrentName",$_FILES["torrent"]["name"]);
  // echo "Upload: " . $_FILES["torrent"]["name"];
  // echo "<br>Type: " . $_FILES["torrent"]["type"];
  // echo "<br>Size: " . ($_FILES["torrent"]["size"] / 1024) . " Kb";
  @mkdir("upload",0755);
  //echo "Stored in: " . $_FILES["torrent"]["tmp_name"] . "<br />";
  $fileTorrent = SaveFile($_FILES["torrent"]["tmp_name"], "upload/", $_FILES["torrent"]["name"]);
} else {
  echo '{error:"文件类型错误"}';
  die();
}
set_time_limit(0);
require_once 'Torrent.php';
// get torrent infos
$torrent = new Torrent($fileTorrent);
// echo '<br><br>private: ', $torrent->is_private() ? 'yes' : 'no';
// echo '<br><br>annonce: ', json_encode($torrent->announce());
// echo '<br><br>name: ', $torrent->name();
// if ($torrent->comment() != "")
  // echo '<br><br>comment: ', $torrent->comment();
// echo '<br><br>piece_length: ', $torrent->piece_length();
// echo '<br><br>size: ',$torrent->size(2);
// echo '<br><br>hash info: ', $torrent->hash_info();
// echo '<br><br>magnet link: ', $torrent->magnet();

//false会输出&而不是&amp;
// echo '<br><br>magnet link: ', $torrent->magnet(false);

// scrape()方法会尝试向所有Tracker查询当前种子的信息
// var_dump($torrent->scrape());

// echo '<br><br>content: ';
// var_dump($torrent->content());
// if (count($torrent->ed2k())>0)
  // var_dump($torrent->ed2k());

$objData = (object)[];
$objData->name = $torrent->name();
$objData->comment = $torrent->comment();
$objData->size = $torrent->size();
$objData->magnet = "magnet:?xt=urn:btih:".$torrent->hash_info();
// $objData->magnet = $torrent->magnet();
$objData->content = $torrent->content();
if (count($torrent->ed2k())>0)
  $objData->ed2k = $torrent->ed2k();
echo json_encode($objData);
if (1 == 2) {
  // create torrent
  $torrent = new Torrent( array( 'test.mp3', 'test.jpg' ), 'http://torrent.tracker/annonce' );
  $torrent->announce(false); // reset announce trackers
  // modify torrent
  $torrent->announce('http://alternate-torrent.tracker/annonce'); // add a tracker
  $torrent->announce(array(
    'http://torrent.tracker/annonce',
    'http://alternate-torrent.tracker/annonce'
  )); // set tracker(s)
  $torrent->comment('hello world');
  $torrent->name('test torrent');
  $torrent->is_private(true);
  $torrent->httpseeds('http://file-hosting.domain/path/'); // Bittornado implementation
  $torrent->url_list(array(
    'http://file-hosting.domain/path/',
    'http://another-file-hosting.domain/path/'
  )); // GetRight implementation
  $torrent->save('output.torrent'); // save to disk
  $torrent->send(); // send to user
}
// print errors
if ($errors = $torrent->errors())
  var_dump($torrent->errors());
die();
?>