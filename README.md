CO2 detector (Masterkit MT8057) telegram bot.
* Language is PHP
* Framework is reactphp
* Measurements storage is InfluxDB

# Requirements
## Hardware

* CO2 detector

## Software

* `apt-get install php5-cli redis-server`
* [Ambient7 agent (java)](https://github.com/maizy/ambient7)

# Setup agent
* Download ambient7 library jar from https://github.com/maizy/ambient7
* Install InfluxDB https://docs.influxdata.com/influxdb/, setup "air" database, "air" user
* Run ```java -jar ambient7-mt8057-agent-x.x.x.jar --writers influxdb
      --influxdb-database air
      --influxdb-baseurl http://localhost:8086/write
      --influxdb-user air --influxdb-password 123qwe```

# Setup ngrok

Ngrok is https forwarding service. Needed for telegram bot API webhook since telegram webhook requires HTTPS.

* `apt-get install unzip`
* download ngrok from https://ngrok.com/download && unzip
* setup token: `./ngrok authtoken <your_token>`
* run ngrok: `./ngrok http 18062`

# Setup php service

* install composer globally
* cd air-telegram-bot
* `composer install`
* copy config.json to config.local.json and setup:
  * telegram > apiToken - telegram bot API key (get from https://core.telegram.org/bots/api)
  * telegram > webhookHost - ngrok host (from ngrok running console)
  * influx > password - InfluxDB air user password
* `nohup php app/app.php > app.log`
* setup telegram https webhook using ngrok: `curl https://<your_subdomain>.ngrok.io/air/setup`
