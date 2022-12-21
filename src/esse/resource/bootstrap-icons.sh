#!/bin/bash

if [ -x "$(command -v rsync)" ]; then

    me=`basename "$0"`
    cd "$( dirname "${BASH_SOURCE[0]}" )"

    echo "start : $me"

    rsync -a ../../../vendor/twbs/bootstrap-icons/font/./ ../css/bootstrap-icons/
    echo "$me : updated bootstrap-icons"
else

    echo "rsync command not found .."
fi
