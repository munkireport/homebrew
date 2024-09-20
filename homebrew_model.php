<?php

use CFPropertyList\CFPropertyList;

class Homebrew_model extends \Model {

    function __construct($serial='')
    {
        parent::__construct('id', 'homebrew'); // Primary key, tablename
        $this->rs['id'] = '';
        $this->rs['serial_number'] = $serial;
        $this->rs['name'] = '';
        $this->rs['full_name'] = '';
        $this->rs['oldname'] = null;
        $this->rs['aliases'] = null;
        $this->rs['desc'] = null;
        $this->rs['homepage'] = null;
        $this->rs['installed_versions'] = null;
        $this->rs['versions_stable'] = null;
        $this->rs['linked_keg'] = null;  
        $this->rs['dependencies'] = null;
        $this->rs['build_dependencies'] = null;
        $this->rs['recommended_dependencies'] = null;
        $this->rs['runtime_dependencies'] = null;
        $this->rs['optional_dependencies'] = null;
        $this->rs['requirements'] = null;
        $this->rs['options'] = null;
        $this->rs['used_options'] = null;
        $this->rs['caveats'] = null;
        $this->rs['conflicts_with'] = null;
        $this->rs['built_as_bottle'] = 0; //TF
        $this->rs['installed_as_dependency'] = null; //TF
        $this->rs['installed_on_request'] = null; //TF
        $this->rs['poured_from_bottle'] = null; //TF
        $this->rs['versions_bottle'] = null; //TF
        $this->rs['keg_only'] = null; //TF
        $this->rs['outdated'] = null; //TF
        $this->rs['pinned'] = null; //TF
        $this->rs['versions_devel'] = null; //TF
        $this->rs['versions_head'] = null; //TF
        $this->rs['brew_json'] = null;
        $this->rs['deprecated'] = null; //TF
        $this->rs['deprecation_date'] = null;
        $this->rs['deprecation_reason'] = null;
        $this->rs['install_time'] = null;
        $this->rs['installed_version'] = null;

        $this->serial_number = $serial;
    }

    // ------------------------------------------------------------------------

