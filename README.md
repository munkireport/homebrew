Homebrew module
==============

Module provides a breakdown on everything currently installed by Homebrew. It can be installed separately from the `homebrew_info` module.

This module requires Homebrew to be installed [https://brew.sh/](https://brew.sh/)

Table Schema
-----
* name - varchar(255) - name of item installed by brew
* full_name - varchar(255) - full name of item installed by brew
* oldname - varchar(255) - previous name of item, if renamed by brew
* aliases - varchar(255) - aliases of item
* desc - varchar(255) - description of item
* homepage - varchar(255) - homepage of item
* installed_versions - varchar(255) - all versions currently installed
* versions_stable - varchar(255) - current stable version
* linked_keg - varchar(255) - version linked for use
* dependencies - text - dependencies of the item
* build_dependencies - text - dependencies required to build
* recommended_dependencies - text - recommended dependencies
* runtime_dependencies - text - dependencies needed to execute binary
* optional_dependencies - text - optional dependencies
* requirements - varchar(255) - requirements of the item
* options - text - options available at install/build time
* used_options - text - options used when installing/building item
* caveats - text - caveats of using/installing/building the item
* conflicts_with - varchar(255) - other items this item conflicts with
* homepage - boolean - item's home page
* built_as_bottle - boolean - was built as a bottle
* installed_as_dependency - boolean - installed as a dependency of another item
* installed_on_request - boolean - installed on request
* poured_from_bottle - boolean - poured from a bottle
* versions_bottle - boolean - is available as a bottle
* keg_only - boolean - is only a keg (not link)
* outdated - boolean - was detected as outdated last brew run
* pinned - boolean - version pinned
* versions_devel - boolean - developmental version available
* versions_head - boolean - head available to build from
* brew_json - text - full text of the item's info (currently disabled)
* deprecated - boolean - if item is deprecated
* deprecation_date - varchar(255) - when item was deprecated
* deprecation_reason - varchar(255) - why was deprecated
* install_time - big int - timestamp of when brew item was installed
* installed_version - varchar(255) - version of brew item that was installed