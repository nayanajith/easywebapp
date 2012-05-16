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


for FILE in $( grep  $FIND  * -R | grep -v -e js -e lib -e refactor.sh -e engine  -e img -e files -e .svn  -e .git $IGNORE | awk -F: '{print $1}' |sort|uniq )
do
	echo Refactoring $FILE ...;
	TMP="/tmp/$(basename $FILE)";
	cp $FILE $TMP;
	cat $TMP | sed s/"$FIND"/"$REPLACE"/g > $FILE;
done
