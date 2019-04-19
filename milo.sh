#!/usr/bin/env bash
# Stop sftp server
# Launch sftp
# Chmod all needed
docker stop docker_sftp-server_1;
cd .docker;
pwd;
docker-compose up -d --build;
chmod -R 777 scripts_shell;
