API для получения и хранения курсов криптовалют. Курсы берутся с Binance API и автоматически обновляются в базе данных.

## Запуск проекта
Клонируйте репозиторий и установите зависимости:
```bash
 composer install
 php bin/console doctrine:migrations:migrate
 symfony server:start
```
## API-эндпоинты
Получение курса в реальном времени (Binance):
GET /api/rates?pair=BTC/USDT
Получение последних 10 сохранённых курсов:
GET /api/rates/history

## Настройка CRON
Добавьте в crontab -e
Также необходимо запустить Messenger Worker
```bash
 * * * * * /usr/bin/php /path/to/project/bin/console schedule:run >> /path/to/project/var/log/scheduler.log 2>&1
 php bin/console messenger:consume async >> /path/to/project/var/log/messenger.log 2>&1 &
```

## Запуск вручную
```bash
 php bin/console app:dispatch-update-rates
 php bin/console messenger:consume async
```