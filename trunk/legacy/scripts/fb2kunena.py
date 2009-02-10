import os,sys,re

copyright = """\\1*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
\\2""";


# Remember that these are regexps:
dictionary = {	'JB_':'KUNENA_', 
		'FB_':'KUNENA_', 
		'fb_link':'CKunenaLink', 
		'jos_fb_':'#__fb_',
		'\<\?\=':'<?php echo ',
		'(\* \@version \$Id\:)(\w+)':'\\1 \\2',
		'(\* \@package Kunena\W+?)(\* \@Copyright.*?Best Of Joomla)':copyright,
		'FBTools':'CKunenaTools'
}



def string_replace(filename, text, dic):
    for i, j in dic.iteritems():
        (text, count) = re.subn(i, j, text)
	if count:
		print "%s: replaced %dx '%s' with '%s'" % (filename, count, i.replace('\n','\\n').replace('\r','\\r'), j.replace('\n','\\n'.replace('\r','\\r')))
    return text

def file_replace(filename, dic):
    f = open(filename, 'rb')
    file = f.read()
    f.close()
  
    txt = string_replace(filename, file, dic)
#    print (txt[1:500])
    f = open(filename, 'wb')
    f.write(txt)
    f.close()


if len(sys.argv)<2:
	print "Usage: python %s directory" % (sys.argv[0])
	sys.exit()

dir = sys.argv[1]

for root, dirs, files in os.walk(dir):
	for name in files:
		if name[-4:] != '.php':
			continue
		if name == 'CHANGELOG.php':
			continue
		file_replace(root+'/'+name, dictionary)
