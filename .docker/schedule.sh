#!/usr/bin/env bash

# enable (-x) debug mode for verbose output
# https://sipb.mit.edu/doc/safe-shell/
#set -eufx -o pipefail

# Run scheduler (poor man crontab ;)
while [[ true ]]
do
  cd /api && php artisan schedule:run --verbose --no-interaction
  sleep 60
done