    /**
     * Process data sent by postflight
     *
     * @param string data
     * @author tuxudo
     **/
    function process($data)
    {
         // Check if data was uploaded
        if (! $data) {
            throw new Exception("Error Processing homebrew Module Request: No data found", 1);
        } else if (substr( $data, 0, 30 ) != '<?xml version="1.0" encoding="' ) { // Else if old style json, process with old json based handler

            // Delete previous set
            $this->deleteWhere('serial_number=?', $this->serial_number);

            // Process json into object thingy
            $brews = json_decode($data, true);

            $booleans = array('built_as_bottle','installed_as_dependency','installed_on_request','poured_from_bottle','keg_only','outdated','pinned','versions_bottle','versions_head');

            $nestedarrays = array('versions','requirements','options','installed');

            foreach ($brews as $singlebrew){

                // Traverse the brew
                foreach ($singlebrew as $key => $field) {

                    // Format booleans before processing
                    if (in_array($key, $booleans) && $field == "true") {
                        // Send a 1 to the db
                        $this->$key = '1';
                    } else if (in_array($key, $booleans)) {
                        // Send a 0 to the db
                        $this->$key = '0';
                    } else if (! empty($field) && ! is_array($field)) { 
                        // If key is not empty, save it to the object
                        $this->$key = $field;
                    } else if (is_array($field) && ! in_array($key, $nestedarrays) && ! empty($field) && $key != "bottle"){
                        // If is an array and not a nested array, is not empty, and is not the bottle array, condense it to a string and save it
                        if (! count(array_filter(array_keys($field), 'is_string')) > 0){
                            $this->$key = implode(", ", $field);
                        }
                    } else if ($key == "requirements" && ! empty($field)){
                        // Fill out the requirements values from the requirements array
                        $requirements = "";
                        foreach ($field as $requirement){
                            $requirements .= $requirement["name"].", ";
                        }
                        $this->requirements = trim($requirements, ", ");
                    } else if ($key == "versions" && ! empty($field)){
                        // Fill out the versions_ values from the versions array
                        $this->versions_stable = $field["stable"];
                        // versions_devel is a 0/1 for false/true
                        if (array_key_exists("devel", $field) && $field["devel"] != ""){
                            $this->versions_devel = '1';
                        } else{
                            $this->versions_devel = '0';
                        }
                        // versions_bottle is a 0/1 for false/true
                        if (array_key_exists("head", $field) && $field["head"] == "bottle"){
                            $this->versions_bottle = '1';
                        } else{
                            $this->versions_bottle = '0';
                        }
                        // versions_head is a 0/1 for false/true
                        if (array_key_exists("head", $field) && $field["head"] == "HEAD"){
                            $this->versions_head = '1';
                        } else{
                            $this->versions_head = '0';
                        }
                    } else if ($key == "installed" && ! empty($field)){
                        // installed_versions
                        $installed_versions = "";
                        foreach ($field as $installed_version){
                            $installed_versions .= $installed_version["version"].", ";
                        }
                        $this->installed_versions = trim($installed_versions, ", ");

                        $newestinstall = array_pop($field);

                        // check if options array is not blank, then fill from installed
                        if (! empty($newestinstall["used_options"])){
                            // If is an array and not a nested array, is not empty, and is not the bottle array, condense it to a string and save it
                            $this->used_options = implode(", ", $newestinstall["used_options"]);
                        } else {
                            $this->used_options = ""; 
                        }
                        // built_as_bottle is a 0/1 for false/true
                        if ($newestinstall["built_as_bottle"] == "true"){
                            $this->built_as_bottle = '1';
                        } else{
                            $this->built_as_bottle = '0';
                        }
                        // installed_as_dependency is a 0/1 for false/true
                        if ($newestinstall["installed_as_dependency"] == "true"){
                            $this->installed_as_dependency = '1';
                        } else{
                            $this->installed_as_dependency = '0';
                        }
                        // installed_on_request is a 0/1 for false/true
                        if ($newestinstall["installed_on_request"] == "true"){
                            $this->installed_on_request = '1';
                        } else{
                            $this->installed_on_request = '0';
                        }
                        // poured_from_bottle is a 0/1 for false/true
                        if ($newestinstall["poured_from_bottle"] == "true"){
                            $this->poured_from_bottle = '1';
                        } else{
                            $this->poured_from_bottle = '0';
                        }
                        // runtime_dependencies
                        if (array_key_exists("runtime_dependencies", $newestinstall)) {
                            if ($newestinstall["runtime_dependencies"] != null){
                                $this->runtime_dependencies = implode(", ", array_column($newestinstall["runtime_dependencies"], 'full_name'));
                            } else {
                                $this->runtime_dependencies = "";
                            }
                        } else {
                                $this->runtime_dependencies = "";
                        }

                    } else if ($key == "options" && ! empty($field)){
                        // options
                        $options = "";
                        foreach ($field as $option){
                            $options .= $option["option"]." (".$option["description"]."), ";
                        }
                        $this->options = trim($options, ", ");

                    } else if ($field == "0" && ! is_array($field)){
                        // Set the value to 0 if it's 0
                        $this->$key = $field;
                    } else {  
                        // Else, null the value
                        $this->$key = '';   
                    }
                    // }
                    // Save the bottles
                    $this->id = '';
                    $this->save();
                }
            }
        } else { // Else process with new XML handler 

            // Process incoming hombrew_info.plist
            $parser = new CFPropertyList();
            $parser->parse($data, CFPropertyList::FORMAT_XML);
            $plist = $parser->toArray();

            // Delete previous set
            $this->deleteWhere('serial_number=?', $this->serial_number);

            // Process each homebrew thing
            foreach ($plist as $brew) {

                // Add the serial number to each entry
                $brew['serial_number'] = $this->serial_number;

                foreach ($this->rs as $key => $value){
                    // If key does not exist in $brew, null it
                    if ( ! array_key_exists($key, $brew) || $brew[$key] == '' && $brew[$key] != '0') {
                        $this->rs[$key] = null;
                    // Set the db fields to be the same as those in the brew file
                    } else {
                        $this->rs[$key] = $brew[$key];
                    }
                }

                // Save the bottles of cider Mmmmmm tasty
                $this->id = '';
                $this->save();
            }
        }
    }
}