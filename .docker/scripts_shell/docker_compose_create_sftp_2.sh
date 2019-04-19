#!/usr/bin/env bash
/usr/local/bin/docker-compose exec sftp-server sh -c "sudo mkdir /uploads/$1/src"