# yiierp
# перед !установкой! композера
composer global require "fxp/composer-asset-plugin:^1.2.0"

# API #
Использование API {scheme}://{base_url}/api
 
# Документация # 
Создание документации 
Переходим в vendor 
Создаем копию папки bower-asset с названием bower
vendor/bin/apidoc api ./ web/docs --page-title "Baza 1.0.0 Documentation"
Использование документации {scheme}://{base_url}/docs

# Вот так тоже ничего #
vendor/bin/apidoc api modules,controllers,models web/docs --page-title "1.0.0 Documentation"

#в доработке# 
    #Unit тесты#
    установить алиас 
    alias codecept="путь к композеру"
    ------------------------------------------------------------------------------------------------------------------------
    построить тесты 
    codecept build
    ------------------------------------------------------------------------------------------------------------------------
    построить запустить
    codecept run
#в доработке# 

//ресайз таблиц 
$(".table-fix").DataTable().draw();

#Xml товаров#
/shop-xml.xml

#Запуск тестов#
php vendor/bin/codecept run unit

#Тестовая бд#
dsn прописываеться в config/test-db.php
миграции - php yii_test migrate

### Перенос Данных

1) испарвления транзакций

проблема в "" 
{"transaction":"318300230","category":"8","product":"p2331","amount":"130","start_price":"2.6596","price1":"110.0000","price2":"110.0000","cost_price":2.6596}{"transaction":"318300230","category":"8","product":"p2332","amount":"260","start_price":"2.4823","price1":"110.0000","price2":"110.0000","cost_price":2.4823}""{"transaction":"318300230","category":"8","product":"p2435","amount":"100","start_price":"0.7092","price1":"30.0000","price2":"30.0000","cost_price":0.7094166666666666}{"transaction":"318300230","category":"8","product":"p2436","amount":"50","start_price":"0.8865","price1":"35.0000","price2":"35.0000","cost_price":0.8867666666666666}

исправленно 
{"transaction":"318300230","category":"8","product":"p2331","amount":"130","start_price":"2.6596","price1":"110.0000","price2":"110.0000","cost_price":2.6596}{"transaction":"318300230","category":"8","product":"p2332","amount":"260","start_price":"2.4823","price1":"110.0000","price2":"110.0000","cost_price":2.4823}{"transaction":"318300230","category":"8","product":"p2435","amount":"100","start_price":"0.7092","price1":"30.0000","price2":"30.0000","cost_price":0.7094166666666666}{"transaction":"318300230","category":"8","product":"p2436","amount":"50","start_price":"0.8865","price1":"35.0000","price2":"35.0000","cost_price":0.8867666666666666}

2) запуск переноса 

AjaxTransferProdValueDb(); - консоль браузера 


3) запус очистки 

ClearOperationsAfterTransferData(); - консоль браузера 
