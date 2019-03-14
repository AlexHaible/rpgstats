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
 * RPG Stats MCP module.
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
        
		$this->db			= $phpbb_container->get('dbal.conn');
		$this->language		= $phpbb_container->get('language');
		$this->log			= $phpbb_container->get('log');
		$this->user			= $phpbb_container->get('user');
		$this->request		= $phpbb_container->get('request');
		$this->template		= $phpbb_container->get('template');
		$this->config		= $phpbb_container->get('config');
		
		$this->language->add_lang('common', 'sauravisus/rpgstats');
		
		$this->tablePrefix	= $phpbb_container->getParameter('core.table_prefix');
		$this->statSetup	= $this->tablePrefix.'statSetup';
		$this->userStats	= $this->tablePrefix.'userStats';
		$this->userTable	= $this->tablePrefix.'users';
		$this->statLimit	= $this->tablePrefix.'statLimiters';
		$this->userGroup	= $this->tablePrefix.'user_group';
		$this->groupTabl	= $this->tablePrefix.'groups';
		
		$this->tpl_name = 'mcp_rpgstats_body';
		$this->page_title = $this->user->lang('MCP_RPGSTATS_TITLE');
		add_form_key('sauravisus_rpgstats_mcp');

		if($mode == "settings"){
			if ($this->request->is_set_post('submituser') || $this->request->variable('u',0))
			{
				if (!check_form_key('sauravisus_rpgstats_mcp') && !$this->request->variable('u',0))
				{
					trigger_error($this->language->lang('FORM_INVALID'));
				}
				else
				{
					if ($this->request->is_set_post('submituser')){
						$username = $this->db->sql_escape($this->request->variable('targetUser',''));
						$sql = "SELECT user_id, group_id FROM $this->userTable WHERE username = '$username'";
						$result = $this->db->sql_query($sql);
						$userId = $this->db->sql_fetchfield('user_id');
						$this->db->sql_freeresult($result);
					} else {
						$userId = $this->request->variable('u',0);
						$sql = "SELECT username, group_id FROM $this->userTable WHERE user_id = '$userId'";
						$result = $this->db->sql_query($sql);
						$username = $this->db->sql_fetchfield('username');
						$this->db->sql_freeresult($result);
					}
					
					$sql = "SELECT $this->statSetup.name, $this->statSetup.id, $this->userStats.value, $this->statSetup.secret FROM $this->statSetup INNER JOIN $this->userStats ON $this->statSetup.id = $this->userStats.statId WHERE $this->userStats.userId = $userId";
					$result = $this->db->sql_query($sql);
					$statValues = $this->db->sql_fetchrowset($result);
					$this->db->sql_freeresult($result);
					
					foreach($statValues as $stats)
					{
						$this->template->assign_block_vars('rpgstats', array(
							'STAT_ID'		=> $stats['id'],
							'STAT_NAME'		=> $stats['name'],
							'STAT_VALUE'	=> $stats['value'],
							'STAT_SECRET'	=> $stats['secret'],
						));
					}
						
					$this->template->assign_vars(array(
						'U_POSTED'	=> true,
						'U_ID'		=> $userId,
						'U_NAME'	=> $username,
						'U_TEST'	=> $this->request->variable('u',0),
						'U_HAS_GET'	=> $this->request->variable('u',0),
					));
				}
			}
			if ($this->request->is_set_post('updateStat'))
			{
				if (!check_form_key('sauravisus_rpgstats_mcp'))
				{
					trigger_error($this->language->lang('FORM_INVALID').'<br/><br/><a href="javascript:void;" onclick="window.history.go(-1)">Go back</a>');
				}
				else
				{
					$statId = $this->db->sql_escape($this->request->variable('statId',''));
					$newStatValue = $this->db->sql_escape($this->request->variable('newStatValue',''));
					$userId = $this->db->sql_escape($this->request->variable('userId',''));
					$username = $this->db->sql_escape($this->request->variable('userName',''));
					$statName = $this->db->sql_escape($this->request->variable('statName',''));
					
					$sql = "SELECT value FROM $this->userStats WHERE statId = $statId AND userId = $userId";
					$result = $this->db->sql_query($sql);
					$oldStatValue = $this->db->sql_fetchrowset($result);
					$this->db->sql_freeresult($result);
					
					$oldStatValue = $oldStatValue[0]['value'];
					
					$sql = "UPDATE $this->userStats SET value = $newStatValue WHERE statId = $statId AND userId = $userId";
					$result = $this->db->sql_query($sql);
					$this->db->sql_freeresult($result);
					
					$statsEdited	= $this->language->lang('RPGSTATS_STAT_EDITED');
					$colon			= $this->language->lang('COLON');
					$fromValue		= $this->language->lang('FROM_VALUE');
					$toValue		= $this->language->lang('TO_VALUE');
					$onUser			= $this->language->lang('ON_USER');
					
					$logString = "$statsEdited$colon $statName, $fromValue$colon $oldStatValue, $toValue$colon $newStatValue $onUser$colon <a href='./memberlist.php?mode=viewprofile&u=$userId'>$username</a>";
					
					$this->log->add('mod', $this->user->data['user_id'], $this->user->data['user_ip'], $logString);
					trigger_error($this->language->lang('MCP_STATS_EDITED').'<br/><br/><a href="javascript:void;" onclick="window.history.go(-1)">Go back</a>');
				}
			}
			
			$this->template->assign_vars(array(
				'U_FIND_USERNAME'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&amp;form=rpgstats_mcp_select_user&amp;field=targetUser&amp;select_single=true'),
				'U_POST_ACTION'		=> $this->u_action,
			));
		}
	}
}