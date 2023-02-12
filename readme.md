# Installation and running test task

```shell
sudo apt-get update \
  && sudo apt-get install -y docker docker-compose make \
  && make build run \
  && ./bin/composer install \
  && ./bin/dconsole doctrine:migrations:migrate \
  && ./bin/dconsole getCurrencyCode \
  && ./bin/dconsole getCurrencyDynamic
```