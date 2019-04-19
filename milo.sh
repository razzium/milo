#!/usr/bin/env bash
# Stop sftp server
# Launch sftp
# Chmod all needed
chmod -R 777 envs;
docker stop docker_sftp-server_1;
cd .docker;
docker-compose up -d --build;
chmod -R 777 scripts_shell;
