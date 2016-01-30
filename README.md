CO2 detector (Masterkit MT8057) telegram bot.
* language is PHP
* framework is reactphp
* measures storage is Redis

# Requirements
* `apt-get install php5-cli redis-server`

# Setup ngrok

Ngrok is https forwarding service. Needed for telegram bot API webhook since telegram webhook requires HTTPS.

* `apt-get install unzip`
* download ngrok from https://ngrok.com/download && unzip
* setup token: `./ngrok authtoken <your_token>`
* run ngrok: `./ngrok http 18062`

# Setup php service

* install composer globally
* `composer install`
* copy config.json to config.local.json and setup telegram bot API key (get from https://core.telegram.org/bots/api)
and ngrok host (from ngrok running console).
* `cd app && nohup php app.php > app.log`
* setup telegram https webhook using ngrok: `curl -v https://<your_subdomain>.ngrok.io/air/setup`
