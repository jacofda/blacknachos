# Setup

```
git clone git@github.com:jacofda/blacknachos.git
cd blacknachos/
composer install
php bin/console doctrine:fixtures:load
```
# API Routes

POST /api/login

@Route("/api/article", methods={"GET","HEAD"})          -> index
@Route("/api/article", methods={"POST"})                -> new
@Route("/api/article/{id}", methods={"POST"})           -> update
@Route("/api/article/{id}", methods={"GET","HEAD"})     -> show

# WEB Routes

to do

