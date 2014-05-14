#!/bin/sh

WRK_DIR=`pwd`

echo "set permissions..."
chmod -R a+w $WRK_DIR/html/install/temp
chmod -R a+w $WRK_DIR/html/user_data
chmod -R a+w $WRK_DIR/html/upload
chmod -R a+w $WRK_DIR/data/cache
chmod -R a+w $WRK_DIR/data/downloads
chmod -R a+w $WRK_DIR/data/Smarty
chmod -R a+w $WRK_DIR/data/class
chmod -R a+w $WRK_DIR/data/logs
chmod -R a+w $WRK_DIR/data/upload
chmod -R a+w $WRK_DIR/data/config
chmod -R a+w $WRK_DIR/html

echo "finished."
