<?php
/**
 *
 * RPG Stats. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Sauravisus
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace sauravisus\rpgstats\acp;

/**
 * RPG Stats ACP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\sauravisus\rpgstats\acp\main_module',
			'title'		=> 'RPGSTATS_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'RPGSTATS_SETTINGS',
					'auth'	=> 'ext_sauravisus/rpgstats && acl_a_board && acl_a_stats',
					'cat'	=> array('RPGSTATS_TITLE')
				),
				'stats'	=> array(
					'title'	=> 'RPGSTATS_STATS',
					'auth'	=> 'ext_sauravisus/rpgstats && acl_a_board && acl_a_stats',
					'cat'	=> array('RPGSTATS_TITLE')
				),
				'limiters'	=> array(
					'title'	=> 'RPGSTATS_LIMITERS',
					'auth'	=> 'ext_sauravisus/rpgstats && acl_a_board && acl_a_stats',
					'cat'	=> array('RPGSTATS_TITLE')
				),
			),
		);
	}
}
