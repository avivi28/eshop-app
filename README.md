### To start the app: (with Docker & Docker-Compose)

1.  `docker-compose up -d --build`
2.  `docker-compose exec app php artisan migrate`
3.  `docker-compose exec app php artisan db:seed`
    **running at http://127.0.0.1:9000/**

### To start the app: (run locally)

1.  `composer install`
2.  `php artisan serve`
3.  `php artisan migrate`
4.  `php artisan db:seed`
    **running at http://127.0.0.1:8000/**

### To run the tests:

1.  `php artisan test`

### To call the api:

1. please import `./EShop_App.postman_collection.json` into postman
2. change collection variable {{host}} based on how u run the server
