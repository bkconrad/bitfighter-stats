#!/bin/bash

DEPLOY_HOST=bitfighter.org
DEPLOY_DIR=/var/www/html/stats
DEPLOY_FILES=$(cat <<EOF
lib.php
index.php
stats.php
player.php
player_stats.css
graphs/graph1.php
graphs/graph2.php
graphs/graph2.php.cumulative
graphs/graphs.php
graphs/index.php
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

scp $DEPLOY_PATHS "$DEPLOY_HOST:$DEPLOY_DIR"
