<?php    
   
    # --------Settings ------- #
    $Setting_TimeFieldID = 7; //Custom field that contains the calculated time for a task in mantis
    $projectsToCheck = [1,4,5,9,7]; //Projects to check
    $devHourByVersion = 160; //Total hours by dev by version
    $devCommitTimeInPerenct = 40; //Time you want to commi
    $devTimeByVersion = ceil( $devHourByVersion * ($devCommitTimeInPerenct / 100)); //Calculate total time by dev and version according to the rules
    $projectPriority = [1 => 50,4 => 10, 5 => 10,9 => 10, 7 => 20 ]; //Priority by project
    # --------Settings ------- #
?>