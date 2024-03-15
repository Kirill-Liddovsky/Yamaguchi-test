## Установка

Скопировать .env и подключить бд
```bash
php -r "file_exists('.env') || copy('.env.example', '.env');"
```
Выполнить команды

```bash
composer install;php artisan migrate --seed;php artisan key:generate;php artisan jwt:secret
```

Перейти по url для получения документации API
```
/api/documentation
```

Данные тестового пользователя
```
email: test@test.ru
password: password
```
