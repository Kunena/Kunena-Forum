import os,sys,re

dictionary = {
'\r\n':'\n'
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

    f = open(filename, 'wb')
    f.write(txt)
    f.close()


if len(sys.argv)<2:
	print "Usage: python %s directory" % (sys.argv[0])
	sys.exit()

dir = sys.argv[1]

for root, dirs, files in os.walk(dir):
	for name in files:
		if name[-4:] != '.php' and name[-4:] != '.xml' and name[-4:] != '.ini' and name[-3:] != '.js':
			continue
		file_replace(root+'/'+name, dictionary)
