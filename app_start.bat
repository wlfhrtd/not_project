REM execute with .\ ahead
symfony server:stop
symfony console messenger:stop-workers
docker-compose stop

symfony server:start -d
docker-compose up -d

timeout 30

REM symfony run -d --watch=config,src,templates,vendor symfony console messenger:consume TRANSPORT_NAME --time-limit=3600 --memory-limit=128M
symfony run -d --watch=config,src,templates,vendor symfony console messenger:consume amqp_order_bus --time-limit=3600 --memory-limit=128M
symfony run -d --watch=config,src,templates,vendor symfony console messenger:consume amqp_email_notification --time-limit=3600 --memory-limit=128M
symfony run -d --watch=config,src,templates,vendor symfony console messenger:consume amqp_telegram_notification --time-limit=3600 --memory-limit=128M
symfony run -d symfony console messenger:consume failed --time-limit=3600 --memory-limit=128M

symfony open:local:webmail
symfony open:local
