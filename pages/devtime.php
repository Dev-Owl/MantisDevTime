<?php
    
    # --------Settings ------- #
    
    $Setting_TimeFieldID = 7;
    $projectsToCheck = [1,4,5,9,7];
    $devHourByVersion = 160;
    $devCommitTimeInPerenct = 40;
    $devTimeByVersion = $devHourByVersion * ($devCommitTimeInPerenct / 100);
    # --------Settings ------- #
    
    #header
	if( !defined( 'MANTIS_VERSION' ) ) { exit(); }
	layout_page_header( plugin_lang_get( 'title' ) );
	layout_page_begin();
?>
<div id="wrapper" style="margin-top:20px">
<?php
    $time_project_user ='SELECT usr.id as developer,sum(CAST(custom.value as signed)) as total_time,bug.target_version as version,bug.project_id'
    .' FROM mantis_bug_table as bug'
    .' INNER JOIN mantis_custom_field_string_table as custom on bug_id = bug.id and custom.field_id='.$Setting_TimeFieldID
    .' INNER JOIN mantis_project_table as pro ON  pro.id = bug.project_id'
    .' INNER JOIN mantis_project_version_table ver ON ver.version = bug.version'
    .' INNER JOIN mantis_user_table usr on usr.id = bug.handler_id'
    .' AND ver.released=0 and ver.obsolete=0'
    .' AND  LOWER(TRIM(bug.target_version)) <> "unplanned"'
    .' WHERE bug.STATUS >=50'
    .' AND custom.value <> ""'
    .' AND pro.enabled=1'
    .' GROUP BY bug.target_version, usr.id;';

    $all_active_projects = 'SELECT id,name FROM mantis_project_table WHERE enabled = 1';
    
    $all_devs_query = 'SELECT usr.id,usr.username FROM mantis_user_table as usr WHERE usr.enabled = 1 and usr.access_level >=55 and usr.access_level <> 90';
    $all_devs = [];
    $result = db_query( $all_devs_query);
    while( $t_row = db_fetch_array( $result ) )
    {
        $all_devs[$t_row["id"]] = $t_row["username"];
    }

    foreach($projectsToCheck as $item) {
        workOnProject($item,$all_devs);
    }

    function workOnProject($projectID,$all_devs){
        
        $get_project_name = 'SELECT name FROM mantis_project_table where id='.$projectID;
        $currentProjectName = db_result(db_query($get_project_name));
        echo '<h1>'.$currentProjectName.'</h1>';
        $all_active_version = 'SELECT proj.id as projectid,ver.id as versionid,CONCAT(proj.name, ": ",ver.version) as Version,ver.version as sversion FROM mantis_project_version_table as ver'
        .' INNER JOIN mantis_project_table as proj on proj.id = project_id'
        .' WHERE released=0 And obsolete=0 and lower(trim(version)) <>"unplanned" and proj.enabled=1 and proj.id='.$projectID.' ORDER BY version ASC';
        $result = db_query( $all_active_version);
        $activeVersion = [];
        echo '<table class="table table-striped"><thead><tr><th>Developer</th>';
        while( $t_row = db_fetch_array( $result ) )
        {
            $activeVersion[] = $t_row;
            echo "<th>";
            echo $t_row["version"];
            echo "</th>";  
        }
        echo '</tr></thead><tbody>';
        foreach($all_devs as $key => $dev) {
            echo '<tr>';
            echo '<td>';
            echo $dev;
            echo '</td>';
            //Work on projects by release and dev
            foreach($activeVersion as $version) {
                timeByVersionProjectUser($key,$projectID,$version["sversion"]);
            }
            echo '</tr>';
        }


        echo '</tbody></table>';

    }

    function timeByVersionProjectUser($user,$project,$version){
        global $Setting_TimeFieldID;
        global $devTimeByVersion;
        $getTotalTime = 'SELECT IFNULL(sum(CAST(custom.value as signed)),0) as total_time'
        .' FROM mantis_bug_table as bug '
        .' INNER JOIN mantis_custom_field_string_table as custom on bug_id = bug.id and custom.field_id='.$Setting_TimeFieldID
        .' WHERE bug.STATUS < 90 AND custom.value <> "" AND bug.handler_id = '.$user.' AND bug.project_id = '.$project.' AND bug.target_version = "'.$version.'"';
        $totaltime = db_result(db_query($getTotalTime));
        if($totaltime > $devTimeByVersion)
            echo '<td style="color:red">';
        else
            echo '<td>';
        echo $totaltime;
        echo "/";
        echo $devTimeByVersion;
        echo '</td>';
    }


?>


</div>

<?php layout_page_end(); 
	#footer
?>