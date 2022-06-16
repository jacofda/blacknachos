# Setup

```
git clone git@github.com:jacofda/blacknachos.git
cd blacknachos/
composer install
php bin/console doctrine:fixtures:load
```
# API Routes

POST /api/login

| url | method | function | 
| :---: | :---: | :---: |
| api/article | GET | index |
| api/article | POST | new |
| api/article/{id} | POST | update |
| api/article/{id} | GET | show |
| api/article/{id} | DELETE | destroy |

# WEB Routes

to do

