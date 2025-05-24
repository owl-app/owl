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
