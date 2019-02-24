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
	'MCP_RPGSTATS'				=> 'Edit Stats',
	'MCP_RPGSTATS_TITLE'		=> 'Stats',
	'MCP_RPGSTATS_EDIT'			=> 'User Stats',
	'RPGSTATS_VALUE'			=> 'Value',
	'RPGSTATS_UPDATE_STAT'		=> 'Update Stat',
	'MCP_STATS_EDITED'			=> 'Stat Updated',
));
