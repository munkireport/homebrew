#!/bin/sh
# Made by tuxudo

# homebrew installed bottles and stuff information

# Skip manual check
if [ "$1" = 'manualcheck' ]; then
	echo 'Manual check: skipping'
	exit 0
fi

# Check if homebrew is installed
CURRENTUSER=$(echo "show State:/Users/ConsoleUser" | scutil | awk '/Name :/ && ! /loginwindow/ { print $3 }')

if [[ $CURRENTUSER != "" ]]; then
    brew=$(sudo -i -u $CURRENTUSER command -v brew)
fi

if [[ -z "$brew" ]]; then
    arch_name="$(uname -m)"
    if [ "${arch_name}" = "x86_64" ]; then
        if [ "$(sysctl -in sysctl.proc_translated)" = "1" ]; then
            brew="/opt/homebrew/bin/brew" # Running through Rosetta 2
        else
            brew="/usr/local/bin/brew" # Intel
        fi 
    elif [ "${arch_name}" = "arm64" ]; then
        brew="/opt/homebrew/bin/brew"
    fi
fi


if [[ -f $brew ]]; then

    # Create cache dir if it does not exist
    DIR=$(dirname $0)
    mkdir -p "$DIR/cache"
    homebrewfile="$DIR/cache/homebrew.json"

    # First cd to / to get around non-existant directory error
    # The sudo is needed to escape brew.sh's UID of 0 check
    cd /; sudo -HE -u nobody $brew info --json=v1 --installed > "${homebrewfile}"

else

    echo "Homebrew is not installed, skipping"

fi
exit 0
