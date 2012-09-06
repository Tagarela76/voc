DIRECTORY="vwm"
RELEASE_DIRECTORY="../vwmRelease"

if [ ! -d "$DIRECTORY" ]; then
	echo "You should cd to VOCWEBMANAGER root folder before sync"
	exit 0
fi

echo "Copying to the tmp folder vwmRelease/"
rsync -rzvp ./$DIRECTORY/* $RELEASE_DIRECTORY/


echo "Compressing JS..."
JS=$RELEASE_DIRECTORY'/modules/js'
FILELIST='minified.tmp'
ls $JS | grep -v min > $FILELIST
while read LINE
do
	if [ ! -d "$JS/$LINE" ]; then
		FILE="$JS/$LINE"
		java -jar tools/yuicompressor-2.4.7.jar $FILE -o $FILE
	fi
done < $FILELIST
rm -f $FILELIST

echo "Starting sync..."
rsync -rzvp \
--exclude "*.tpl.php" \
--exclude ".svn" \
--exclude ".git" \
--exclude "migrations/" \
--exclude "tests/" \
--exclude "tmp/*" \
--exclude "config" \
--exclude "modules/phpgacl/" \
--inplace $RELEASE_DIRECTORY/* root@jon.vocwebmanager.com:/home/jonvo0/public_html/vocwebmanager.com/vwm/;

echo "Sync is done"
exit 0
