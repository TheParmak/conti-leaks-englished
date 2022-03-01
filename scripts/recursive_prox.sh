#!/bin/bash
pwd=$(pwd)
cd $(echo $1 | tr -d '\r')
for f in *; do
  echo "Processing $f file..."
  bash $pwd/prox.sh $f
done
cd $(echo $pwd | tr -d '\r')