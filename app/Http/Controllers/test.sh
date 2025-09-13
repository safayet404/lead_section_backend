#!/usr/bin/env sh
while true; do
    echo "[$(date +"%Y-%m-%d %H:%M:%S")] Pinging https://saleor-beta-api.barrzen.com/..."
    if curl -fsS https://saleor-beta-api.barrzen.com/ > /dev/null; then
        echo "[$(date +"%Y-%m-%d %H:%M:%S")] SUCCESS: Ping to https://saleor-beta-api.barrzen.com/ succeeded"
    else
        echo "[$(date +"%Y-%m-%d %H:%M:%S")] ERROR: Ping to https://saleor-beta-api.barrzen.com/ failed (exit code: $?)" >&2
    fi
    sleep 30
done