#!/usr/bin/env bash
/usr/bin/docker inspect -f "{{.State.Running}}" $1