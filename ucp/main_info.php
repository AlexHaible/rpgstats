<?php
/**
 *
 * RPG Stats. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Sauravisus
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace sauravisus\rpgstats\ucp;

/**
 * RPG Stats UCP module info.
 */
class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\sauravisus\rpgstats\ucp\main_module',
			'title'		=> 'UCP_RPGSTATS_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'UCP_RPGSTATS',
					'auth'	=> 'ext_sauravisus/rpgstats && acl_u_use_stats',
					'cat'	=> array('UCP_RPGSTATS_TITLE')
				),
			),
		);
	}
}
