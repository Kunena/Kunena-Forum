#!/bin/bash
GIT_SOURCE=${0%/*/*}
GIT_TARGET=$PWD
OPT_DELETE=0
OPT_DELETE_ALL=0

echo
echo "Link Kunena development tree into web site."
echo "GIT repository in ........: $GIT_SOURCE"
echo "Joomla installation in ...: $GIT_TARGET"
echo

while getopts ":d" optname
	do
		case "$optname" in
			"d")
				OPT_DELETE=1
				;;
			"f")
				OPT_DELETE_ALL=1
				;;
			"?")
				echo "Unknown option $OPTARG"
				exit 1
				;;

		esac
	done

[ -f $GIT_TARGET/configuration.php ] || {
	echo "ERROR: Joomla installation was not found!"
	echo "Please run this command in Joomla root directory!"
	echo
	echo "Add symbolic links to repository (as user www-data):"
	echo "sudo -u www-data $0"
	echo "Remove symbolic links (as user www-data):"
	echo "sudo -u www-data $0 -d"
	echo "Remove everything (including media):"
	echo "sudo -u www-data $0 -df"
	echo
	exit 1;
}

sources=(
	"src/administrator/components/com_kunena"
	"src/components/com_kunena"
	"src/libraries/kunena"
	"src/plugins/plg_finder_kunena"
	"src/plugins/plg_kunena_altauserpoints"
	"src/plugins/plg_kunena_community"
	"src/plugins/plg_kunena_comprofiler"
	"src/plugins/plg_kunena_easyblog"
	"src/plugins/plg_kunena_easyprofile"
	"src/plugins/plg_kunena_easysocial"
	"src/plugins/plg_kunena_finder"
	"src/plugins/plg_kunena_gravatar"
	"src/plugins/plg_kunena_joomla"
	"src/plugins/plg_kunena_kunena"
	"src/plugins/plg_quickicon_kunena"
	"src/plugins/plg_system_kunena"
	"src/plugins/plg_sampledata_kunena"
	"pkg_kunena.xml"
	"src/libraries/kunena/kunena.xml"
	"src/media/kunena/kunena_media.xml"
)
targets=(
	"administrator/components/com_kunena"
	"components/com_kunena"
	"libraries/kunena"
	"plugins/finder/kunena"
	"plugins/kunena/altauserpoints"
	"plugins/kunena/community"
	"plugins/kunena/comprofiler"
	"plugins/kunena/easyblog"
	"plugins/kunena/easyprofile"
	"plugins/kunena/easysocial"
	"plugins/kunena/finder"
	"plugins/kunena/gravatar"
	"plugins/kunena/joomla"
	"plugins/kunena/kunena"
	"plugins/quickicon/kunena"
	"plugins/system/kunena"
	"plugins/sampledata/kunena"
	"administrator/manifests/packages/pkg_kunena.xml"
	"administrator/manifests/libraries/kunena.xml"
	"administrator/manifests/files/kunena_media.xml"
)

for (( i = 0 ; i < ${#sources[@]} ; i++ ))
do
	source="$GIT_SOURCE/${sources[$i]}"
	target="$GIT_TARGET/${targets[$i]}"
	if [ ! -L $target ]; then
		rm -rf "$target"
	else
		unlink "$target"
	fi
	if ((!$OPT_DELETE)); then
		echo "Linking ${targets[$i]}"
		ln -s "$source" "$target"
	fi
done;
if ((!$OPT_DELETE)); then
	echo "Copying media/kunena"
	rm -rf "$GIT_TARGET/media/kunena"
	cp -r "$GIT_SOURCE/src/media/kunena" "$GIT_TARGET/media/kunena"
else
	if ((!OPT_DELETE_ALL)); then
		rm -rf "$GIT_TARGET/src/media/kunena"
	fi
	mkdir "$GIT_TARGET/administrator/components/com_kunena"
	cp -r "$GIT_SOURCE/src/administrator/components/com_kunena/kunena.xml" "$GIT_TARGET/administrator/components/com_kunena/kunena.xml"
	echo "Removed development tree from your web site."
	echo "Please install Kunena Package to fix your site!"
fi

echo
echo "Done!"
echo
