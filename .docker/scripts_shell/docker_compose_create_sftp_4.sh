#!/usr/bin/env bash
/usr/local/bin/docker-compose exec sftp-server sh -c "echo \"$1:$2\" | sudo chpasswd"