# Installation

```shell
sudo apt-get update \
  && sudo apt-get install -y docker docker-compose make \
  && make
```

# API

```http request
POST http://localhost:3001/get-currency
Content-Type: application/json
UserEmail: test@user.example
AuthToken: 123

{
    "date": "2023-02-12",
    "code": "R01235",
    "baseCode": "RUR"
}
```

# Принцип работы
- При первой инициализации приложения происходит загрузка актуальных значений курсов валют
- Далее, при каждом запросе происходит проверка на давность предыдущего значения курса 
- Если давность более 1 торгового дня, происходит загрузка актуальных курсов заданной валюты
- Затем пользователю возвращаются данные из локальной БД