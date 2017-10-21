#!/bin/bash

owner="$1"
phpversionname="$2"

file="/home/$owner/.phpenv/versions/$phpversionname/etc/php-fpm.conf"

cp /home/$owner/.phpenv/versions/$phpversionname/etc/php-fpm.conf.default /home/$owner/.phpenv/versions/$phpversionname/etc/php-fpm.conf
if [ -f /home/$owner/.phpenv/versions/$phpversionname/etc/php-fpm.d/www.conf.default ]; then
	cp /home/$owner/.phpenv/versions/$phpversionname/etc/php-fpm.d/www.conf.default /home/$owner/.phpenv/versions/$phpversionname/etc/php-fpm.d/www.conf
	file=/home/$owner/.phpenv/versions/$phpversionname/etc/php-fpm.d/www.conf
fi;

sed -e "s,listen = 127.0.0.1:9000,listen = /tmp/php${phpversionname:0:1}-fpm.sock,g" --in-place $file
sed -e "s,;listen.owner = nobody,listen.owner = $owner,g" --in-place $file
sed -e "s,;listen.group = nobody,listen.group = $owner,g" --in-place $file
sed -e "s,;listen.mode = 0660,listen.mode = 0666,g" --in-place $file
sed -e "s,user = nobody,;user = $owner,g" --in-place $file
sed -e "s,group = nobody,;group = $owner,g" --in-place $file
