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