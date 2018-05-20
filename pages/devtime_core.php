<?php
require_once("devtime_conf.php");
function workOnProject($projectID,$all_devs){
    $get_project_name = 'SELECT name FROM mantis_project_table where id='.$projectID;
    $currentProjectName = db_result(db_query($get_project_name));
    echo '<h1>'.$currentProjectName;
    echo ' '.getProjectPriority($projectID) . "%";
    echo '</h1>';
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

function time_usr_project_version($user,$project,$version){
    global $Setting_TimeFieldID;
    $getTotalTime = 'SELECT IFNULL(sum(CAST(custom.value as signed)),0) as total_time'
    .' FROM mantis_bug_table as bug '
    .' INNER JOIN mantis_custom_field_string_table as custom on bug_id = bug.id and custom.field_id='.$Setting_TimeFieldID
    .' WHERE bug.STATUS < 90 AND custom.value <> "" AND bug.handler_id = '.$user.' AND bug.project_id = '.$project.' AND bug.target_version = "'.$version.'"';
    return db_result(db_query($getTotalTime));
}

function timeByVersionProjectUser($user,$project,$version){
    
    global $devTimeByVersion;
    $totaltime = time_usr_project_version($user,$project,$version);
    $finalProjectTime = ceil($devTimeByVersion * ( getProjectPriority($project) / 100));
    if($totaltime > $finalProjectTime)
        echo '<td style="color:red">';
    else
        echo '<td>';
    echo $totaltime;
    echo "/";
    echo $finalProjectTime;
    echo '</td>';
}

function getProjectPriority($project){
    global $projectPriority;
    if(array_key_exists($project,$projectPriority))
    {
        return $projectPriority[$project];
    }
    return 100;
}

function overviewNextVersion($devs)
{
    global $projectsToCheck;
    global $devTimeByVersion;
    echo '<h1>Overview - Next version</h1>';
    
    echo '<table class="table table-striped"><thead><tr><th>Developer</th>';
    $projectNextVersion = [];
    
    foreach($projectsToCheck as $project){
        $nextVersionByProject = 'SELECT  proj.name as project,version FROM mantis_project_version_table as ver'
        .' inner join mantis_project_table as proj ON proj.ID = ver.project_id '
        .' WHERE obsolete=0 AND released=0 AND trim(lower(version)) <> "unplanned" AND project_id= '.$project
        .' ORDER BY date_order ASC LIMIT 1';
        $queryResult = db_query( $nextVersionByProject );
        echo '<th>';
        while( $t_row = db_fetch_array($queryResult))
        {
            $projectNextVersion[$project] = [$t_row["version"],$t_row["project"]];
            echo  $projectNextVersion[$project][1]."(".$projectNextVersion[$project][0].")";
        }
        
        echo '</th>';
    }
    echo '<th>Total</th>';
    echo '</tr>';
    echo '<tbody>';
    foreach($devs as $key => $devname) {
        $total = 0;
        echo '<tr>';
        echo '<td>';
        echo $devname;
        echo '</td>';
        foreach($projectNextVersion as $project => $versioninfo){
           $forVersion = time_usr_project_version($key,$project,$versioninfo[0]);
           $total += $forVersion;
           echo '<td>';
           echo $forVersion;
           echo '</td>';
        }

        if($total > $devTimeByVersion)
            echo '<td style="color:red">';
        else
            echo '<td>';

        echo $total;
        echo '/';
        echo $devTimeByVersion;
        echo '</td>';
    }
    echo '</tbody>';
    echo '</table>';
}

?>