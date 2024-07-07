# php8-sf7/docker.sh
#!/bin/sh
set -e

php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:schema:create
symfony serve --port=8000 --no-tls
