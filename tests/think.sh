#!/bin/bash

php ./../../../think auth:test-access
php ./../../../think auth:test-auth
php ./../../../think auth:test-jwt
php ./../../../think auth:test-sanctum
