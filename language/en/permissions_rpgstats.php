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
	'ACL_U_USE_STATS'	=> 'Can use the stat system',
	'ACL_M_CHG_STATS'	=> 'Can edit stats',
	'ACL_A_STATS'		=> 'Can change config of stat system',
	'ACL_CAT_STATS'		=> 'RPGStats',
));