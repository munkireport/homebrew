#!/usr/local/munkireport/munkireport-python3

import subprocess
import os
import plistlib
import re
import pwd
import json

def get_brew_info(brew):

    # Get homebrew's owner because it runs everything in the user's context
    homebrew_owner = pwd.getpwuid(os.stat(brew).st_uid).pw_name
    cachedir = '%s/cache' % os.path.dirname(os.path.realpath(__file__))

    # We need this awful, ugly workaround because homebrew is evil when it comes to scripting as `root`
    os.system("/usr/bin/sudo -HE -u "+homebrew_owner+" "+brew+" info --json=v2 --installed > "+cachedir+"/homebrew.json")

    # Load the homebrew json
    homebrew_json = json.loads(open(cachedir+"/homebrew.json", 'r').read().strip())

    result = []

    # Process casks
    if "casks" in homebrew_json:
        for brew in homebrew_json["casks"]:
            cask = {'brew_json':str(brew)}

            if "name" in brew and isinstance(brew["name"], list):
                cask["name"] = ''.join(brew["name"])
                cask["full_name"] = ''.join(brew["name"])
            elif "name" in brew:
                cask["name"] = brew["name"]

            if "conflicts_with" in brew and isinstance(brew["conflicts_with"], list):
                cask["conflicts_with"] = ', '.join(brew["conflicts_with"])
            elif "conflicts_with" in brew and isinstance(brew["conflicts_with"], str):
                cask["conflicts_with"] = brew["conflicts_with"]

            if "desc" in brew and brew["desc"] != None:
                cask["desc"] = brew["desc"]
            if "homepage" in brew and brew["homepage"] != None:
                cask["homepage"] = brew["homepage"]
            if "caveats" in brew and brew["caveats"] != None:
                cask["caveats"] = brew["caveats"]
            if "deprecation_date" in brew and brew["deprecation_date"] != None:
                cask["deprecation_date"] = brew["deprecation_date"]
            if "deprecation_reason" in brew and brew["deprecation_reason"] != None:
                cask["deprecation_reason"] = brew["deprecation_reason"]

            if "outdated" in brew:
                cask["outdated"] = to_bool(brew["outdated"]) # Boolean
            if "pinned" in brew:
                cask["pinned"] = to_bool(brew["pinned"]) # Boolean
            if "deprecated" in brew:
                cask["deprecated"] = to_bool(brew["deprecated"]) # Boolean

            if "installed" in brew:
                cask["installed_version"] = brew["installed"]
            if "installed_time" in brew:
                cask["install_time"] = str(brew["installed_time"]) #####

            result.append(cask)

    # Process formulae
    if "formulae" in homebrew_json:
        for brew in homebrew_json["formulae"]:
            bottle = {'brew_json':str(brew)}

            if "full_name" in brew:
                bottle["full_name"] = brew["full_name"]
            if "name" in brew:
                bottle["name"] = brew["name"]

            if "oldnames" in brew and len(brew["oldnames"]) > 0: # Array to string
                bottle["oldname"] = ', '.join(brew["oldnames"])
            if "aliases" in brew and len(brew["aliases"]) > 0: # Array to string
                bottle["aliases"] = ', '.join(brew["aliases"])
            if "dependencies" in brew and len(brew["dependencies"]) > 0: # Array to string
                bottle["dependencies"] = ', '.join(brew["dependencies"])
            if "build_dependencies" in brew and len(brew["build_dependencies"]) > 0: # Array to string
                bottle["build_dependencies"] = ', '.join(brew["build_dependencies"])
            if "recommended_dependencies" in brew and len(brew["recommended_dependencies"]) > 0: # Array to string
                bottle["recommended_dependencies"] = ', '.join(brew["recommended_dependencies"])
            if "optional_dependencies" in brew and len(brew["optional_dependencies"]) > 0: # Array to string
                bottle["optional_dependencies"] = ', '.join(brew["optional_dependencies"])
            if "requirements" in brew and len(brew["requirements"]) > 0: # Array to string
                requirements = []
                for requirement in brew["requirements"]:
                    if "name" in requirement:
                        requirements.append(requirement["name"])
                bottle["requirements"] = ', '.join(requirements)
            try:
                if "options" in brew and len(brew["options"]) > 0: # Array to string
                    bottle["options"] = ', '.join(brew["options"])
            except:
                pass
            if "conflicts_with" in brew and len(brew["conflicts_with"]) > 0: # Array to string
                bottle["conflicts_with"] = ', '.join(brew["conflicts_with"])

            if "desc" in brew and brew["desc"] != None:
                bottle["desc"] = brew["desc"]
            if "homepage" in brew and brew["homepage"] != None:
                bottle["homepage"] = brew["homepage"]
            if "linked_keg" in brew and brew["linked_keg"] != None:
                bottle["linked_keg"] = brew["linked_keg"]
            if "caveats" in brew and brew["caveats"] != None:
                bottle["caveats"] = brew["caveats"]
            if "deprecation_date" in brew and brew["deprecation_date"] != None:
                bottle["deprecation_date"] = brew["deprecation_date"]
            if "deprecation_reason" in brew and brew["deprecation_reason"] != None:
                bottle["deprecation_reason"] = brew["deprecation_reason"]

            if "keg_only" in brew:
                bottle["keg_only"] = to_bool(brew["keg_only"]) # Boolean
            if "outdated" in brew:
                bottle["outdated"] = to_bool(brew["outdated"]) # Boolean
            if "pinned" in brew:
                bottle["pinned"] = to_bool(brew["pinned"]) # Boolean
            if "deprecated" in brew:
                bottle["deprecated"] = to_bool(brew["deprecated"]) # Boolean

            if "installed" in brew:
                newest_install = brew["installed"][-1]

                if "built_as_bottle" in newest_install:
                    bottle["built_as_bottle"] = to_bool(newest_install["built_as_bottle"]) # Boolean
                if "installed_as_dependency" in newest_install:
                    bottle["installed_as_dependency"] = to_bool(newest_install["installed_as_dependency"]) # Boolean
                if "installed_on_request" in newest_install:
                    bottle["installed_on_request"] = to_bool(newest_install["installed_on_request"]) # Boolean
                if "poured_from_bottle" in newest_install:
                    bottle["newest_install"] = to_bool(newest_install["poured_from_bottle"]) # Boolean
                if "time" in newest_install:
                    bottle["install_time"] = str(newest_install["time"]) #####
                if "version" in newest_install:
                    bottle["installed_version"] = str(newest_install["version"]) #####

                if "used_options" in newest_install and len(newest_install["used_options"]) > 0: # Array to string
                    bottle["used_options"] = ', '.join(newest_install["used_options"])


                if "runtime_dependencies" in newest_install and len(newest_install["runtime_dependencies"]) > 0:
                    runtime_dependencies = []

                    for dependency in newest_install["runtime_dependencies"]:
                        if "full_name" in dependency:
                            runtime_dependencies.append(str(dependency["full_name"]))

                    if len(runtime_dependencies) > 0: # Array to string
                        bottle["runtime_dependencies"] = ', '.join(runtime_dependencies)

                for version_installed in brew["installed"]:

                    if "version" in version_installed and len(version_installed["version"]) > 0:
                        installed_versions = []

                        for version_version in version_installed["version"]:
                            if "full_name" in version_version:
                                installed_versions.append(version_installed["version"])

                        if len(installed_versions) > 0: # Array to string
                            bottle["installed_versions"] = ', '.join(installed_versions)

            if "versions" in brew:
                if "head" in brew["versions"] and brew["versions"]["head"] == "HEAD":
                    bottle["versions_head"] = "1" # Boolean
                else:
                    bottle["versions_head"] = "0" # Boolean
                
                if "devel" in brew["versions"] and brew["versions"]["devel"] != "":
                    bottle["versions_devel"] = "1" # Boolean
                else:
                    bottle["versions_devel"] = "0" # Boolean

                if "bottle" in brew["versions"]:
                    bottle["versions_bottle"] = to_bool(brew["versions"]["bottle"]) # Boolean

                if "stable" in brew["versions"]:
                    bottle["versions_stable"] = brew["versions"]["stable"]

            result.append(bottle)

    # Cleanup after ourselves
    try:
        os.remove(cachedir+"/homebrew.json")
    except OSError:
        pass

    return result

