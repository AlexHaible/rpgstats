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
 * RPG Stats ACP module.
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
	
    public function main($id, $mode)
    {
		global $phpbb_container;
        
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
		
		add_form_key('sauravisus_rpgstats_acp');
		
		if($mode == "stats")
		{
			$this->page_title = $this->language->lang('RPGSTATS_STATS_TITLE');
			$this->tpl_name = 'acp_rpgstats_stats';

			if ($this->request->is_set_post('edit') || $this->request->is_set_post('delete') || $this->request->is_set_post('new'))
			{
				$statName			= $this->db->sql_escape($this->request->variable('statName',''));
				$statDefaultValue	= $this->db->sql_escape($this->request->variable('statDefaultValue',0));
				$statMin			= $this->db->sql_escape($this->request->variable('statMin',0));
				$statMax			= $this->db->sql_escape($this->request->variable('statMax',0));
				$statDisplay		= $this->db->sql_escape($this->request->variable('statDisplay',0));
				$statHidden			= $this->db->sql_escape($this->request->variable('statHidden',0));
				$statId				= ($this->request->is_set_post('new') ? 0 : $this->db->sql_escape($this->request->variable('statId',0)));
			}
			
			if ($this->request->is_set_post('edit'))
			{
				if (!check_form_key('sauravisus_rpgstats_acp'))
				{
					trigger_error($this->language->lang('FORM_INVALID').' '. adm_back_link($this->u_action));
				}
				else 
				{
					$sql = "UPDATE $this->statSetup SET name = '$statName', defaultValue = '$statDefaultValue', min = '$statMin', max = '$statMax', display = '$statDisplay', secret = '$statHidden' WHERE id = $statId";
					$result = $this->db->sql_query($sql);
					$this->db->sql_freeresult($result);
					
					$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], $this->language->lang('RPGSTATS_STAT_EDITED').': '.$statName);
					trigger_error($this->language->lang('RPGSTATS_STAT_EDITED').' '. adm_back_link($this->u_action));
				}
			}
			if ($this->request->is_set_post('delete'))
			{
				if (!check_form_key('sauravisus_rpgstats_acp'))
				{
					trigger_error($this->language->lang('FORM_INVALID').' '. adm_back_link($this->u_action));
				}
				else 
				{
					$statId = $this->db->sql_escape($this->request->variable('statId',0));
					
					$sql = 'SELECT user_id FROM ' . $this->userTable;
					$result = $this->db->sql_query($sql);
					$userIds = $this->db->sql_fetchrowset($result);
					$this->db->sql_freeresult($result);
					
					foreach($userIds as $id)
					{
						$userId = $id['user_id'];
						$sql = "SELECT value FROM $this->userStats WHERE statId = $statId AND userId = $userId";
						$result = $this->db->sql_query($sql);
						$statValues = $this->db->sql_fetchrowset($result);
						$this->db->sql_freeresult($result);
						
						foreach($statValues as $stats)
						{
							$statValue = $stats['value'];
							$sql = "UPDATE $this->userStats SET value = value + $statValue WHERE statId = 1 AND userId = $userId";
							$result = $this->db->sql_query($sql);
							$this->db->sql_freeresult($result);
						}
					}
					
					$sql = "DELETE FROM ".$this->statSetup." WHERE id = ".$statId;
					$result = $this->db->sql_query($sql);
					$this->db->sql_freeresult($result);
					$sql = "DELETE FROM ".$this->userStats." WHERE statId = ".$statId;
					$result = $this->db->sql_query($sql);
					$this->db->sql_freeresult($result);
					
					$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], $this->language->lang('RPGSTATS_STAT_DELETED').': '.$statName);
					trigger_error($this->language->lang('RPGSTATS_STAT_DELETED').' '. adm_back_link($this->u_action));
				}
			}
			
			if ($this->request->is_set_post('new'))
			{
				if (!check_form_key('sauravisus_rpgstats_acp'))
				{
					trigger_error($this->language->lang('FORM_INVALID').' '. adm_back_link($this->u_action));
				}
				else
				{
					$sql = "INSERT INTO ".$this->statSetup." (name, defaultValue, min, max, display, secret) VALUES ('$statName',$statDefaultValue,$statMin,$statMax,$statDisplay,$statSecret)";
					$result = $this->db->sql_query($sql);
					$this->db->sql_freeresult($result);
					
					$sql = 'SELECT id, name, defaultValue, min, max, display, secret FROM ' . $this->statSetup;
					$result = $this->db->sql_query($sql);
					$statValues = $this->db->sql_fetchrowset($result);
					$this->db->sql_freeresult($result);
					
					$sql = 'SELECT user_id FROM ' . $this->userTable;
					$result = $this->db->sql_query($sql);
					$userIds = $this->db->sql_fetchrowset($result);
					$this->db->sql_freeresult($result);
					
					foreach($userIds as $id)
					{
						foreach($statValues as $stats)
						{
							$sql = "INSERT INTO ".$this->userStats." (name, defaultValue, min, max, display, secret) VALUES ('$statName',$statDefaultValue,$statMin,$statMax,$statDisplay,$statHidden)";
							$result = $this->db->sql_query($sql);
							$this->db->sql_freeresult($result);
						}
					}
					
					$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], $this->language->lang('RPGSTATS_STAT_CREATED').': '.$statName);
				}
			}
			
			$sql = 'SELECT id, name, defaultValue, min, max, display, secret FROM ' . $this->statSetup;
			$result = $this->db->sql_query($sql);
			$statValues = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);
			
			foreach($statValues as $stats)
			{
				$this->template->assign_block_vars('rpgstats', array(
					'STAT_ID'			=> $stats['id'],
					'STAT_NAME'			=> $stats['name'],
					'STAT_DEFAULTVALUE'	=> $stats['defaultValue'],
					'STAT_MIN'			=> $stats['min'],
					'STAT_MAX'			=> $stats['max'],
					'STAT_DISPLAY'		=> $stats['display'],
					'STAT_HIDDEN'		=> $stats['secret'],
				));
			}
			
			$this->template->assign_vars(array(
				'U_ACTION' => $this->u_action,
			));
		}
		if($mode == "settings")
		{
			$this->page_title = $this->language->lang('RPGSTATS_SETTINGS_TITLE');
			$this->tpl_name = 'acp_rpgstats_settings';
			
			if ($this->request->is_set_post('submit'))
			{
				if (!check_form_key('sauravisus_rpgstats_acp'))
				{
					trigger_error($this->language->lang('FORM_INVALID').' '. adm_back_link($this->u_action));
				}
				else
				{
					$visibilityOverride	= $this->request->variable('adminOverride',0);
					$visibilityLevel	= $this->request->variable('adminVisibility',0);
					$userDecrease		= $this->request->variable('userDecrease',0);
					
					$this->config->set('adminVisibilityEnabled', $visibilityOverride);
					$this->config->set('adminVisibilityLevel', $visibilityLevel);
					$this->config->set('canUsersDecreaseStats', $userDecrease);
					
					trigger_error($this->language->lang('RPGSTATS_SETTINGS_EDITED').' '. adm_back_link($this->u_action));
				}
			}
			
			if ($this->request->is_set_post('globalReallocation'))
			{
				if (!check_form_key('sauravisus_rpgstats_acp'))
				{
					trigger_error($this->language->lang('FORM_INVALID').' '. adm_back_link($this->u_action));
				}
				else
				{
					$sql = "UPDATE $this->userStats SET value = 1 + value WHERE statId = 2";
					$result = $this->db->sql_query($sql);
					$this->db->sql_freeresult($result);
					
					$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], $this->language->lang('RPGSTATS_GREALL_GIVEN'));
					trigger_error($this->language->lang('RPGSTATS_GREALL_GIVEN').' '. adm_back_link($this->u_action));
				}
			}
			
			if ($this->request->is_set_post('singleReallocation'))
			{
				if (!check_form_key('sauravisus_rpgstats_acp'))
				{
					trigger_error($this->language->lang('FORM_INVALID').' '. adm_back_link($this->u_action));
				}
				else
				{
					$username = $this->db->sql_escape($this->request->variable('username',''));
					$sql = "SELECT user_id FROM $this->userTable WHERE username = '$username'";
					$result = $this->db->sql_query($sql);
					$userId = $this->db->sql_fetchrowset($result);
					$this->db->sql_freeresult($result);
					$userId = $userId[0]['user_id'];
					
					$sql = "UPDATE $this->userStats SET value = 1 + value WHERE statId = 2 AND userId = $userId";
					$result = $this->db->sql_query($sql);
					$this->db->sql_freeresult($result);
					
					$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], $this->language->lang('RPGSTATS_SREALL_GIVEN').': '.$username);
					trigger_error($this->language->lang('RPGSTATS_SREALL_GIVEN').' '. adm_back_link($this->u_action));
				}
			}
			
			$this->template->assign_vars(array(
				'U_ACTION'						=> $this->u_action,
				'U_ADMIN_VISIBILITY_ENABLED'	=> $this->config->offsetGet('adminVisibilityEnabled'),
				'U_ADMIN_VISIBILITY_LEVEL'		=> $this->config->offsetGet('adminVisibilityLevel'),
				'U_USER_DECREASE_ENABLED'		=> $this->config->offsetGet('canUsersDecreaseStats'),
				'U_FIND_USER_URI'				=> append_sid('./../memberlist.php?mode=searchuser&form=rpgstats_sreall&field=username&select_single=true'),
			));
		}
	}
}
