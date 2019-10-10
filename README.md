# Copy required files from dist

```
$ cp .env.dist .env
$ cp traefik.toml.dist traefik.toml
```

### Set vars in env & traefik

## Set required perm to acme for cert generate
```
$ sudo chmod 400 acme.json
```

## Login to registry (hub.t-me.pp.ua)
```
$ docker login REGISTRY
```
##up server 

1) перед поднятием докера php init 
2) заменять переменные в config/params.php для локального использывания скопировать и заменять переменные в config/params-local.php
```
(string){URL_API} - адрес api 

(boolean){DEBBUG_SYNC} - более детальный вывод в телегу : true | false
(int){ERROR_RATE} - расхождения которое больше указаного числа Major , которые меньше Minor
(int){FLOAT} - количество знаков на обрезания 
{TARGET_SYNC} - версия для синхронизаций 

if(TARGET_SYNC == 'release-2.1') 
    do sync from 'release-2.1' => old to 'release-2.2' => new
elseif(TARGET_SYNC == 'release-2.2')
    do sync from 'release-2.2' => new to 'release-2.1' => old
```
3) обезательно подымать сервера в одной подсети с api 

4) проверить что бы пользователь внутри докера мог создавать папку transferLog по пути www/web/uploads/

## Типы ошибок синхронизаций 
```
1) Major - alert (критическая ошибка)
1) Minor - warning (предуприждения)
```
## Pull and run container
```
$ docker-compose up -d
```
## Директория логов 
```
web/uploads/transferLog 
```
## проверка api 
```
$ docker exec -it newbase_php bash -c 'php yii syncronization/ping-api'
```
## Пример cron jobs
```
$ docker exec newbase_php bash -c 'php yii syncronization/check-data'
```
## Проверка блок файла 
```
$ docker exec -it newbase_php bash -c 'php yii syncronization/check-lock'
```
## Снятия блок файла 
```
$ docker exec -it newbase_php bash -c 'php yii syncronization/un-lock'
```
## Очистка данных с аpi
```
$ docker exec -it newbase_php bash -c 'php yii syncronization/clear-data'
```
## Перенос данных 
```
AjaxTransferProdValueDb() - начало переноса выполнять из браузерной консоли !главное что бы копм не ушел в сон!
ClearOperationsAfterTransferData() - начало очистки выполнять из браузерной консоли !главное что бы копм не ушел в сон!
```