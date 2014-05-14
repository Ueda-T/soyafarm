#!/bin/sh
svn propset -R 'svn:ignore' '*' data/Smarty/templates_c
svn propset -R 'svn:ignore' '*' data/cache
svn propset -R 'svn:ignore' '*' data/download
svn propset -R 'svn:ignore' '*' data/logs
svn propset -R 'svn:ignore' '*' data/upload

svn propset -R 'svn:ignore' '*' html/upload
svn propset -R 'svn:ignore' '*' html/install
svn propset -R 'svn:ignore' '*' html/upload/graph_image
svn propset -R 'svn:ignore' '*' html/upload/mobile_image
svn propset -R 'svn:ignore' '*' html/upload/save_image
svn propset -R 'svn:ignore' '*' html/upload/temp_image

