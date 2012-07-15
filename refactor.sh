#!/bin/bash
if [[ $# -lt 2 ]]
then
	echo "Usage:"
	echo "$0 <find string> <replace string> <ignore regexp>";
	exit 0;
fi

FIND=$1;
REPLACE=$2;
IGNORE='';

if [[ ! -z $3 ]]
then
   IGNORE=" -e $3";
fi


for FILE in $( grep  $FIND  * -R | grep -v -e *.php -e js -e lib -e refactor.sh -e engine  -e img -e files -e .svn  -e .git $IGNORE | awk -F: '{print $1}' |sort|uniq )
do
	echo Refactoring $FILE ...;
	TMP="/tmp/$(basename $FILE)";
	cp $FILE $TMP;
	#cat $TMP | sed s/"$FIND"/"$REPLACE"/g > $FILE;
   
   cat $TMP | sed  \
      -e "s/\$_SESSION\[PAGE\]\['\([a-z_]*\)']\s*=\s*\(.*\);/set_param\('\1',\2\);/g" \
      -e "s/isset(\$_SESSION\[PAGE\]\['\([a-z_]*\)'])/\!is_null\(get_param\('\1'\)\)/g" \
      -e "s/\$_SESSION\[PAGE\]\['\([a-z_]*\)']/get_param\('\1'\)/g" \
      -e "s/\$GLOBALS\['P_TABLES'\]\['\([a-z_]*\)'\]/p_t\('\1'\)/g" \
      -e "s/\$GLOBALS\['MOD_P_TABLES'\]\['\([a-z_]*\)'\]/m_p_t\('\1'\)/g" \
      -e "s/\$GLOBALS\['S_TABLES'\]\['\([a-z_]*\)'\]/s_t\('\1'\)/g" \
      -e "s/\$GLOBALS\['MOD_S_TABLES'\]\['\([a-z_]*\)'\]/m_s_t\('\1'\)/g" > $FILE
done
