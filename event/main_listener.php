<?php
/**
 *
 * RPG Stats. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Sauravisus
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace sauravisus\rpgstats\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * RPG Stats Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	protected $template;
	protected $user;
	protected $db;
	protected $log;
	protected $cache;
	protected $request;
	protected $config;
	protected $auth;
	protected $language;
	protected $root_path;
	protected $php_ext;
	protected $table_prefix;

	/**
	* Constructor
	*
	* @param \phpbb\template\template		 	$template
	* @param \phpbb\user						$user
	* @param \phpbb\db\driver\driver_interface	$db
	* @param \phpbb\controller\helper		 	$helper
	* @param \phpbb\log\log					 	$log
	* @param \phpbb\cache\service		 		$cache
	* @param \phpbb\request\request		 		$request
	* @param \phpbb\config\config				$config
	* @param string								$php_ext		   phpEx
	* @access public
	*/
	public function __construct(
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\log\log $log,
		\phpbb\cache\service $cache,
		\phpbb\request\request $request,
		\phpbb\config\config $config,
		\phpbb\auth\auth $auth,
		\phpbb\language\language $language,
		$root_path,
		$php_ext,
		$table_prefix
	)
	{
		$this->template 		= $template;
		$this->user 			= $user;
		$this->db 				= $db;
		$this->log 				= $log;
		$this->cache 			= $cache;
		$this->request 			= $request;
		$this->config 			= $config;
		$this->auth 			= $auth;
		$this->language			= $language;
		$this->php_ext 			= $php_ext;
		$this->root_path 		= $root_path;
		$this->table_prefix 	= $table_prefix;
		$this->stat_setup		= $this->table_prefix.'statSetup';
		$this->user_stats		= $this->table_prefix.'userStats';
		$this->user_table		= $this->table_prefix.'users';
		$this->limiter_table	= $this->table_prefix.'statLimiters';
		$this->user_groups		= $this->table_prefix.'user_group';
	}

	/**
	 * Assign functions defined in this class to event listeners in the core
	 *
	 * @return array
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_add_after'						=> 'handleDatabaseAdditions',
			'core.permissions'							=> 'permissions',
			'core.page_header_after'					=> 'moneyInHeader',
			'core.viewtopic_modify_post_data'			=> 'generateStatsForAvatar',
			'core.viewtopic_modify_post_row'			=> 'displayStatsBelowAvatar',
			'core.memberlist_view_profile'				=> 'statsInProfile',
			'core.acp_manage_group_request_data'		=> 'acp_manage_group_request_data',
			'core.acp_manage_group_initialise_data'		=> 'acp_manage_group_initialise_data',
			'core.acp_manage_group_display_form'		=> 'acp_manage_group_display_form',
			'core.user_setup'							=> 'user_setup',
		);
	}

	/**
	 * Add stats to the new user in the database
	 * 
	 * @param \phpbb\event\data $event The event object
	 */
	public function handleDatabaseAdditions($event)
	{
		$userId = $event['user_id'];
		$sql = 'SELECT id, defaultValue, display FROM ' . $this->stat_setup;
		$result = $this->db->sql_query($sql);
		$values = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		foreach($values as $data){
			$sql = "INSERT INTO " . $this->user_stats . " (userId, statId, value, display) VALUES ('".$userId."','".$data['id']."','".$data['defaultValue']."','".$data['display']."')";
			$result = $this->db->sql_query($sql);
			$this->db->sql_freeresult($result);
		}
	}
	
	public function generateStatsForAvatar($event){
		$rowset						= $event['rowset'];
		$topic_data					= $event['topic_data'];
		$poster_id					= array_unique(array_column($rowset, 'user_id'));
		$viewer_id					= $this->user->data['user_id'];
		$adminVisibilitySetting		= $this->config->offsetGet('adminVisibilityEnabled');
		$adminVisibilityLevel		= $this->config->offsetGet('adminVisibilityLevel');
		
		$rpg_stats = array();

		// Grab user's stats
		$sql = "SELECT $this->stat_setup.name, $this->stat_setup.id, $this->stat_setup.display AS org_display, $this->user_stats.value, $this->stat_setup.secret, $this->user_stats.display AS user_display, $this->user_stats.userId FROM $this->stat_setup INNER JOIN $this->user_stats ON $this->stat_setup.id = $this->user_stats.statId WHERE ".$this->db->sql_in_set($this->user_stats.'.userId', $poster_id);
		$result = $this->db->sql_query($sql);
		$i = 0;
		while ($row = $this->db->sql_fetchrow($result)){
			foreach($poster_id as $id){	
				if($row['userId'] == $id){
					$rpg_stats[(int) $id][] = array(
						'STAT_ID'			=> $row['id'],
						'STAT_NAME'			=> $row['name'],
						'STAT_VALUE'		=> $row['value'],
						'STAT_SECRET'		=> $row['secret'],
						'STAT_VISIBILITY'	=> $row['user_display'],
						'STAT_ORG_VIS'		=> $row['org_display'],
					);
				}
			}
		}
		$this->db->sql_freeresult($result);
		
		$event['topic_data'] = array_merge($event['topic_data'], array(
			'rpg_stats'	=> $rpg_stats,
		));
		$this->template->assign_vars(array(
			'U_VIEWER_ID'			=> $viewer_id,
			'U_CAN_EDIT'			=> ($this->auth->acl_get('m_chg_stats')) ? true : false,
			'A_VIS_SETTING'			=> $adminVisibilitySetting,
			'A_VIS_LEVEL'			=> $adminVisibilityLevel,
		));
	}
	
	public function displayStatsBelowAvatar($event){
		$poster_id = $event['poster_id'];
		$topic_data = $event['topic_data'];
		$post_row = $event['post_row'];

		$rpg_stats = $topic_data['rpg_stats'];

		$post_row['RPG_STATS'] = !empty($rpg_stats[(int) $poster_id]) ? $rpg_stats[(int) $poster_id] : array();
		
		$event['post_row'] = $post_row;
	}
	
	public function statsInProfile($event){
		$member						= $event['member'];
		$userId						= $member['user_id'];
		$viewer_id					= $this->user->data['user_id'];
		$adminVisibilitySetting		= $this->config->offsetGet('adminVisibilityEnabled');
		$adminVisibilityLevel		= $this->config->offsetGet('adminVisibilityLevel');
		
		// Grab user's stats
		$sql = "SELECT $this->stat_setup.name, $this->stat_setup.id, $this->stat_setup.display AS org_display, $this->user_stats.value, $this->stat_setup.secret, $this->user_stats.display AS user_display FROM $this->stat_setup INNER JOIN $this->user_stats ON $this->stat_setup.id = $this->user_stats.statId WHERE $this->user_stats.userId = $userId";
		$result = $this->db->sql_query($sql);
		$statValues = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		
		foreach($statValues as $stats)
		{
			$this->template->assign_block_vars('rpgstats', array(
				'STAT_ID'			=> $stats['id'],
				'STAT_NAME'			=> $stats['name'],
				'STAT_VALUE'		=> $stats['value'],
				'STAT_SECRET'		=> $stats['secret'],
				'STAT_VISIBILITY'	=> $stats['user_display'],
				'STAT_ORG_VIS'		=> $stats['org_display'],
			));
		}
		
		$this->template->assign_vars(array(
			'U_USER_ID'				=> $userId,
			'U_VIEWER_ID'			=> $viewer_id,
			'U_CAN_EDIT'			=> ($this->auth->acl_get('m_chg_stats')) ? true : false,
			'A_VIS_SETTING'			=> $adminVisibilitySetting,
			'A_VIS_LEVEL'			=> $adminVisibilityLevel,
		));
		
		$this->language->add_lang('common', 'sauravisus/rpgstats');
	}
	
	public function moneyInHeader($event){
		$user_id = $this->user->data['user_id'];
		
		// Grab user's money
		$sql = 'SELECT value
			FROM ' . $this->user_stats . '
			WHERE userId = '. $user_id .' AND statId = 3';
		$result = $this->db->sql_query($sql);
		$amount = $this->db->sql_fetchfield('value');
		$this->db->sql_freeresult($result);
		
		// Grab name of money stat
		$sql = 'SELECT name
			FROM ' . $this->stat_setup . '
			WHERE id = 3';
		$result = $this->db->sql_query($sql);
		$stat_name = $this->db->sql_fetchfield('name');
		$this->db->sql_freeresult($result);
		
		$this->template->assign_vars(array(
			'T_USER_ID'			=> $user_id,
			'T_MONEY_NAME'		=> $stat_name,
			'T_MONEY_AMOUNT'	=> $amount,
		));
		
		$this->language->add_lang('common', 'sauravisus/rpgstats');
	}
	
	public function permissions($event)
	{
		$permissions = $event['permissions'];
		$permissions += array(
			'u_use_stats'	=> array(
				'lang'			=> 'ACL_U_USE_STATS',
				'cat'			=> 'rpgstats'
			),
			'm_chg_stats'	=> array(
				'lang'			=> 'ACL_M_CHG_STATS',
				'cat'			=> 'rpgstats'
			),
			'a_stats'		=> array(
				'lang'			=> 'ACL_A_STATS',
				'cat'			=> 'rpgstats'
			),
		);
		$event['permissions']	= $permissions;
		$categories['rpgstats']	= 'ACL_CAT_STATS';
		$event['categories']	= array_merge($event['categories'], $categories);
	}
	
	public function acp_manage_group_request_data($event)
	{
		$submit_ary = $event['submit_ary'];
		$submit_ary['limiter'] = $this->request->variable('group_limiter', 0);
		$event['submit_ary'] = $submit_ary;
	}
	
	public function acp_manage_group_initialise_data($event)
	{
		$test_variables = $event['test_variables'];
		$test_variables['limiter'] = 'int';
		$event['test_variables'] = $test_variables;
	}
	
	public function acp_manage_group_display_form($event)
	{
		$group_row = $event['group_row'];
		$this->template->assign_vars(array(
			'GROUP_LIMITER' => $group_row['group_limiter'],
		));
		
		// Grab limiters
		$sql = "SELECT id, name FROM $this->limiter_table";
		$result = $this->db->sql_query($sql);
		$limiters = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		
		foreach($limiters as $limiter)
		{
			$this->template->assign_block_vars('limiters', array(
				'LIMITER_ID'	=> $limiter['id'],
				'LIMITER_NAME'	=> $limiter['name'],
			));
		}
	}
	
	public function user_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name'	=> 'sauravisus/rpgstats',
			'lang_set'	=> 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}
}