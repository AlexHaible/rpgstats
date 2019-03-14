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

class install_user_schema extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v320\v320');
	}

	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'statSetup'		=> array(
					'COLUMNS' => array(
						'id'			=> array('UINT', null, 'auto_increment'),
						'name'			=> array('VCHAR:255', ''),
						'defaultValue'	=> array('UINT', 0),
						'min'			=> array('UINT', 0),
						'max'			=> array('UINT', 0),
						'display'		=> array('UINT', 0),
						'secret'		=> array('UINT', 0),
					),
					'PRIMARY_KEY' => 'id'
				),
				$this->table_prefix . 'userStats'		=> array(
					'COLUMNS' => array(
						'id'			=> array('UINT', null, 'auto_increment'),
						'userId'		=> array('UINT', 0),
						'statId'		=> array('UINT', 0),
						'value'			=> array('UINT', 0),
						'display'		=> array('UINT', 0),
					),
					'PRIMARY_KEY' => 'id'
				),
				$this->table_prefix . 'statLimiters'	=> array(
					'COLUMNS' => array(
						'id'			=> array('UINT', null, 'auto_increment'),
						'name'			=> array('VCHAR:255', 0),
						'min'			=> array('UINT', 0),
						'max'			=> array('UINT', 0),
					),
					'PRIMARY_KEY' => 'id'
				),
			),
			'add_columns'	=> array(
				$this->table_prefix . 'groups'	=> array(
					'group_limiter'		=> array('UINT', 1),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'statSetup',
				$this->table_prefix . 'userStats',
				$this->table_prefix . 'statLimiters',
			),
			'drop_columns'	=> array(
				$this->table_prefix . 'groups'	=> array(
					'group_limiter',
				),
			),
		);
	}
	
	public function update_data()
	{
		return array(
			array('custom', array(
				array($this, 'insertDefaultData')
			)),
			// Add permissions
			array('permission.add', array('u_use_stats', true)),
			array('permission.add', array('m_chg_stats', true)),
			array('permission.add', array('a_stats', true)),
			// Set permissions
			array('permission.permission_set', array('REGISTERED', 'u_use_stats', 'group')),
			array('permission.permission_set', array('ADMINISTRATORS', 'a_stats', 'group')),
			array('permission.permission_set', array('ADMINISTRATORS', 'm_chg_stats', 'group')),
			array('permission.permission_set', array('GLOBAL_MODERATORS', 'm_chg_stats', 'group')),
		);
	}
	
	public function insertDefaultData()
	{
		$statSetup	= $this->table_prefix.'statSetup';
		$users		= $this->table_prefix.'users';
		$userStats	= $this->table_prefix.'userStats';
		$statLimit	= $this->table_prefix.'statLimiters';
		
		/* Default limiter of 0 */
		$sql = "INSERT INTO $statLimit (name, min, max) VALUES ('No Limiter',0,0)";
		$result = $this->db->sql_query($sql);
		$this->db->sql_freeresult($result);
		
		/* Protected stats */
		$sql = "INSERT INTO $statSetup (name, defaultValue, min, max, display, secret) VALUES ('Unallocated Points',0,0,0,0,0)";
		$result = $this->db->sql_query($sql);
		$this->db->sql_freeresult($result);
		$sql = "INSERT INTO $statSetup (name, defaultValue, min, max, display, secret) VALUES ('Reallocations',0,0,0,0,0)";
		$result = $this->db->sql_query($sql);
		$this->db->sql_freeresult($result);
		$sql = "INSERT INTO $statSetup (name, defaultValue, min, max, display, secret) VALUES ('Money',500,0,0,0,0)";
		$result = $this->db->sql_query($sql);
		$this->db->sql_freeresult($result);
		
		/* Default Stats */
		
		$sql = "INSERT INTO $statSetup (name, defaultValue, min, max, display, secret) VALUES ('STR',6,0,10,3,0)";
		$result = $this->db->sql_query($sql);
		$this->db->sql_freeresult($result);
		$sql = "INSERT INTO $statSetup (name, defaultValue, min, max, display, secret) VALUES ('DEX',5,0,10,3,0)";
		$result = $this->db->sql_query($sql);
		$this->db->sql_freeresult($result);
		$sql = "INSERT INTO $statSetup (name, defaultValue, min, max, display, secret) VALUES ('INT',7,0,10,3,0)";
		$result = $this->db->sql_query($sql);
		$this->db->sql_freeresult($result);
		$sql = "INSERT INTO $statSetup (name, defaultValue, min, max, display, secret) VALUES ('WIS',7,0,10,3,0)";
		$result = $this->db->sql_query($sql);
		$this->db->sql_freeresult($result);
		$sql = "INSERT INTO $statSetup (name, defaultValue, min, max, display, secret) VALUES ('CHA',4,0,10,3,0)";
		$result = $this->db->sql_query($sql);
		$this->db->sql_freeresult($result);
		$sql = "INSERT INTO $statSetup (name, defaultValue, min, max, display, secret) VALUES ('STA',8,0,10,3,0)";
		$result = $this->db->sql_query($sql);
		$this->db->sql_freeresult($result);
		
		/* Handle existing users */
		
		$sql = "SELECT id, defaultValue, display FROM $statSetup";
		$result = $this->db->sql_query($sql);
		$statValues = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		
		$sql = "SELECT user_id FROM $users WHERE group_id != 6";
		$result = $this->db->sql_query($sql);
		$userValues = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		foreach($userValues as $userData){
			$userId				= $userData['user_id'];
			foreach($statValues as $statData){
				$statId			= $statData['id'];
				$defaultValue	= $statData['defaultValue'];
				$display		= $statData['display'];
				
				$sql = "INSERT INTO $userStats (userId, statId, value, display) VALUES ('$userId','$statId','$defaultValue','$display')";
				$result = $this->db->sql_query($sql);
				$this->db->sql_freeresult($result);
			}
		}
	}
}
