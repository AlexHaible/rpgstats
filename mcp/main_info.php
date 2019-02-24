<?php
/**
 *
 * RPG Stats. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Sauravisus
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace sauravisus\rpgstats\mcp;

/**
 * RPG Stats MCP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\sauravisus\rpgstats\mcp\main_module',
			'title'		=> 'MCP_RPGSTATS_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'MCP_RPGSTATS',
					'auth'	=> 'ext_sauravisus/rpgstats && acl_m_chg_stats',
					'cat'	=> array('MCP_RPGSTATS_TITLE')
				),
			),
		);
	}
}
