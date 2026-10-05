#!/bin/sh
set -eu

php spark migrate --all
exec apache2-foreground
