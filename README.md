# task-list-backend-laminas

## Step by step to start the application

**Installation using Composer**

docker-compose up -d --build


**Install dependencies**

docker-compose exec api-laminas composer install


**Create database**

docker-compose exec mysql mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS taskdb_leonardo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"


**Run the migrations**

docker-compose exec api-laminas php vendor/bin/phinx migrate


**Run the tests**

docker-compose exec api-laminas ./vendor/bin/phpunit


**Authentication**

The API requires authentication. Send a Basic Auth with **user:1234** in the Authorization header.


**Endpoints**

The API runs on port 8081. To access it, use http://localhost:8081


POST http://localhost:8081/api/task

PUT http://localhost:8081/api/task/:id

PUT http://localhost:8081/api/task/${this.task.id}/status

DELETE http://localhost:8081/api/task/:id

GET http://localhost:8081/api/task?page=1&limit=10&status=pending&title=xxx

POST http://localhost:8081/api/notify


The API contains a simulation of sending an email for pending tasks. The service could be running in a CRON, but for testing purposes, the service can be called through the endpoint: POST http://localhost:8081/api/notify
