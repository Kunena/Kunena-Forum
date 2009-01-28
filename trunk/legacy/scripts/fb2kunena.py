import os,sys,re

dictionary = {# 'JB':'KUNENA', 
              # 'FB':'KUNENA', 
              'fb_link':'CKunenaLink', 
              'jos_fb_':'#__fb_'}

def string_replace(filename, text, dic):
    for i, j in dic.iteritems():
        (text, count) = re.subn(i, j, text)
	if count:
		print "%s: found %d %s" % (filename, count, i)
    return text

def file_replace(filename, dic):
    f = open(filename, 'rb')
    file = f.read()
    f.close()
  
    txt = string_replace(filename, file, dic)

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
