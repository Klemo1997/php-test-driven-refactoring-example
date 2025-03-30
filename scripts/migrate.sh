#!/usr/bin/sh

DATABASE=$1

for migration in $(ls ./migrations/*sql); do
    echo "Migrating: $migration";
    sqlite3 $DATABASE < "$migration";
done