#!/bin/bash
sed -i '/^$/d' $1
sed -i 's/{ "/"/g' $1
sed -i 's/\\/{backslash}/g' $1
sed -i 's/\"\./"/g' $1
sed -i 's/},//g' $1
sed -i -E 's/"body": "(.*)": "/"body": "\1 /g' $1