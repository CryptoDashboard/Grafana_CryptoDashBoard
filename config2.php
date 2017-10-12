<?php
return [
// fiat_currency : EUR, JPY, USD, GBP, or even any crypto-currency ...  Whatever CoinmarketCap/Cryptocompare takes. You can try here to see if it works (Replace YOU_CURRENCY_IN_SYMBOL) :
// https://min-api.cryptocompare.com/data/price?fsym=BTC&tsyms=YOUR_CURRENCY_IN_SYMBOL   
// crypto_currency : The crypto-currency value you want to be saved to. BTC recommanded as most pairs are traded with this one. 
// Exchanges : Enabled / Disabled 
	'fiat_currency' => 'EUR',   // !! Must be in CAPS
	'crypto_currency' => 'BTC',  // !! Must be in CAPS
	'main_display_in_fiat_or_crypto' => 'fiat', // !! Must be in CAPS / Can be either fiat or crypto. This is what the graph will mainly use. 
	'exchange_Kraken' => 'Enabled',  // !! Respect Case !
	'exchange_Bittrex' => 'Enabled',
	'exchange_Bitfinex' => 'Disabled',
	'exchange_Hitbtc' => 'Disabled',
	'exchange_bitfinex_apikey' => '',
	'exchange_bitfinex_apipass' => '',
	'exchange_bitfinex_api_url' => '',
	'exchange_bittrex_apikey' => '',
	'exchange_bittrex_apisecret' => '',
	'exchange_hitbtc_apikey' => '',
	'exchange_hitbtc_apisecret' => '',
	'exchange_kraken_apikey' => '',
	'exchange_kraken_api_secret' => '',
	'wallet_address_eth' => '',
	'wallet_address_btc' => '',
	'wallet_address_bch' => ''
];
?> 