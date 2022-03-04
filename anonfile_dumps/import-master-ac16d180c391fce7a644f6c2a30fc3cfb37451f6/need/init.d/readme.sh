#!/bin/bash
update-rc.d gearman-job-server defaults
useradd gearman
mkdir /var/run/gearman/
chown gearman:gearman /var/run/gearman/