<?php
/**
 *
 * RPG Stats. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Sauravisus
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace sauravisus\rpgstats\migrations;

class install_acp_module extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v320\v320');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_RPGSTATS_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_RPGSTATS_TITLE',
				array(
					'module_basename'	=> '\sauravisus\rpgstats\acp\main_module',
					'modes'				=> array('stats','settings','limiters'),
				),
			)),
			array('config.add', array(
				'adminVisibilityEnabled', 0
			)),
			array('config.add', array(
				'adminVisibilityLevel', 0
			)),
			array('config.add', array(
				'canUsersDecreaseStats', 0
			)),
		);
	}
}
