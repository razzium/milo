#!/usr/bin/env bash
cd .docker;

# Run docker sftp & mariadb
docker-compose up -d --build --force-recreate;

# Chmod envs folder
chmod -R 777 envs;