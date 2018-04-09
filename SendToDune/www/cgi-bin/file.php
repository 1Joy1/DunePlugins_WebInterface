#!/bin/sh

################################################################################
# @script-name: do
# @author: Andrii Kopyniak
# @ctime: 17.09.2012
# @mtime: 13.12.2012
# 
# @description: Setting up the environment on the dune.
################################################################################

cgi_plugin_env ()
{
  STORE_IFS=$IFS
  IFS='/' array=($PWD)

  n=${#array[@]}
  export PLUGIN_NAME="${array[$n-3]}"

  buf=
  for ((i=1; i<=$n-3; i++))
  do
    buf="$buf/${array[$i]}";
  done                                 
  export PLUGIN_INSTALL_DIR_PATH="$buf"

  export PLUGIN_TMP_DIR_PATH="/tmp/plugins/$PLUGIN_NAME"        
  export PLUGIN_WWW_URL="http://127.0.0.1/plugins/$PLUGIN_NAME/"        
  export PLUGIN_CGI_URL="http://127.0.0.1/cgi-bin/plugins/$PLUGIN_NAME/"

  PERSISTFS_DATA_DIR_PATH="/persistfs/plugins_data/$PLUGIN_NAME"
  FLASHDATA_DATA_DIR_PATH="/flashdata/plugins_data/$PLUGIN_NAME"
  if [ -d "$PERSISTFS_DATA_DIR_PATH" ]; then              
    export PLUGIN_DATA_DIR_PATH="$PERSISTFS_DATA_DIR_PATH"
  elif [ -d "$FLASHDATA_DATA_DIR_PATH" ]; then            
    export PLUGIN_DATA_DIR_PATH="$FLASHDATA_DATA_DIR_PATH"
  else                          
    export PLUGIN_DATA_DIR_PATH=
  fi

  IFS=$STORE_IFS
}


################################################################################
# Begin
################################################################################
cgi_plugin_env

LD_LIBRARY_PATH="$LD_LIBRARY_PATH:./lib:/usr/lib:/tango3/firmware_ext/flashlite/lib"
SCRIPT_FILENAME=`echo $SCRIPT_FILENAME | sed -n 's/^\(\/.*\/\).*/\1file/p'`

./php-cgi

################################################################################
# End
################################################################################
