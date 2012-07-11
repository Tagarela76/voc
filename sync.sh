DIRECTORY="vwm"

if [ ! -d "$DIRECTORY" ]; then
	echo "You should cd to VOCWEBMANAGER root folder before sync"
	exit 0
fi

rsync -rzvp --exclude "*.tpl.php" --exclude ".svn" --exclude ".git" --exclude "vwm/migrations/" --exclude "vwm/tests/" --exclude "tmp/*" --exclude "config" --inplace ./vwm/* root@jon.vocwebmanager.com:/home/jonvo0/public_html/vocwebmanager.com/vwm/;

echo "Sync is done"
exit 0
