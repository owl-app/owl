## Installation

```shell
# Install required dependencies
$ composer install

# Copy .env file and change the database connection settings
$ cp .env .env.local

# Create database
$ php bin/console doctrine:database:create

# Create database schema
$ php bin/console doctrine:schema:create

# Load fixtures
$ php bin/console sylius:fixtures:load

# Install packages
$ yarn install

# Build dev
$ yarn encore dev

# Create theme dir for admin
$ mkdir -p public/_themes/owl/admin

# Install assets
$ php bin/console sylius:theme:assets:install public/_themes/owl/admin

# Start server
$ symfony serve:start

## Tests

google-chrome --disable-gpu --headless --remote-debugging-address=0.0.0.0 --remote-debugging-port=9222
google-chrome --remote-debugging-address=0.0.0.0 --remote-debugging-port=9222

google-chrome-stable --enable-automation --disable-background-networking --no-default-browser-check --no-first-run --disable-popup-blocking --disable-default-apps --allow-insecure-localhost --disable-translate --disable-extensions --no-sandbox --enable-features=Metal --headless --remote-debugging-port=9222 --window-size=2880,1800 --proxy-server='direct://' --proxy-bypass-list='*' http://127.0.0.1

DEV:

php -dxdebug.remote_enable=1 -dxdebug.remote_autostart=On -dxdebug.client_port=9000 bin/console fos:elastica:search query="{'*':'*'}"

php bin/console fos:elastica:populate --pager-persister=async

sudo supervisorctl start messenger-consume:*

## ELASTICSEARCH

curl -X GET -u elastic:0TTkPTNLWookEgtd9=ZC "https://localhost:9200/user/_search?pretty" -H 'Content-Type: application/json' --cacert ./.elasticsearch/http_ca.crt

curl -X PUT -u elastic:0TTkPTNLWookEgtd9=ZC "https://localhost:9200/user/_settings?pretty" -H 'Content-Type: application/json' --cacert ./.elasticsearch/http_ca.crt -d'
{
  "index": {
    "max_result_window": 10000
  }
}
'

## DOCKER

docker compose --env-file ./.docker/elasticsearch/.env.local -f docker-compose-elasticsearch.yml config

docker compose --env-file ./.docker/elasticsearch/.env.local -f docker-compose-elasticsearch.yml up

docker cp owl-base-tmp-es01-1:/usr/share/elasticsearch/config/certs/ca/ca.crt ./.elasticsearch/.

chmod 664 .elasticsearch/ca.crt

## MESSENGER

