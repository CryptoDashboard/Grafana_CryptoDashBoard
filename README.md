# Grafana_CryptoDashBoard
PHP Script that retrieve accounts balances from various exchanges and save them to InfluxDB for using with Grafana

![Screenshot](https://user-images.githubusercontent.com/32750570/31518428-81ed26aa-af9f-11e7-8af9-641e67ade1e2.png)

# Requirements

- PHP7
- php-curl
- composer (If you want to use Hitbtc API)
- [Influxdb](https://portal.influxdata.com/downloads)
- [Grafana](https://www.grafana.com)

# Installation

- Install all the requirements
- Edit config file to your needs
- Enable UDP in Grafana config
- Create a DB named Crypto in influxDB
- Setup a crontab / scheduled task to run Portfolio.php every 1min. 
- Import the example Dashboard in Grafana or create your own. 

# Usage

- Create the required API on the exchanges you're using. Only read access to balances/ledgers is needed. 
- Use CoinMarketCap.php or edit the json file to add the additionals currencies you want to graph. 

# Warning 

- There's some bugs. I'm not a developper at all, I just do this as a hobby for my own needs. Feel free to fork it, submit some pull requests, whatever. 



