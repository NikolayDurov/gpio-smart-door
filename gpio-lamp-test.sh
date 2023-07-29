#!/bin/bash

gpio mode 7 out;
gpio mode 1 in;

while true; do
  # Do something
  gpio write 7 1;
  gpio read 1 > ./ports-info/1;
  curl --data "param1=value1&param2=value2" http://10.10.10.100;
  sleep 1;
  gpio write 7 0;
  # дописать к существующему >>, перезаписать >
  gpio read 1 > ./ports-info/1;
  curl --data "param1=value1&param2=value2" http://10.10.10.100;
  sleep 1;
done
