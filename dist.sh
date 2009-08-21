#!/bin/bash
rm -f scormcloud-plugin-ilias4.zip
zip -r scormcloud-plugin-ilias4.zip * -x \*/.svn/* -x dist.sh -x todo.txt
