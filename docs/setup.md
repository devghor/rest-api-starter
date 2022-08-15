
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
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=1
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=iRHZJTDp41CZ3G4V87b4q4J9npheADSB7ZRNTxyd
```