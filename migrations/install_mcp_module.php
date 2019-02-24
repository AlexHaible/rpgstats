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

class install_mcp_module extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		$sql = 'SELECT module_id
			FROM ' . $this->table_prefix . "modules
			WHERE module_class = 'mcp'
				AND module_langname = 'MCP_RPGSTATS_TITLE'";
		$result = $this->db->sql_query($sql);
		$module_id = $this->db->sql_fetchfield('module_id');
		$this->db->sql_freeresult($result);

		return $module_id !== false;
	}

	static public function depends_on()
	{
		return array('\sauravisus\rpgstats\migrations\install_user_schema');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'mcp',
				0,
				'MCP_RPGSTATS_TITLE'
			)),
			array('module.add', array(
				'mcp',
				'MCP_RPGSTATS_TITLE',
				array(
					'module_basename'	=> '\sauravisus\rpgstats\mcp\main_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
