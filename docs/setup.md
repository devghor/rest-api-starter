
# Environment Setup

```bash
docker-compose up 

docker-compose exec app bash

composer install

php artisan key:generate

php artisan migrate --seed

php artisan passport:install
```

**Copy & paste the client id and secret to env file** 
```bash
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=
```