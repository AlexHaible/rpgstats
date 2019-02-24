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
	'UCP_RPGSTATS'						=> 'Stats',
	'UCP_RPGSTATS_TITLE'				=> 'Stats',
	'RPGSTATS_VALUE'					=> 'Value',
	'RPGSTATS_UPDATE_STAT'				=> 'Save changes',
	'RPGSTATS_REALLOCATE'				=> 'Use Reallocation | Amount remaining',
	'RPGSTATS_POINTS_REMAINING'			=> 'Points remaining',
	'RPGSTATS_REALLOCATION_COMPLETE'	=> 'All points reset. You can now reallocate points.',
	'RPGSTATS_REALLOCATION_PERFORMED'	=> 'Reallocation performed. Previous total and new total are',
	'UCP_RPGSTATS_USER'					=> '%s',
	'UCP_RPGSTATS_SAVED'				=> 'Stat changes have been saved successfully!',
	'UCP_STATS_EDITED'					=> 'Stats edited',
));
