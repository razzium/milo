#!/usr/bin/env bash
test=$1
echo "$test"
sudo docker exec docker-dood-milo bash -c 'cd $test; ls';