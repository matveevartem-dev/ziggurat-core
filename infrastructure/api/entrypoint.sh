#!/bin/sh
set -e

# 1. Подтягиваем зависимости (если их нет или изменился lock-файл)
# --no-interaction чтобы не ждал нажатий кнопок
composer install --no-interaction --optimize-autoloader

# 2. Ждем, пока база (MySQL) прогреется и станет доступна
# (Опционально, но полезно, чтобы миграции не упали на старте)

# 3. Применяем миграции Yii3
# php yii migrate/up --interactive=0

# 4. Передаем управление RoadRunner (запускаем сервер)
exec rr serve -c .rr.yaml
