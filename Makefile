PROJECT_NAME := test_task
TEST_TASK_POSTGRES_PORT := 3002
TEST_TASK_NGINX_PORT := 3001

all: build run install init

clear:
	PROJECT_NAME=${PROJECT_NAME} \
	TEST_TASK_POSTGRES_PORT=${TEST_TASK_POSTGRES_PORT} \
	TEST_TASK_NGINX_PORT=${TEST_TASK_NGINX_PORT} \
	docker-compose rm --force --stop -v

build:
	PROJECT_NAME=${PROJECT_NAME} \
	TEST_TASK_POSTGRES_PORT=${TEST_TASK_POSTGRES_PORT} \
	TEST_TASK_NGINX_PORT=${TEST_TASK_NGINX_PORT} \
	docker-compose build

run:
	PROJECT_NAME=${PROJECT_NAME} \
	TEST_TASK_POSTGRES_PORT=${TEST_TASK_POSTGRES_PORT} \
	TEST_TASK_NGINX_PORT=${TEST_TASK_NGINX_PORT} \
	docker-compose up -d

install:
	./bin/composer install

init:
	./bin/composer install \
      && ./bin/dconsole doctrine:migrations:migrate \
      && ./bin/dconsole getCurrencyCode \
      && ./bin/dconsole getCurrencyDynamic