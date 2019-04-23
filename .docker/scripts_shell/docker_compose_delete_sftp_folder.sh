#!/usr/bin/env bash
/usr/local/bin/docker-compose exec sftp-server-milo sh -c "sudo rm -rf /uploads/$1"