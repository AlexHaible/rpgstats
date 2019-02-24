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
	'VIEWING_RPGSTATS'				=> 'Viewing RPG Stats',
	'ACP_RPGSTATS_TITLE'			=> 'RPG Stats',
	'MCP_RPGSTATS_TITLE'			=> 'RPG Stats',
	'UCP_RPGSTATS_TITLE'			=> 'RPG Stats',
	'RPGSTATS_TITLE'				=> 'RPG Stats',
	'RPGSTATS_SETTINGS'				=> 'Settings',
	'RPGSTATS_STATNAME'				=> 'Name of the stat',
	'RPGSTATS_DEFAULTVALUE'			=> 'Default value',
	'RPGSTATS_MIN'					=> 'Minimum value',
	'RPGSTATS_MAX'					=> 'Maximum value',
	'RPGSTATS_DISPLAY'				=> 'Where to display stats?',
	'RPGSTATS_DISPLAY_HIDDEN'		=> 'Hidden',
	'RPGSTATS_DISPLAY_PROFILE'		=> 'Profile page',
	'RPGSTATS_DISPLAY_AVATAR'		=> 'Below avatar',
	'RPGSTATS_DISPLAY_BOTH'			=> 'Profile Page & Below Avatar',
	'RPGSTATS_DISPLAY_ORIGINAL'		=> 'Original Placement',
	'RPGSTATS_DELETE'				=> 'Delete',
	'RPGSTATS_EDIT'					=> 'Save',
	'RPGSTATS_SYSTEM_STATS'			=> 'Required Stats',
	'RPGSTATS_CUSTOM_STATS'			=> 'Custom Stats',
	'RPGSTATS_STAT_EDITED'			=> 'Stat edited',
	'RPGSTATS_STAT_DELETED'			=> 'Stat deleted',
	'RPGSTATS_ADD_NEW'				=> 'Add new stat',
	'RPGSTATS_NEW'					=> 'Add',
	'RPGSTATS_STATS'				=> 'Stats',
	'RPGSTATS_GREALL_GIVEN'			=> 'Reallocation token granted to all users',
	'RPGSTATS_SREALL_GIVEN'			=> 'Reallocation token granted to user',
));