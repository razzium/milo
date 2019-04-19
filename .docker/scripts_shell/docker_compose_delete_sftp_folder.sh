#!/usr/bin/env bash
/usr/local/bin/docker-compose exec sftp-server sh -c "sudo rm -rf /uploads/$1"