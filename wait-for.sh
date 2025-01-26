#!/bin/bash
set -e

host="$1"
shift

until nc -z -v -w30 "$host" 3306; do
  >&2 echo "MySQL is unavailable - sleeping"
  sleep 1
done

>&2 echo "MySQL is up - executing command"
exec "$@"