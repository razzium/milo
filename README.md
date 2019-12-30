# Milo (Not for production...for the moment !)
## Prerequisites
- Linux / macOS environment
- Docker (warning : add "sudo usermod -aG docker ${USER}")

## Install
- git clone https://github.com/razzium/milo.git (if Git is not installed -> "docker run  -ti --rm -v ${PWD}:/git alpine/git:latest clone https://github.com/razzium/milo.git")
- cd milo
- sh milo.sh
- Take a coffee and wait ...
- [SERVER_URL] : 9888 (defaut credentials : admin@admin.com/password)

## Features
  - Webserver : Apache || NGINX
  - Php
  - MariaDB / MySQL
  - phpMyAdmin
  - SFTP

## Roadmap very short term
  - Webserver : NGINX
  - xDebug
  - Redis
  - User management
  - Php version management (in thinking)
  - MySQL version management (in thinking)
  - Container Dockerfile OR Libs for Container
  
## Roadmap 
  - Add NodeJS env
  - Backend NodeJS
