<?php
//print_r($_POST['coins']);
$inp = file_get_contents('CoinMarketCapWatchList.json');
$tempArray = (array) json_decode($inp);
//$merged_array = array_merge($tempArray, $_POST['coins']);
$merged_array = array_diff(array_merge($tempArray,$_POST['coins']),array_intersect($tempArray,$_POST['coins']));
$jsonData = json_encode($merged_array);
echo $jsonData;
file_put_contents('CoinMarketCapWatchList.json', $jsonData);
//file_put_contents("CoinMarketCapWatchList.json",json_encode($_POST['coins']));
?>