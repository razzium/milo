#!/usr/bin/env bash

# Chmod envs folder
chmod -R 777 envs;

# Run docker sftp & mariadb
cd .docker;
docker-compose up -d --build;
chmod -R 777 scripts_shell;