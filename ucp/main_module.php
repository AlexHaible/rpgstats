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
 * RPG Stats UCP module.
 */
class main_module
{
	/** @var string */
	public $page_title;
	/** @var string */
	public $tpl_name;
	/** @var string */
	public $u_action;
	/** @var string */
	public $statSetup;
	/** @var \phpbb\request\request */
	protected $request;
	/** @var \phpbb\config\config */
	protected $config;
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\language\language */
	protected $language;
	/** @var \phpbb\log\log */
	protected $log;
	/** @var \phpbb\user */
	protected $user;

	function main($id, $mode)
	{
		global $phpbb_container, $phpEx, $phpbb_root_path;
        
		$this->db		= $phpbb_container->get('dbal.conn');
		$this->language	= $phpbb_container->get('language');
		$this->log		= $phpbb_container->get('log');
		$this->user		= $phpbb_container->get('user');
		$this->request	= $phpbb_container->get('request');
		$this->template	= $phpbb_container->get('template');
		$this->config	= $phpbb_container->get('config');
		
		$this->language->add_lang('common', 'sauravisus/rpgstats');
		
		$this->statSetup = $phpbb_container->getParameter('sauravisus.rpgstats.table.statsetup');
		$this->userStats = $phpbb_container->getParameter('sauravisus.rpgstats.table.userstats');
		$this->userTable = $phpbb_container->getParameter('sauravisus.rpgstats.table.usertable');

		$this->tpl_name = 'ucp_rpgstats_body';
		$this->page_title = $this->user->lang('UCP_RPGSTATS_TITLE');
		add_form_key('sauravisus_rpgstats_ucp');
			
		function GetPrefixedItemsFromArray($array, $prefix){
			$keys = array_keys($array);
			$result = array();
			foreach ($keys as $key){
				if (strpos($key, $prefix) === 0){
					$result[$key] = $array[$key];
				}
			}
			return $result;
		}

		if ($this->request->is_set_post('updateStats'))
		{
			if (!check_form_key('sauravisus_rpgstats_ucp'))
			{
				trigger_error($this->user->lang('FORM_INVALID'));
			}
			
			$userId = $this->user->data['user_id'];
			$post_vars_array = $this->request->variable_names(\phpbb\request\request_interface::POST); 
			
			$workingArray = array();
			foreach($post_vars_array as $post){
				$workingArray[$post] = $this->db->sql_escape($this->request->variable($post,''));
			}
			
			$sql = "SELECT $this->statSetup.id FROM $this->statSetup";
			$result = $this->db->sql_query($sql);
			$numberOfIds = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);
			
			$groupingArray = array();
			foreach($numberOfIds as $id){
				$groupingArray[] = GetPrefixedItemsFromArray($workingArray, $id['id']);
			}
			$i = 1;
			foreach($groupingArray as $array){
				$statIdKey		= $i.'_statId';
				$statValueKey	= $i.'_statValue';
				$statDisplayKey	= $i.'_statDisplay';
				$statNameKey	= $i.'_statName';
				$statId			= (int) $this->db->sql_escape($array[$statIdKey]);
				$newStatValue	= (int) $this->db->sql_escape($array[$statValueKey]);
				$statDisplay	= (int) $this->db->sql_escape($array[$statDisplayKey]);
				$statName		= $this->db->sql_escape($array[$statNameKey]);
				
				$sql = "SELECT $this->userStats.value FROM $this->userStats WHERE statId = $statId AND userId = $userId";
				$result = $this->db->sql_query($sql);
				$oldStatValue = $this->db->sql_fetchrowset($result);
				$this->db->sql_freeresult($result);
				$oldStatValue = $oldStatValue[0]['value'];
				
				$sql = "UPDATE $this->userStats SET value = $newStatValue, display = $statDisplay WHERE statId = $statId AND userId = $userId";
				$result = $this->db->sql_query($sql);
				$this->db->sql_freeresult($result);
				
				$statsEdited	= $this->language->lang('UCP_STATS_EDITED');
				$colon			= $this->language->lang('COLON');
				$fromValue		= $this->language->lang('FROM_VALUE');
				$toValue		= $this->language->lang('TO_VALUE');
				
				$logString = "$statsEdited$colon $statName, $fromValue$colon $oldStatValue, $toValue$colon $newStatValue";
				if($oldStatValue != $newStatValue){
					$this->log->add('user', $userId, $this->user->data['user_ip'], $logString, time(), array('reportee_id' => $userId));
				}
				$i++;
			}
			
			$message = $this->user->lang('UCP_RPGSTATS_SAVED') . '<br /><br />' . $this->user->lang('RETURN_UCP', '<a href="' . $this->u_action . '">', '</a>');
			trigger_error($message);
		} 
		if ($this->request->is_set_post('doReallocation'))
		{
			if (!check_form_key('sauravisus_rpgstats_ucp'))
			{
				trigger_error($this->user->lang('FORM_INVALID'));
			}
			
			$userId = $this->user->data['user_id'];
			
			$sql = "SELECT statId, value FROM $this->userStats WHERE userId = $userId";
			$result = $this->db->sql_query($sql);
			$statValues = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);
			
