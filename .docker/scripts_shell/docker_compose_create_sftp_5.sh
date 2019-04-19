#!/usr/bin/env bash
/usr/local/bin/docker-compose exec sftp-server sh -c "sudo chown $1:sftp -R /uploads/$1/src"