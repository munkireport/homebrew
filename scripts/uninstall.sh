#!/bin/bash

# Remove homebrew scripts
rm -f "${MUNKIPATH}preflight.d/homebrew.sh"
rm -f "${MUNKIPATH}preflight.d/homebrew.py"

# Remove homebrew.json and plist file
rm -f "${CACHEPATH}homebrew.json"
rm -f "${CACHEPATH}homebrew.plist"
