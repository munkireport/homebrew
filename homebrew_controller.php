<?php 

/**
 * homebrew module class
 *
 * @package munkireport
 * @author tuxudo
 **/
class Homebrew_controller extends Module_controller
{
    /*** Protect methods with auth! ****/
    function __construct()
    {
        // Store module path
        $this->module_path = dirname(__FILE__);
    }

    /**
     * Default method
     * @author tuxudo
     *
     **/
    function index()
    {
        echo "You've loaded the homebrew module!";
    }

    /**
     * Retrieve data in json format
     *
     **/
    public function get_data($serial_number = '')
    {
        // Remove non-serial number characters
        $serial_number = preg_replace("/[^A-Za-z0-9_\-]]/", '', $serial_number);

        $obj = new View();

        if (! $this->authorized()) {
            $obj->view('json', array('msg' => 'Not authorized'));
            return;
        }

        $sql = "SELECT name, full_name, oldname, aliases, `desc`, homepage, install_time, installed_version, installed_versions, versions_stable, linked_keg, dependencies, build_dependencies, recommended_dependencies, runtime_dependencies, optional_dependencies, requirements, options, used_options, caveats, conflicts_with, built_as_bottle, installed_as_dependency, installed_on_request, poured_from_bottle, versions_bottle, keg_only, outdated, deprecated, deprecation_date, deprecation_reason, pinned, versions_devel, versions_head
                        FROM homebrew
                        WHERE serial_number = '$serial_number'";

        $queryobj = new Homebrew_model();
        $homebrew_tab = $queryobj->query($sql);
        $obj->view('json', array('msg' => current(array('msg' => $homebrew_tab)))); 
    }
} // END class Homebrew_controller
