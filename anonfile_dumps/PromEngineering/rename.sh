#! /bin/bash
old=$1
new=${old//[\''"?*%#^!@$&()=+[]{};,`~']}
new=${new//['<>:\|']/-}                   # I removed /, see below.
if [[ -e $new ]] ; then
    if [[ $old != $new ]] ; then
        echo Cannot rename "$old" to "$new" - target already exists.
    fi
else
    mv "$old" "$new"
fi
