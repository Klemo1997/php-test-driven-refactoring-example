#!/usr/bin/sh

if ! docker info &>/dev/null; then
    echo "Docker daemon is not running." >&2
    exit 1
fi

lines=$(docker compose ps | wc -l)

if [ "$lines" -eq 1 ]; then
  echo "Containers are not running, run \"docker compose up [-d]\" first." >&2
  exit 1
fi

echo "Running migrations...";
docker compose exec php sh ./scripts/migrate.sh database.sqlite;

echo "Done!"