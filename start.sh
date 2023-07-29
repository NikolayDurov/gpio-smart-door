#!/bin/bash

cd /var/gpio-library/gpio-smart-home/;
sh gpio-lamp-test.sh &

docker-compose up --build;