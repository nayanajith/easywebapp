#!/bin/bash
VERSION="1.9.1";
DOJO_SRC="http://download.dojotoolkit.org/release-$VERSION/dojo-release-$VERSION-src.tar.gz";
BASE_DIR="dojo-release-$VERSION-src";
SAVE_AS="$BASE_DIR.tar.gz";

function download_files(){
   if [[ -f $( which curl ) ]]
	then
	   echo "Downloading Dojo source...";
	   curl -C -  $DOJO_SRC -o "$SAVE_AS";
	   echo "Downloading Dojo source md5...";
	   curl -C -  "$DOJO_SRC.md5" -o "$SAVE_AS.md5";
	else
	   echo "Downloading Dojo source...";
	   wget -c $DOJO_SRC -O "$SAVE_AS";
	   echo "Downloading Dojo source md5...";
	   wget -c "$DOJO_SRC.md5" -O "$SAVE_AS.md5";
   fi
}

function check_integrity(){
   echo "Checking integrity";
   if [[ -f $( which md5sum ) ]]
   then
      md5sum -c $SAVE_AS.md5;
      if [[ $? != 0 ]] 
      then
         echo "Integrity of $SAVE_AS failed!";
         echo "Delete $SAVE_AS.md5 and run the script again";
    	   exit 0;
      fi
   else
      if [[ -z $( grep $( md5 -q "dojo-release-$VERSION-src.tar.gz" ) "dojo-release-$VERSION-src.tar.gz.md5" ) ]]
      then
         echo "Integrity of $SAVE_AS failed!";
         echo "Delete $SAVE_AS.md5 and run the script again";
    	   exit 0;
      fi
   fi
}

if [[ -f "$SAVE_AS" && -f "$SAVE_AS.md5" ]]
then
   check_integrity;
else
   download_files;
   check_integrity;
fi

echo "Extracting file";
tar -xzvf $SAVE_AS

RELEASENAME="dojo-ucscis";
RELEASENAME_="releaseName=$RELEASENAME";
#optimize -> shrinksafe / shrinksafe.keepLines / closur / packer
#cssOptimize -> comments / comments.KeepLines
#layerOptimize -> shrinksafe | default:shrinksafe
#mini -> false/true | default:false
#stripConsole -> all / normal,warn / normal,error
OPTIMIZE_PROD="optimize=shrinksafe cssOptimize=comments mini=true layerOptimize=shrinksafe stripConsole=all";
OPTIMIZE_DEV="optimize=shrinksafe.keepLines cssOptimize=comments.keepLines";
ACTION="action=clean,release";
pushd .
cd $BASE_DIR/util/buildscripts/
./build.sh profile=../../../ucscis.profile.js $ACTION $OPTIMIZE_DEV version=$VERSION $RELEASENAME_
  

if [[ $? == 0 ]]
then
	echo "The build process competed without any error";
	echo "Installing $RELEASENAME ...";
	popd
	cd ../
	cp -fr "dojo-custom-build/$BASE_DIR/release/$RELEASENAME" .
	rm dojo-release
	ln -sf dojo-ucscis dojo-release
	
   echo 
	echo 'Include following three lines in <ucscis_root>/core/dojo_requier.php '
	echo '<script src="<?php echo JS; ?>/dojo/ucscis.js" type="text/javascript">
<script src="<?php echo JS; ?>/dijit/ucscis.js" type="text/javascript">
<script src="<?php echo JS; ?>/dojox/ucscis.js" type="text/javascript">
';
else
	echo "Error building dojo!";
fi
