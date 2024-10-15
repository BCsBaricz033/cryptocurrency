# cryptocurrency
Cryptocurrency app

Adatbázisban a coins tábla tartalmazza az apiról lejövő coins listát, amelyet felhasználva lehet új criptót beszúrni.
A wallet tábla tartalmazza a felhasználó saját megvásárolt valutáit.
Körülbelüli lépések amelyel végighaladtam a feladat megoldásán

composer create-project laravel/laravel cryptocurrency-app
create cryptocurrency database
change the database in database file
.env change the database type
php artisan migrate:refresh --seed
composer require laravel/breeze --dev
php artisan breeze:install
npm install
 composer require codenix-sv/coingecko-api
php artisan make:migration create_values_table
php artisan make:controller DashboardController
npm install vue-multiselect@next
Fontosabb adatok a lekérdezésből:
price_change_percentage_24h
price_change_24h
image

Aszinkron 
php artisan make:job RefreshCoinsPrice
php artisan queue:work

Indítás:
a megfelelő müködés érdekében a migration és seeder lefuttatása
php artisan serve
npm run dev
php artisan queue:work





