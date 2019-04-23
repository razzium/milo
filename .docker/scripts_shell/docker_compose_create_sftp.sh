#!/usr/bin/env bash
/usr/local/bin/docker run -p $1:22 -d atmoz/sftp $2:$3:::upload/$2