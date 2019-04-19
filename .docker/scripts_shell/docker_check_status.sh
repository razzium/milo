#!/usr/bin/env bash
docker inspect -f "{{.State.Running}}" $1