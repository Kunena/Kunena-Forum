#!/bin/bash
GIT_SOURCE=${0%/*/*}
GIT_TARGET=$PWD

echo
echo "Link Kunena development tree into web site."
echo "GIT repository in ........: $GIT_SOURCE"
echo "Joomla installation in ...: $GIT_TARGET"
echo

[ -f $GIT_TARGET/configuration.php ] || { 
	echo "ERROR: Joomla installation was not found!"
	echo "Please run this command in Joomla root directory!"
	echo
	echo "Example:"
	echo "sudo -u www-data $0"
	echo
	exit 1; 
}

sources=( 
	"components/com_kunena/admin"
	"components/com_kunena/site"
	"libraries/kunena"
	"plugins/plg_quickicon_kunena"
	"plugins/plg_system_kunena"
	"pkg_kunena.xml"
	"libraries/kunena/kunena.xml"
	"media/kunena/kunena_media.xml"
)
targets=(
	"administrator/components/com_kunena"
	"components/com_kunena"
	"libraries/kunena"
	"plugins/quickicon/kunena"
	"plugins/system/kunena"
	"administrator/manifests/packages/pkg_kunena.xml"
	"administrator/manifests/libraries/kunena.xml"
	"administrator/manifests/files/kunena_media.xml"
)

for (( i = 0 ; i < ${#sources[@]} ; i++ ))
do
	source=$GIT_SOURCE/${sources[$i]}
	target=$GIT_TARGET/${targets[$i]}
	if [ ! -L $target ]; then
		rm -rf $target
	else
		unlink $target
	fi
	echo "Linking ${targets[$i]}"
	ln -s $source $target
done;
echo "Copying media/kunena"
cp -r $GIT_SOURCE/media/kunena $GIT_TARGET/media/kunena

echo
echo "Done!"
echo