def to_bool(s):
    if s == "":
        return ""
    elif s == True or s == "YES" or s == "Yes" or s == "yes" or s == "1":
        return 1
    else:
        return 0

def main():
    """Main"""

    # Remove old homebrew.sh script, if it exists
    if os.path.isfile(os.path.dirname(os.path.realpath(__file__))+'/homebrew.sh'):
        os.remove(os.path.dirname(os.path.realpath(__file__))+'/homebrew.sh')

    # Check if homebrew exists
    if os.path.isfile('/usr/local/bin/brew'):
        # If Intel Mac
        brew="/usr/local/bin/brew"
        result = get_brew_info(brew)
    elif os.path.isfile('/opt/homebrew/bin/brew'):
        # Else if Apple Silicon Mac
        brew="/opt/homebrew/bin/brew"
        result = get_brew_info(brew)
    else:
        # We have no brew installed
        print("Homebrew is not installed, skipping")
        result = []

    # Write homebrew results to cache
    cachedir = '%s/cache' % os.path.dirname(os.path.realpath(__file__))
    output_plist = os.path.join(cachedir, 'homebrew.plist')
    try:
        plistlib.writePlist(result, output_plist)
    except:
        with open(output_plist, 'wb') as fp:
            plistlib.dump(result, fp, fmt=plistlib.FMT_XML)

if __name__ == "__main__":
    main()
