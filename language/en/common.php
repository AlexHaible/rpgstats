<?php
/**
 *
 * RPG Stats. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Sauravisus
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'VIEWING_RPGSTATS'					=> 'Viewing RPG Stats',
	'ACP_RPGSTATS_TITLE'				=> 'RPG Stats Admin',
	'MCP_RPGSTATS_TITLE'				=> 'RPG Stats Moderator',
	'UCP_RPGSTATS_TITLE'				=> 'RPG Stats User',
	'RPGSTATS_TITLE'					=> 'RPG Stats',
	'RPGSTATS_SETTINGS_TITLE'			=> 'RPG Stats Configuration',
	'RPGSTATS_STATS_TITLE'				=> 'RPG Stats Settings',
	'RPGSTATS_REALLOCATION_TITLE'		=> 'Stat Reallocation',
	'RPGSTATS_GREALLOCATION_TITLE'		=> 'Global Reallocation',
	'RPGSTATS_SREALLOCATION_TITLE'		=> 'Single Reallocation',
	'RPGSTATS_LIMITER'					=> 'Group Limiter',
	'RPGSTATS_STATNAME'					=> 'Name of the stat',
	'RPGSTATS_DEFAULTVALUE'				=> 'Default value',
	'RPGSTATS_MIN'						=> 'Minimum value',
	'RPGSTATS_MAX'						=> 'Maximum value',
	'RPGSTATS_DISPLAY'					=> 'Where to display stats?',
	'RPGSTATS_DISPLAY_HIDDEN'			=> 'Nowhere',
	'RPGSTATS_DISPLAY_PROFILE'			=> 'Profile page',
	'RPGSTATS_DISPLAY_AVATAR'			=> 'Below avatar',
	'RPGSTATS_DISPLAY_BOTH'				=> 'Profile Page & Below Avatar',
	'RPGSTATS_HIDDEN_STAT'				=> 'Is the stat hidden?',
	
	'RPGSTATS_DELETE'					=> 'Delete',
	'RPGSTATS_EDIT'						=> 'Save',
	'RPGSTATS_GO_EDIT'					=> 'Edit this users stats',
	'RPGSTATS_SUBMIT'					=> 'Submit',
	'RPGSTATS_NEW'						=> 'Add',
	'RPGSTATS_REALLOCATION_SUBMIT'		=> 'Give Reallocation',
	'RPGSTATS_NO'						=> 'No',
	'RPGSTATS_YES'						=> 'Yes',
	
	'RPGSTATS_SYSTEM_STATS'				=> 'Required Stats',
	'RPGSTATS_CUSTOM_STATS'				=> 'Custom Stats',
	'RPGSTATS_ADD_NEW'					=> 'Add new stat',
	'RPGSTATS_LIMITERS'					=> 'Limiters',
	'RPGSTATS_STATS'					=> 'Stats',
	'RPGSTATS_ENABLED'					=> 'Enabled',
	'RPGSTATS_DISABLED'					=> 'Disabled',
	'RPGSTATS_ADMIN_OVERRIDE'			=> 'Admin Visibility Override',
	'RPGSTATS_ADMIN_VISIBILITY'			=> 'Admin Visibility Level',
	'RPGSTATS_USER_DECREASE'			=> 'Can users decrease their stats?',
	'RPGSTATS_FIND_USER'				=> 'Find User',
	
	'RPGSTATS_STAT_EDITED'				=> 'Stat edited',
	'RPGSTATS_STAT_DELETED'				=> 'Stat deleted',
	'RPGSTATS_LIMITER_EDITED'			=> 'Limiter edited',
	'RPGSTATS_LIMITER_DELETED'			=> 'Limiter deleted',
	'RPGSTATS_SETTINGS_EDITED'			=> 'Settings changed',
	'FROM_VALUE'						=> 'from value',
	'TO_VALUE'							=> 'to value',
	'ON_USER'							=> 'on user',
	'REASONING'							=> 'Reasoning for stat addition',
	'RPGSTATS_REASON_EXPLAIN'			=> 'Please enter the reason for the stats granted into this text field',
	
	'RPGSTATS_GREALLOCATION_EXPLAIN'	=> 'Clicking this button will give every user on your forum a stat reallocation credit. They can use this credit to freely reallocate their stats as they so desire.',
));