#!/bin/bash

if ! which -s php; then
  echo 'PHP CLI executable could not be found.'
  echo 'Please install a PHP CLI.'
  exit 1;
fi

if ! which -s npm; then
  echo 'NPM executable could not be found.'
  echo 'Please install npm.'
  exit 1;
fi

echo "Installing composer locally..."
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php --install-dir=. --filename=composer
php -r "unlink('composer-setup.php');"

echo "Installing composer dependencies..."
./composer install

echo "Installing npm dependencies..."
npm install