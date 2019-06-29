#!/usr/bin/env bash
# Check if docker is installed
if [ ! -x "$(command -v docker)" ]; then
    echo "Docker is needed to run Milo, please install it to continue."
fi

cd .docker;

# RUN docker razzium dind (dood)
docker run -d -ti -v /var/run/docker.sock:/var/run/docker.sock -v ${PWD}:/${PWD} --name docker-dood-milo -w="$PWD" --restart always razzium/dind

# Run docker sftp & mariadb
#docker-compose up -d --build --force-recreate;
docker run -ti --rm  -v /var/run/docker.sock:/var/run/docker.sock -v $PWD:$PWD docker/compose:1.23.2 -f $PWD/docker-compose.yml up -d --build --force-recreate

# Chmod envs folder
chmod -R 777 envs;

# Todo (one day) : custom port
# read -p "On which port do you want to run Milo (default 9800) : "  port

#if [ -z "$port" ]
#then
#  port="9800"
#fi

#echo $port
