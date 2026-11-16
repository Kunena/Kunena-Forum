#!/bin/bash
#
# Link the Kunena development tree into a Joomla site in a given folder (for example the site of a Docker container), without
# going into that folder first. It runs git-link.sh in that folder: the links, the options and the checks are the ones of
# git-link.sh.
#
#   sym-link.sh [-d|-df] [joomla folder]
#
# The Joomla folder is the last argument, else $JOOMLA_DIR, else /dockerfiles/joomlanew/j4.

SCRIPTS="$(cd "$(dirname "$0")" && pwd)"
OPTIONS=()
TARGET="${JOOMLA_DIR:-/dockerfiles/joomlanew/j4}"

for argument in "$@"; do
	case "$argument" in
		-*) OPTIONS+=("$argument") ;;
		*) TARGET="$argument" ;;
	esac
done

if [ ! -d "$TARGET" ]; then
	echo "ERROR: the Joomla folder $TARGET does not exist."
	echo "Usage: $0 [-d|-df] [joomla folder]   (or set JOOMLA_DIR)"
	exit 1
fi

cd "$TARGET" && exec bash "$SCRIPTS/git-link.sh" "${OPTIONS[@]}"
