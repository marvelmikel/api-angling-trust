
printf "Updating Composer \n"
composer install

printf "Updating ENV \n"
cp .env.staging .env

printf "Running Migrations \n"
php artisan migrate
