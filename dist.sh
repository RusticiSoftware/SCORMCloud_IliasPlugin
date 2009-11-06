#!/bin/bash
rm -rf ../build
rm -rf ../dist
mkdir ../dist
mkdir -p ../build/Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud
cp -r . ../build/Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud
rm -f ../dist/scormcloud-plugin-ilias4.zip
cd ../build
rm -f ../ScormCloud/scormcloud-plugin-ilias4.zip
zip -r ../ScormCloud/scormcloud-plugin-ilias4.zip * -x \*/.svn/* -x \*dist.sh -x \*/todo.txt -x \*/*.zip   
rm -rf ../build
