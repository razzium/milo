#!/usr/bin/env bash
# Stop sftp server
# Launch sftp
# Chmod all needed

# Ask config params
echo -n "What is the database name ? "
read dbNameAnswer

echo -n "Who is the database user ? "
read dbUserAnswer

echo -n "What is the database user password ? "
read dbPasswordAnswer

echo -n "What is the database port ? "
read dbPortAnswer

echo    "<?php \n"   \
    "define('DB_NAME', \"$dbNameAnswer\"); \r\n"   \
    "define('DB_USER', \"$dbUserAnswer\"); \r\n"   \
    "define('DB_PASS', \"$dbPasswordAnswer\"); \r\n"   \
    "define('DB_PORT', \"$dbPortAnswer\"); \r\n"   \
    "define('DB_ENVS_FOLDER', \"envs\"); \r\n"   \
    "define('REFRESH_ENV_STATUS_INTERVAL', 60000); \r\n"  \
    > env.php

# Chmod envs folder
chmod -R 777 envs;

# Ru docker sftp
docker stop docker_sftp-server_1;
cd .docker;
docker-compose up -d --build;
chmod -R 777 scripts_shell;


