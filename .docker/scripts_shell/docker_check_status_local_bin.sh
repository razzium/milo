#!/usr/bin/env bash
/usr/local/bin/docker inspect -f "{{.State.Running}}" $1