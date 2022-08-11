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

# If the Homebrew path was not found, try the default paths
if [[ -z "$brew" ]]; then
    arch_name="$(uname -m)"
    if [ "${arch_name}" = "x86_64" ]; then
        # Since munkireport uses an x86 version of Python 2.7 , it is possible we're on an
        # Apple Silicon Mac but our current environment is x86 through Rosetta 2.
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
