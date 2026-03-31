#!/bin/bash

# Starts the PHP built-in server and executes the Codeception acceptance test
# suite. Extra arguments are forwarded to codecept:
# bash Tests/run-acceptance.sh backend --steps

set -eo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
WEB_ROOT="$(realpath -m "$SCRIPT_DIR/../.Build/Web")"
INSTANCE_DIR="$WEB_ROOT/typo3temp/var/tests/acceptance"
PORT=8000

# Kill any existing PHP server on this port.
pkill -f "php -S.*:$PORT" 2>/dev/null || true
sleep 1

# Clean stale test instance and database.
SQLITE_DIR="${INSTANCE_DIR%/*}/acceptance-sqlite-dbs"
rm -rf "$INSTANCE_DIR" "$SQLITE_DIR"
mkdir -p "$INSTANCE_DIR" "$SQLITE_DIR"
# Create empty SQLite file so the Codeception Db module can connect during
# initialization (before the TYPO3 InstallExtension populates it).
touch "$SQLITE_DIR/test_acceptance.sqlite"

# Start PHP server pointing to the test instance dir.
# Enable coverage driver for c3.php collection when COVERAGE=1.
COVERAGE_ARGS=""
if [ "${COVERAGE:-0}" = "1" ]; then
    if php -m 2>/dev/null | grep -qi pcov; then
        COVERAGE_ARGS="-d pcov.enabled=1 -d pcov.directory=$SCRIPT_DIR/../Classes"
    elif php -m 2>/dev/null | grep -qi xdebug; then
        COVERAGE_ARGS="-d xdebug.mode=coverage"
    else
        echo "ERROR: COVERAGE=1 requires PCOV or Xdebug PHP extension." >&2
        exit 1
    fi
fi

TYPO3_PATH_ROOT="$INSTANCE_DIR" TYPO3_PATH_APP="$INSTANCE_DIR" \
php $COVERAGE_ARGS \
    -S 0.0.0.0:$PORT \
    -t "$INSTANCE_DIR" \
    "$SCRIPT_DIR/Acceptance/router.php" \
    >/tmp/php-acceptance-server.log 2>&1 &
    SERVER_PID=$!

# Kill the server when this script exits (normal or error).
# shellcheck disable=SC2064
trap "kill $SERVER_PID 2>/dev/null || true" EXIT

# Wait for the server to accept connections (up to 10 s).
for i in $(seq 1 10); do
    if ! kill -0 "$SERVER_PID" 2>/dev/null; then
        echo "ERROR: PHP server failed to start." >&2
        cat /tmp/php-acceptance-server.log >&2
        exit 1
    fi
    (echo >/dev/tcp/127.0.0.1/$PORT) 2>/dev/null && break
    sleep 1
done
echo "PHP acceptance server started (PID $SERVER_PID) on port $PORT"

# Codecept needs TYPO3_PATH_ROOT = .Build/Web/ for defineOriginalRootPath().
export TYPO3_PATH_ROOT="$WEB_ROOT"
export TYPO3_PATH_APP="$WEB_ROOT"

# Default Selenium/webserver hosts (Docker Compose names); CI overrides these.
export SELENIUM_HOST="${SELENIUM_HOST:-selenium}"

CODECEPT_ARGS=("run" "-c" "$SCRIPT_DIR/codeception.yml")
CODECEPT_PHP_ARGS=""
if [ "${COVERAGE:-0}" = "1" ]; then
    CODECEPT_ARGS+=("--coverage" "--coverage-xml" "--coverage-html" "--disable-coverage-php")
    # The codecept runner also initializes a coverage driver, so pass the same flags.
    CODECEPT_PHP_ARGS="$COVERAGE_ARGS"
fi
CODECEPT_ARGS+=("$@")

php $CODECEPT_PHP_ARGS "$SCRIPT_DIR/../.Build/bin/codecept" "${CODECEPT_ARGS[@]}"
