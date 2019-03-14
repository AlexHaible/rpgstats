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
	'ACP_RPGSTATS_TITLE'				=> 'RPG Stats',
	'MCP_RPGSTATS_TITLE'				=> 'RPG Stats',
	'UCP_RPGSTATS_TITLE'				=> 'RPG Stats',
	'RPGSTATS_TITLE'					=> 'RPG Stats',
	'RPGSTATS_LIMITERS_TITLE'			=> 'RPG Stat Limiters',
	'RPGSTATS_SETTINGS'					=> 'Settings',
	'RPGSTATS_STATNAME'					=> 'Name of the stat',
	'RPGSTATS_DEFAULTVALUE'				=> 'Default value',
	'RPGSTATS_MIN'						=> 'Minimum value',
	'RPGSTATS_MAX'						=> 'Maximum value',
	'RPGSTATS_DISPLAY'					=> 'Where to display stats?',
	'RPGSTATS_DISPLAY_HIDDEN'			=> 'Hidden',
	'RPGSTATS_DISPLAY_PROFILE'			=> 'Profile page',
	'RPGSTATS_DISPLAY_AVATAR'			=> 'Below avatar',
	'RPGSTATS_DISPLAY_BOTH'				=> 'Profile Page & Below Avatar',
	'RPGSTATS_DISPLAY_ORIGINAL'			=> 'Original Placement',
	'RPGSTATS_DELETE'					=> 'Delete',
	'RPGSTATS_EDIT'						=> 'Save',
	'RPGSTATS_SYSTEM_STATS'				=> 'Required Stats',
	'RPGSTATS_CUSTOM_STATS'				=> 'Custom Stats',
	'RPGSTATS_STAT_EDITED'				=> 'Stat edited',
	'RPGSTATS_STAT_DELETED'				=> 'Stat deleted',
	'RPGSTATS_ADD_NEW'					=> 'Add new stat',
	'RPGSTATS_NEW'						=> 'Add',
	'RPGSTATS_STATS'					=> 'Stats',
	'RPGSTATS_GREALL_GIVEN'				=> 'Reallocation token granted to all users',
	'RPGSTATS_SREALL_GIVEN'				=> 'Reallocation token granted to user',
	'RPGSTATS_LIMITERS_EXPLANATION'		=> 'This feature allows admins to make group limiters that are prioritized over a stats individual min/max settings. A max value of 0 means there\'s no limit.',
	'RPGSTATS_LIMITER_EXPLAINATION'		=> 'Pick a limiter to set for this group if so desired. Default is "No Limiter".',
	'RPGSTATS_ADD_NEW_LIMITER'			=> 'Add new limiter',
	'RPGSTATS_LIMITERNAME'				=> 'Limiter name',
	'RPGSTATS_SYSTEM_LIMITERS'			=> 'Required Limiters',
	'RPGSTATS_CUSTOM_LIMITERS'			=> 'Custom Limiters',
));