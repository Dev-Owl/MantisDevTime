<?php
    require_once("devtime_core.php"); #load devtime plugin data
    #header
	if( !defined( 'MANTIS_VERSION' ) ) { exit(); }
	layout_page_header( plugin_lang_get( 'title' ) );
	layout_page_begin();
?>
<div id="wrapper" style="margin-top:20px">
<?php
    //Build up developer list
    $all_devs_query = 'SELECT usr.id,usr.username FROM mantis_user_table as usr WHERE usr.enabled = 1 and usr.access_level >=55 and usr.access_level <> 90';
    $all_devs = [];
    $result = db_query( $all_devs_query);
    while( $t_row = db_fetch_array( $result ) )
    {
        $all_devs[$t_row["id"]] = $t_row["username"];
    }
    //Build up total overview
    overviewNextVersion($all_devs);

    //Build up table for time overview
    foreach($projectsToCheck as $projectID) {
        workOnProject($projectID,$all_devs); //Project to check and developer to check
    }
?>
</div>

<?php 
    #footer
    layout_page_end(); 
?>