REM execute with .\ ahead
symfony server:stop
symfony console messenger:stop-workers
docker-compose stop
