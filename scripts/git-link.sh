#!/bin/bash
#
# Link the Kunena development tree into a Joomla site: run it in the root directory of the site.
#
#   git-link.sh       make the links (the installed copies of Kunena are replaced by links)
#   git-link.sh -d    remove the links (install the Kunena package again afterwards)
#   git-link.sh -df   remove the links and the whole media/kunena folder, with the uploaded attachments and avatars
#
# The code is linked. media/kunena/core (scripts and styles) is linked too; the other files of media/kunena which are in git
# (emoticons, ranks, topic icons...) are copied, so that the attachments and avatars of the site stay in the site.

# The git clone is the parent of the folder of this script, as an absolute path: a link with a relative target would point
# from the folder of the link.
GIT_SOURCE="$(cd "$(dirname "$0")/.." && pwd)"
GIT_TARGET="$PWD"
OPT_DELETE=0
OPT_DELETE_ALL=0

echo
echo "Link Kunena development tree into web site."
echo "GIT repository in ........: $GIT_SOURCE"
echo "Joomla installation in ...: $GIT_TARGET"
echo

while getopts ":df" o; do
	case "$o" in
		d)
			OPT_DELETE=1
			;;
		f)
			OPT_DELETE_ALL=1
			;;
		*)
			echo "Unknown option -${OPTARG}"
			exit 1
			;;
	esac
done

if ((OPT_DELETE_ALL && !OPT_DELETE)); then
	echo "-f is only used with -d (-df)"
	exit 1
fi

[ -f "$GIT_TARGET/configuration.php" ] || {
	echo "ERROR: Joomla installation was not found!"
	echo "Please run this command in Joomla root directory!"
	echo
	echo "Add symbolic links to repository (as user www-data):"
	echo "sudo -u www-data $0"
	echo "Remove symbolic links (as user www-data):"
	echo "sudo -u www-data $0 -d"
	echo "Remove everything (including media, with the uploaded attachments and avatars):"
	echo "sudo -u www-data $0 -df"
	echo
	exit 1
}

[ -f "$GIT_SOURCE/src/pkg_kunena.xml" ] || {
	echo "ERROR: $GIT_SOURCE is not a git clone of Kunena (no src/pkg_kunena.xml)"
	exit 1
}

sources=(
	"src/admin"
	"src/site"
	"src/api"
	"src/libraries/kunena"
	"src/media/kunena/core"
	"src/pkg_kunena.xml"
	"src/libraries/kunena/kunena.xml"
	"src/media/kunena_media.xml"
)
targets=(
	"administrator/components/com_kunena"
	"components/com_kunena"
	"api/components/com_kunena"
	"libraries/kunena"
	"media/kunena/core"
	"administrator/manifests/packages/pkg_kunena.xml"
	"administrator/manifests/libraries/kunena.xml"
	"administrator/manifests/files/kunena_media.xml"
)

# plg_<group>_<name> is installed in plugins/<group>/<name>
for plugin in "$GIT_SOURCE"/src/plugins/plg_*_*; do
	[ -d "$plugin" ] || continue
	name="${plugin##*/plg_}"
	sources+=("src/plugins/plg_${name}")
	targets+=("plugins/${name%%_*}/${name#*_}")
done

# True when the folder of a path is inside the git clone (through a link of the site, e.g. media/kunena linked whole): the
# script would then delete or overwrite files of the clone itself.
in_git_source() {
	local parent
	parent="$(cd "$(dirname "$1")" 2>/dev/null && pwd -P)" || return 1
	case "$parent/" in
		"$(cd "$GIT_SOURCE" && pwd -P)/"*) return 0 ;;
	esac
	return 1
}

for (( i = 0 ; i < ${#sources[@]} ; i++ ))
do
	source="$GIT_SOURCE/${sources[$i]}"
	target="$GIT_TARGET/${targets[$i]}"

	if in_git_source "$target"; then
		echo "Skipped ${targets[$i]} (its folder is a link to the git clone)"
		continue
	fi

	if ((OPT_DELETE)); then
		# Only the links are removed: a real folder is an installed copy, which is kept.
		if [ -L "$target" ]; then
			unlink "$target"
			echo "Unlinked ${targets[$i]}"
		fi
		continue
	fi

	# Nothing to link to: the installed copy is kept, there is no link which points nowhere.
	if [ ! -e "$source" ]; then
		echo "Skipped ${targets[$i]} (${sources[$i]} does not exist)"
		continue
	fi

	if [ -L "$target" ]; then
		unlink "$target"
	elif [ -e "$target" ]; then
		rm -rf "$target"
	fi

	mkdir -p "$(dirname "$target")"
	echo "Linking ${targets[$i]}"
	ln -s "$source" "$target"
done

if ((!OPT_DELETE)) && in_git_source "$GIT_TARGET/media/kunena/core"; then
	echo "Skipped copying media/kunena (it is a link to the git clone)"
elif ((!OPT_DELETE)); then
	# The other media of Kunena which are in git: copied over the files of the site, nothing else of the site is removed.
	echo "Copying media/kunena (without core)"
	git -C "$GIT_SOURCE" ls-files -z -- src/media/kunena | while IFS= read -r -d '' file; do
		relative="${file#src/media/kunena/}"
		[ "${relative%%/*}" = "core" ] && continue
		mkdir -p "$GIT_TARGET/media/kunena/$(dirname "$relative")"
		cp "$GIT_SOURCE/$file" "$GIT_TARGET/media/kunena/$relative"
	done
else
	if ((OPT_DELETE_ALL)); then
		echo "Removing media/kunena (with the uploaded attachments and avatars)"
		rm -rf "$GIT_TARGET/media/kunena"
	fi

	# Joomla looks for the manifest of the component to install the package again.
	mkdir -p "$GIT_TARGET/administrator/components/com_kunena"
	cp "$GIT_SOURCE/src/admin/kunena.xml" "$GIT_TARGET/administrator/components/com_kunena/kunena.xml"
	echo "Removed development tree from your web site."
	echo "Please install Kunena Package to fix your site!"
fi

echo
echo "Done!"
echo