			$prevUnallocated = (int) 0;
			$newUnallocated = (int) 0;
			foreach($statValues as $stats){
				if($stats['statId'] != 2 && $stats['statId'] != 3){$newUnallocated = $newUnallocated + $stats['value'];}
				if($stats['statId'] == 1){$prevUnallocated = $prevUnallocated + $stats['value'];}
			}
			
			$sql = "UPDATE $this->userStats SET value = $newUnallocated WHERE statId = 1 AND userId = $userId";
			$result = $this->db->sql_query($sql);
			$this->db->sql_freeresult($result);
			
			$sql = "UPDATE $this->userStats SET value = 0 WHERE statId <> 1 AND statId <> 2 AND statId <> 3 AND userId = $userId";
			$result = $this->db->sql_query($sql);
			$this->db->sql_freeresult($result);
			
			$sql = "UPDATE $this->userStats SET value = value - 1 WHERE statId = 2 AND userId = $userId";
			$result = $this->db->sql_query($sql);
			$this->db->sql_freeresult($result);
			
			$this->log->add('user', $userId, $this->user->data['user_ip'], $this->language->lang('RPGSTATS_REALLOCATION_PERFORMED').': '.$prevUnallocated.', '.$newUnallocated, time(), array('reportee_id' => $userId));
			$message = $this->user->lang('RPGSTATS_REALLOCATION_COMPLETE') . '<br /><br />' . $this->user->lang('RETURN_UCP', '<a href="' . $this->u_action . '">', '</a>');
			trigger_error($message);
			
		} 
		else 
		{
			$userId = $this->user->data['user_id'];
			
			$sql = "SELECT $this->statSetup.name, $this->statSetup.id, $this->userStats.value, $this->statSetup.secret, $this->userStats.display, $this->statSetup.min, $this->statSetup.max FROM $this->statSetup INNER JOIN $this->userStats ON $this->statSetup.id = $this->userStats.statId WHERE $this->userStats.userId = $userId";
			$result = $this->db->sql_query($sql);
			$statValues = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);
			
			$i=0;
			$unallocatedPoints=0;
			foreach($statValues as $stats)
			{
				$this->template->assign_block_vars('rpgstats', array(
					'STAT_ID'		=> $stats['id'],
					'STAT_NAME'		=> $stats['name'],
					'STAT_VALUE'	=> $stats['value'],
					'STAT_SECRET'	=> $stats['secret'],
					'STAT_DISPLAY'	=> $stats['display'],
					'STAT_MIN'		=> $stats['min'],
					'STAT_MAX'		=> $stats['max'],
				));
				$i++;
			}
			
			$this->template->assign_vars(array(
				'U_ID'					=> $userId,
				'S_UCP_ACTION'			=> $this->u_action,
				'CAN_DECREASE'			=> $this->config->offsetGet('canUsersDecreaseStats'),
				'NUMBER_OF_STATS'		=> $i,
			));
		}
	}
}
