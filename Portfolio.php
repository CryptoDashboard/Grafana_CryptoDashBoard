<?php
///////////////////////////////////////
// CryptoDashBoard for Grafana 1.0 	///
//		12 Oct. 2017			   	///
///////////////////////////////////////

//////////////////////////////////////////////////////////////
//////////			CONFIG			//////
//////////////////////////////////////////////////////////////
$config = include('config.php');
define('INFLUX_IP','127.0.0.1'); // The IP address of your InfluxDB instance
define('INFLUX_PORT',8089); // The UDP (!) port of your InfluxDB instance; UDP is not enabled by default, you need to enable it in influxdb.conf 

//Required for Hitbtc only, comment if you don't use Hitbtc
require 'vendor/autoload.php';
use Hitbtc\ProtectedClient;
/////////////////////////////////////
//////// Kraken Import     //////////
/////////////////////////////////////

if($config['exchange_Kraken'] == 'Enabled')
{
	require_once 'KrakenAPIClient.php';
	$kraken = new KrakenAPI($config['exchange_kraken_apikey'], $config['exchange_kraken_api_secret']);
	$res = $kraken->QueryPrivate('Balance');

	// Debug
	//print_r($res);
	echo '<pre>'; print_r($res); echo '</pre>';
	// 
	
	foreach($res as $a=>$b){
		foreach($b as $currency=>$currency_value)
		{
			if (strlen($currency)>3) {
				$currency = substr($currency, 1);
			}	
			if($currency == 'XBT') $currency='BTC';
			if($currency <> $config['fiat_currency'])
			{
				$json = file_get_contents('https://min-api.cryptocompare.com/data/price?fsym='.$currency.'&tsyms='.$config['fiat_currency']); 
				$data = json_decode($json);
				$json = file_get_contents('https://min-api.cryptocompare.com/data/price?fsym='.$currency.'&tsyms='.$config['crypto_currency']); 
				$data2 = json_decode($json);
				$price_fiat = $data->$config['fiat_currency'];
				$price_crypto = $data2->$config['crypto_currency'];
				//echo $price_crypto.'<br>';
				//print_r($data2->$config['crypto_currency']);
			}
			else
			{
				$json = file_get_contents('https://min-api.cryptocompare.com/data/price?fsym='.$currency.'&tsyms='.$config['crypto_currency']); 
				$data2 = json_decode($json);
				$price_crypto = $data2->$config['crypto_currency'];
				$price_fiat = $currency_value;
			}

		$currency_total;
		
		if ($currency == $config['fiat_currency'])
		{
			$currency_total = $currency_value;
		}
		else
		{
			$currency_total = $currency_value * $price_fiat;
		}
		
		

/////////////////////////////////////////////
//    END KRAKEN IMPORT //
/////////////////////////////////////////////

		if($config['main_display_in_fiat_or_crypto'] == 'fiat')
		{
		// Coins current market value
		sendToDB('assets5,currency='.$currency.',exchange=kraken,price_fiat='.$price_fiat.',price_crypto='.$price_crypto.',amount='.$currency_value.' value='.$price_fiat);
		}
		else
		{
		// Coins current market value
		sendToDB('assets5,currency='.$currency.',exchange=kraken,price_fiat='.$price_fiat.',price_crypto='.$price_crypto.',amount='.$currency_value.' value='.$price_crypto);
		}


		// DB	
		sendToDB('balances,currency='.$currency.',exchange=kraken,amount='.$currency_value.' value='.$price_crypto);
		// Coins to Fiat Conversion Portfolio value
		sendToDB('portfolio,currency='.$currency.',exchange=kraken value='.$currency_total);

		}
	}

}

/////////////////////////////////////////////
//    ETH WALLET IMPORT //
/////////////////////////////////////////////

if($config['wallet_address_eth'] != "")
{
		$address = $config['wallet_address_eth'];
		$json = file_get_contents('https://api.etherscan.io/api?module=account&action=balance&address='.$address.'&tag=latest');
		$data=json_decode($json);
		$eth_balance = $data->result;
		$eth_balance = $eth_balance / 1000000000000000000;

		$json_string = 'https://api.coinmarketcap.com/v1/ticker/ethereum/?convert='.$config['fiat_currency'];
		$jsondata = file_get_contents($json_string);
		$obj2 = json_decode($jsondata,true);
		foreach($obj2 as $json){
		$currency = $json['symbol'];
		$fiat_currency = strtolower($config['fiat_currency']);
		$price_fiat = $json['price_'.$fiat_currency];	
		}
		$eth_wallet = $eth_balance * $price_fiat;
		
		$eth_wallet = ($eth_balance * $price_fiat);
		sendToDB('portfolio,currency='.$currency.',exchange=wallet value='.$eth_wallet);
		sendToDB('balances,currency='.$currency.',exchange=wallet value='.$eth_balance);
	
}
/////////////////////////////////////////////
//    END ETH WALLET IMPORT //
/////////////////////////////////////////////


////////////////////////////////////////////
//			BITTREX START IMPORT /////
////////////////////////////////////////////

