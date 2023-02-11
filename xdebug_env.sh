#!/usr/bin/bash

# for php 8.1 in WSL 

WIN_MACHINE=$(cat /etc/resolv.conf | grep -oP 'nameserver \K.*')

export XDEBUG_CONFIG="client_host=$WIN_MACHINE mode=debug client_port=9001 connect_timeout_ms=200 force_display_errors=1 force_error_reporting=1 idekey=PHPStorm log=/tmp/xdebug.log log_level=7 output_dir=/tmp start_with_request=yes"
export XDEBUG_SESSION=PHPStorm
export PHP_IDE_CONFIG="serverName=localhost"
