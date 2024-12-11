#!/bin/bash

./vendor/bin/php-cs-fixer fix

php ./../../../think auth:test-init
php ./../../../think auth:test-access
php ./../../../think auth:test-auth
php ./../../../think auth:test-jwt
php ./../../../think auth:test-sanctum