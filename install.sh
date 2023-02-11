#!/usr/bin/env bash

printf "Copying .env file \n";

printf "Generating application key \n";
php artisan key:generate

printf "Installing composer packages \n";
composer install

printf "Running database migrations \n";
php artisan migrate

printf "Seeding database \n";
php artisan db:seed

printf "Generating passport keys \n";
php artisan passport:keys
