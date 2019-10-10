#!/bin/bash
set | egrep '^(MYSQL_DATABASE|MYSQL_USER|MYSQL_PASSWORD|DOMAIN_API)=' > /etc/environment

# set -e
# 
# . /etc/environment

\cp config/db.php.dist config/db.php
\cp config/params.dist.php config/params.php

sed -i -- "s~{NAME}~$MYSQL_DATABASE~g" config/db.php
sed -i -- "s~{USER}~$MYSQL_USER~g" config/db.php
sed -i -- "s~{PASSWORD}~$MYSQL_PASSWORD~g" config/db.php
sed -i -- "s~localhost~mariadb~g" config/db.php
sed -i -- "s~{DOMAIN_API}~$DOMAIN_API~g" config/params.php

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
php composer.phar install
php composer.phar update
php -r "unlink('composer.phar');"

php init --env=Development --overwrite=No

i=0
while [ $i -lt 1 ]
do
  mysqladmin -u$MYSQL_USER -p$MYSQL_PASSWORD -hmariadb ping >/dev/null 2>&1
  if [ $? -eq 0 ]
  then
    break
  else
    echo "waiting for the finish of dump loading ..."
    sleep 5s
  fi
done

php yii migrate/up --interactive=0

echo 'done'
