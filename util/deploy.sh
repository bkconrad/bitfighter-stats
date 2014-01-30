#!/bin/bash

DEPLOY_HOST=bitfighter.org
DEPLOY_DIR=/var/www/html/new-stats
DEPLOY_FILES=$(cat <<EOF
app/
lib/
assets/
badges/
lib.php
index.html
db_functions.php.changeme
stats.php
player.php
records.php
players_per_game.php
EOF
)

# No changes needed below this line

SELF=`readlink -f "$0"`
ROOT=$(dirname $(dirname $SELF))
DEPLOY_PATHS=
for FILE in $DEPLOY_FILES
do
	DEPLOY_PATHS="$DEPLOY_PATHS $ROOT/$FILE"
done

scp -r $DEPLOY_PATHS "$DEPLOY_HOST:$DEPLOY_DIR"
