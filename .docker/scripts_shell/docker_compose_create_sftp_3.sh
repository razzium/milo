#!/usr/bin/env bash
/usr/local/bin/docker-compose exec sftp-server sh -c "sudo useradd -d /uploads/$1 -G sftp $1 -s /usr/sbin/nologin"