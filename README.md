# MantisDevTime

## Description

The plugin allows you to get an overview about forecasted time a developer will spend on different projects and it also includes an overall view that shows the total time for the next version by developer in mantis.

## Installation
The installation is the same as for all Mantis plugins:

1. Download the repo folder
2. Copy it into your Mantis installation plugin folder *(make sure the folder is called MantisDevTime)*
3. Login to Mantis as an Admin, open the Configuration -> Plugin section and click on install

![alt text](https://raw.githubusercontent.com/Dev-Owl/MantisDevTime/master/devtime_example.jpg "Plugin in mantis")

## Configuration

All the plugin configuration can be found in the file pages/devtime_conf.php, the following settings are available:
* $Setting_TimeFieldID = ID of the custom field that contains the calculated time for this task in mantis (field must be integer)
* $projectsToCheck =  Array of project ID´s to check, either get the ID from the DB or click on the manage menu item in mantis and check the URL
* $devHourByVersion = Total time for developer by version 
* $devCommitTimeInPerenct = Percentage value of time you want to commit by version
* $devTimeByVersion = ceil( $devHourByVersion * ($devCommitTimeInPerenct / 100)); default calculation to get the time/version
* $projectPriority = Priority in percent by project as array, [[1=50]]


## Usage

After installation and configuration just click on the new menu item in Mantis called Dev Time, you will get the overview for all configured projects
