<?php
$config = include('config.php');

$json_string = 'https://api.coinmarketcap.com/v1/ticker/?convert='.$config['fiat_currency'];   //&limit=10';
$jsondata = file_get_contents($json_string);
$obj = json_decode($jsondata,true);

//echo '<pre>';
//print_r($obj);
asort($obj);
echo '<form action="CoinMarketCap2.php" method="POST">';
echo '<select name="coins[]" multiple="multiple" style="height:100%;" >';

foreach($obj as $json){
echo '<option value='.$json['id'].'  style="width:200px;">'.$json['name'].' ('.$json['symbol'].')';
}

echo '</select>';
echo '<input type="submit">';
echo '</form>';
?>