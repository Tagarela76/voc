DIRECTORY="vwm"
RELEASE_DIRECTORY="../vwmRelease"
TARGET="root@jon.vocwebmanager.com:/home/jonvo0/public_html/vocwebmanager.com"

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

        # if empty line then skip
        [ -z "$LINE" ] && continue
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
--inplace $RELEASE_DIRECTORY/* $TARGET/vwm/;

echo "Syncing vendor separatly"
rsync -rvzp --inplace vendor/* $TARGET/vendor/;

echo "Sync is done"
exit 0
