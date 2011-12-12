#!/bin/bash
if [[ $# -lt 2 ]]
then
	echo "Usage:"
	echo "$0 <find> <replace>";
	exit 0;
fi

FIND=$1;
REPLACE=$2;

for FILE in $( find  $( ls | grep -v -e js -e lib -e refactor.sh -e engine  -e img -e files) -type f | grep -v .svn  )
do  
	if [[ $( grep $FIND $FILE ) != '' ]] 
	then 
		echo Processing $FILE ...;
		TMP="/tmp/$(basename $FILE)";
		cp $FILE $TMP;
		cat $TMP | sed s/"$FIND"/"$REPLACE"/g > $FILE;
		#cat $TMP | sed 's/[	]/   /g' > $FILE;
		#cat $TMP | sed 's/$xhr_combobox->param_setter();/$xhr_combobox->param_setter();$xhr_combobox->html_requester();/g' > $FILE;
	fi 
done
