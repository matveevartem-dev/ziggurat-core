#!/bin/sh
set -e
# Динамически получаем IP, если нужно
DYNAMIC_IP=$(host tcrm.test | awk '{print $4}')
echo "$DYNAMIC_IP  tcrm.test" >> ./etc/hosts

# Запускаем оригинальный процесс
#exec "$@"
