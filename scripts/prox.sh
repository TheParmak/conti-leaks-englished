#!/bin/bash
sed -i '/^$/d' $1
sed -i 's/{ "/"/g' $1
sed -i 's/\\/{backslash}/g' $1
sed -i 's/\"\./"/g' $1
sed -i 's/},//g' $1
<<<<<<< HEAD
sed -i -E 's/"body": "(.*)": "/"body": "\1 /g' $1
=======
sed -i 's/   "/  "/g' $1
sed -i 's/")\n/)"\n/g' $1
sed -i -E 's/"body": "(.*)": "/"body": "\1 /g' $1
sed -i -E 's/"from": "(.*)"\n/"from": "\1",\n/g' $1
>>>>>>> 843314e (add more stuff to prox, move prox for fetching upstream)
