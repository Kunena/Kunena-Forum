# Build a Translate test file from a kunena.english.php file
# Noel Hunter, Feb 9, 2009
#
# This shell script parses a kunena.english.php file, and creates
# a file names translation.txt, which contains the defines, and the
# matching English text.  Translators may then import the text file
# into an editor, edit the English column, and export as tab-delimited
# text for processing by translate_from_text.php
#
# kunena.english.php should be copied into the same directory as this script
#
# To run:
# sh build_translate_txt.sh
grep -i define kunena.english.php \
|cut -d\' -f2,4\
|sed -e "s/'/\t/"\
> translation.txt