if($config['exchange_Bittrex'] == 'Enabled')
{
	$apikey=$config['exchange_bittrex_apikey'];
	$apisecret=$config['exchange_bittrex_apisecret'];
	$nonce=time();
	$uri='https://bittrex.com/api/v1.1/account/getbalances?apikey='.$apikey.'&nonce='.$nonce;
	$sign=hash_hmac('sha512',$uri,$apisecret);
	$ch = curl_init($uri);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign));
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$execResult = curl_exec($ch);
	$data = json_decode($execResult, true);

	//print_r($data);

	echo "<br>";
			for ($i = 0; $i<sizeof($data) +1; $i++)
			{
		$bittrex_balance = $data["result"][$i]["Balance"];
		$bittrex_currency = $data["result"][$i]["Currency"];
		$json = file_get_contents('https://min-api.cryptocompare.com/data/price?fsym='.$bittrex_currency.'&tsyms='.$config['fiat_currency']); 
		$data2 = json_decode($json);
		$bittrex_price_fiat = $data2->EUR;
		$bittrex_fiat_balance = $bittrex_balance * $bittrex_price_fiat;
		if($bittrex_currency == 'BCC') 
		{
			$bittrex_currency = 'BCH';
		}
		// Coins current market value
		sendToDB('assets,currency='.$bittrex_currency.',exchange=bittrex value='.$bittrex_price_fiat);
		sendToDB('balances,currency='.$bittrex_currency.',exchange=bittrex value='.$bittrex_balance);
		// Coins to Fiat Conversion Portfolio value
		sendToDB('portfolio,currency='.$bittrex_currency.',exchange=bittrex value='.$bittrex_fiat_balance);

		
	//	$balances = $bittrex_currency." - " .$balance." / " .$bittrex_price_fiat."€ <br>";
	//  echo $balances;
	}


	if(curl_exec($ch) === false)
	{
		echo 'Erreur Curl : ' . curl_error($ch);
	}

}
/////////////////////////////////////////////
//    END BITTREX IMPORT //
/////////////////////////////////////////////









/////////////////////////////////////////////
//    HITBTC IMPORT 	//
/////////////////////////////////////////////
if($config['exchange_Hitbtc'] == 'Enabled')
{
///////////////////////////////////////////////////////////

	$client = new \Hitbtc\ProtectedClient($config['exchange_hitbtc_apikey'],$config['exchange_hitbtc_apisecret'], $demo = false);
	try {
		foreach ($client->getBalanceTrading() as $balance) {
			if($balance->getAvailable() > 0){
			
				$hitbtc_balance = $balance->getAvailable();
				$hitbtc_currency = $balance->getCurrency();
				$json = file_get_contents('https://min-api.cryptocompare.com/data/price?fsym='.$hitbtc_currency.'&tsyms='.$config['fiat_currency']); 
				$data2 = json_decode($json);
				$hitbtc_price_fiat = $data2->$config['fiat_currency'];
				$hitbtc_fiat_balance = $hitbtc_balance * $hitbtc_price_fiat;
				// Coins current market value
				sendToDB('assets,currency='.$hitbtc_currency.',exchange=hitbtc value='.$hitbtc_price_fiat);
				sendToDB('balances,currency='.$hitbtc_currency.',exchange=hitbtc value='.$hitbtc_balance);
				// Coins to Fiat Conversion Portfolio value
				sendToDB('portfolio,currency='.$hitbtc_currency.',exchange=hitbtc value='.$hitbtc_fiat_balance);   
				 
				 // echo $balance->getCurrency() . ' ' . $balance->getAvailable()."<br>";              
			}
		}
	} catch (\Hitbtc\Exception\InvalidRequestException $e) {
		echo $e;
	} catch (\Exception $e) {
		echo $e;
	}
}
/////////////////////////////////////////////
//    COINMARKETCAP IMPORT //
/////////////////////////////////////////////

$data = json_decode(file_get_contents('./CoinMarketCapWatchList.json', FILE_USE_INCLUDE_PATH));
//print_r($data);
foreach($data as $obj => $currency)
{
	$json_string = 'https://api.coinmarketcap.com/v1/ticker/'.$currency.'/?convert='.$config['fiat_currency'];   //&limit=10';
	$jsondata = file_get_contents($json_string);
	$obj2 = json_decode($jsondata,true);
	foreach($obj2 as $json){
	$currency = $json['symbol'];
	$fiat_currency = strtolower($config['fiat_currency']);
	$price_fiat = $json['price_'.$fiat_currency];	
	}
//	echo $currency." - " .$price_fiat."€<br>";
	sendToDB('assets,currency='.$currency.',exchange=CoinMarketCap value='.$price_fiat);
}

echo "Done !";



function sendToDB($data)
{
	$socket = stream_socket_client("udp://".INFLUX_IP.":".INFLUX_PORT."");
	stream_socket_sendto($socket, $data);
	stream_socket_shutdown($socket, STREAM_SHUT_RDWR);
}
?>
