#!/bin/bash

# cwd
cd /var/www/php-challenge

# composer install if vendors dir doesn't exist
if [[ ! -f vendor/autoload.php ]]; then

  composer install

  # check setup
  php app/check.php

fi

# run server
php app/console server:run --env=dev 0.0.0.0:9080 2>&1
