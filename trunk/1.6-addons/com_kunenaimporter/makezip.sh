rm -f *~
rm -f */*~ 
rm -f */*/*~ 
rm -f */*/*/*~
rm -f */*/*/*/*~
rm -f */*/*/*/*/*~
rm -f */*/*/*/*/*/*~
rm *.zip
rm -rf zip
mkdir zip
cp -r administrator/components/com_kunenaimporter zip/admin
cp -r administrator/language zip/lang
cp administrator/components/com_kunenaimporter/install/* zip/
cp index.html manifest.xml zip/
cd zip
zip -r ../com_kunenaimporter-0.2.9.zip *
cd ..
