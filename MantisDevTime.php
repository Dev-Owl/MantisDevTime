<?php
if( !defined( 'MANTIS_VERSION' ) ) { exit(); }


class TaskProgressPlugin extends MantisPlugin {

    # Plugin definition
	function register() {
		$this->name         = plugin_lang_get( 'title' );
		$this->description  = plugin_lang_get ( 'description' );
		$this->page         = '';

		$this->version      = '0.0.1';
		$this->requires = array(
			'MantisCore' => '2.0.0',
		);

		$this->author       = 'Christian Muehle';
		$this->contact      = 'info@devowl.de';
		$this->url          = 'http://devowl.de';
	}
	
    # Plugin configuration
	function config() {
		return array(
            'access_threshold'  => DEVELOPER, // Set global access level requireed to access plugin
		);
	}

    # Plugin hooks
    function hooks() {
       /* return array(
            'EVENT_VIEW_BUG_EXTRA'           => 'addProgressBarToPage',
        );*/
    }
}

?>
