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


for FILE in $( grep  $FIND  * -R | grep -v -e *.php -e js -e lib -e refactor.sh -e engine  -e img -e files -e .git $IGNORE | awk -F: '{print $1}' |sort|uniq )
do
	echo Refactoring $FILE ...;
	TMP="/tmp/$(basename $FILE)";
	cp $FILE $TMP;
#--------------------------------------1-------------------------------------------
#cat $TMP | sed s/"$FIND"/"$REPLACE"/g > $FILE;
   
#--------------------------------------2-------------------------------------------
#cat $TMP | sed  \
#   -e "s/\$_SESSION\[PAGE\]\['\([a-z_]*\)']\s*=\s*\(.*\);/set_param\('\1',\2\);/g" \
#   -e "s/isset(\$_SESSION\[PAGE\]\['\([a-z_]*\)'])/\!is_null\(get_param\('\1'\)\)/g" \
#   -e "s/\$_SESSION\[PAGE\]\['\([a-z_]*\)']/get_param\('\1'\)/g" \
#   -e "s/\$GLOBALS\['P_TABLES'\]\['\([a-z_]*\)'\]/p_t\('\1'\)/g" \
#   -e "s/\$GLOBALS\['MOD_P_TABLES'\]\['\([a-z_]*\)'\]/m_p_t\('\1'\)/g" \
#   -e "s/\$GLOBALS\['S_TABLES'\]\['\([a-z_]*\)'\]/s_t\('\1'\)/g" \
#   -e "s/\$GLOBALS\['MOD_S_TABLES'\]\['\([a-z_]*\)'\]/m_s_t\('\1'\)/g" > $FILE

#--------------------------------------2-------------------------------------------
#cat $TMP | sed s/"this.name"/"this.id"/g > $FILE;
#--------------------------------------3-------------------------------------------
#cat $TMP | sed 's/this.name/this.id/g' > $FILE;
#cat $TMP | sed 's/set_param(this.name,this.value);fill_form(this.value,"main")/s_p_c_add("ok",fill_form,this.value);set_param(this.id,this.value)/g' > $FILE;
#cat $TMP | sed "s/isset(\$_SESSION\[PAGE\]\['FILTER'\])?\$_SESSION\[PAGE\]\['FILTER'\]:null/get_filter()/g" > $FILE;
cat $TMP | sed "s/isset(\$_SESSION\[PAGE\]\['FILTER'\])?\" AND \".\$_SESSION\[PAGE\]\['FILTER'\]:null/get_filter(null,true)/g" > $FILE;
#cat $TMP | sed "s/get_filter(null,true)l/get_filter(null,true)/g" > $FILE;
done

