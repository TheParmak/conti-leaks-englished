#!/bin/bash
sed -i '/^$/d' $1
sed -i 's/{ "/"/g' $1
sed -i 's/\\/{backslash}/g' $1
sed -i 's/\"\./"/g' $1
sed -i 's/},//g' $1
sed -i 's/   "/  "/g' $1
sed -i 's/")\n/)"\n/g' $1
sed -i 's/"?\n/?"\n/g' $1
sed -i 's/  ""/  "/g' $1
sed -i -E 's/"body": "(.*)"(.*)"/"body": "\1\\"\2"/g' $1
sed -i -E 's/"body": "(.*)": "/"body": "\1 : /g' $1
sed -i -E 's/"from": "(.*)"\n/"from": "\1",\n/g' $1