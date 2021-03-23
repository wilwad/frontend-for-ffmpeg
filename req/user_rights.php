<?php
/*
 * Define user rights for the menus
 */

// defined rights

$arr_options = [		 						
  				 'home'=>['icon'=>'fa fa-fw fa-home', 
							   'title'=>'View your options', 
							   'intro-text'=>'View options assigned to your account.',
							   'url'=>'home-user'],
				  ];
				  
$arr_forms =    [
						'applicant' =>['icon'=>'fa fa-fw fa-user',
						                    'title'=>'add new applicant',
						                    'intro-text'=>'add a new applicant',
						                    'hidden'=>false,
						                    'url'=>'applicant-new'],
						                    
						'loan' =>['icon'=>'fa fa-fw fa-table',
						                    'title'=>'add new loan',
						                    'intro-text'=>'add a new loan',
						                    'hidden'=>true,
						                    'url'=>'loan-new'],	

						'loan repayment new' =>['icon'=>'fa fa-fw fa-usd',
						                    'title'=>'add new loan payment',
						                    'intro-text'=>'add a new loan payment',
						                    'hidden'=>true,
						                    'url'=>'loan-repayment-new'],	

						'loan repayment edit' =>['icon'=>'fa fa-fw fa-edit',
						                    'title'=>'add new loan payment',
						                    'intro-text'=>'edit a loan payment',
						                    'hidden'=>true,
						                    'url'=>'loan-repayment-edit'],
						                    
						'file' =>['icon'=>'fa fa-fw fa-file-o',
						                    'title'=>'add new loan',
						                    'intro-text'=>'add a new file',
						                    'hidden'=>true,
						                    'url'=>'file-new'],

    					'User' =>['icon'=>'fa fa-fw fa-user',
						                    'title'=>'add new user',
						                    'intro-text'=>'Add a new user',
						                    'hidden'=>true,
						                    'url'=>'new-user']	
					];


/* manage these fields */
$arr_manage_agents =    [								   				

							'Applicants'  =>['icon'=>'fa fa-fw fa-users',
										   'title'=>'manage applicants',
										   'intro-text'=>'manage applicants on the system.',
										   'url'=>'manage-applicants'],	

							'Applicant Details'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'applicant details',
										   'intro-text'=>'edit applicant details.',
										   'hidden'=>true,
										   'url'=>'applicant-details'],	
										   
							'Edit Applicant'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'Edit applicant',
										   'intro-text'=>'Edit an applicant.',
										   'hidden'=>true,
										   'url'=>'applicant-edit'],	
										   				   
							'Loans'  =>['icon'=>'fa fa-fw fa-usd',
										   'title'=>'manage loans',
										   'divider-top'=>true,
										   'intro-text'=>'manage loans.',
										   'url'=>'manage-loans'],	
										   
							'Loan Summary'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'loan summary',
										   'intro-text'=>'view loan details.',
										   'hidden'=>true,
										   'url'=>'loan-summary'],	
										   
							/*'Edit Loan'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'edit loan',
										   'intro-text'=>'edit a loan.',
										   'hidden'=>true,
										   'url'=>'loan-edit'],*/

							'Loan Details'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'edit loan details',
										   'intro-text'=>'edit the details of a loan.',
										   'hidden'=>true,
										   'url'=>'loan-details'],
										   
						'edit file' =>['icon'=>'fa fa-fw fa-file-o',
						                    'title'=>'edit file',
						                    'intro-text'=>'edit a file',
						                    'hidden'=>true,
						                    'url'=>'file-edit'],										   
						                    
                    ];
                    
/*
https://mani.loans/?view=loan-communication-note-new&id=5&personid=1
*/                    
$arr_manage_debtcollectors =    [			

							'Debt Collections'  =>['icon'=>'fa fa-fw fa-exclamation-triangle',
										   'title'=>'Debt Collections',
										   'intro-text'=>'manage loans under debt collection.',
										   'url'=>'manage-debt-collections'],
										   
							'Applicants'  =>['icon'=>'fa fa-fw fa-users',
										   'title'=>'manage applicants',
										   'intro-text'=>'manage applicants on the system.',
										   'hidden'=>true,
										   'url'=>'manage-applicants'],	
										   
    							'Applicant Details'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'applicant details',
										   'intro-text'=>'edit applicant details.',
										   'hidden'=>true,
										   'url'=>'applicant-details'],	
										   
							'Loans'  =>['icon'=>'fa fa-fw fa-usd',
										   'title'=>'manage loans',
										   'divider-top'=>true,
										   'intro-text'=>'manage loans.',
										   'url'=>'manage-loans'],	
										   
							'Loan Details'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'view loan details',
										   'intro-text'=>'view loan details.',
										   'hidden'=>true,
										   'url'=>'loan-details'],	
										   
							'Loan Summary'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'loan summary',
										   'intro-text'=>'view loan details.',
										   'hidden'=>true,
										   'url'=>'loan-summary'],	
										   
    						'loan repayment new' =>['icon'=>'fa fa-fw fa-usd',
    						                    'title'=>'add new loan payment',
    						                    'intro-text'=>'add a new loan payment',
    						                    'hidden'=>true,
    						                    'url'=>'loan-repayment-new'],	
    						                    
    						'loan repayment edit' =>['icon'=>'fa fa-fw fa-edit',
    						                    'title'=>'add new loan payment',
    						                    'intro-text'=>'edit a loan payment',
    						                    'hidden'=>true,
    						                    'url'=>'loan-repayment-edit'],
    						                    
						    'Loan Comm Note New' =>['icon'=>'fa fa-fw fa-plus',
    						                    'title'=>'add loan note',
    						                    'intro-text'=>'add a new note',
    						                    'hidden'=>true,
    						                    'url'=>'loan-communication-note-new'],	
    						                    
    						'loan Comm SMS New' =>['icon'=>'fa fa-fw fa-plis',
    						                    'title'=>'send loan SMS',
    						                    'intro-text'=>'send SMS to loan holder',
    						                    'hidden'=>true,
    						                    'url'=>'loan-communication-sms-new'],	    						                    
						                    
                    ];
                    
$arr_manage_manager =    [								   				

							'Applicants'  =>['icon'=>'fa fa-fw fa-users',
										   'title'=>'manage applicants',
										   'intro-text'=>'manage applicants.',
										   'url'=>'manage-applicants'],	

							'Applicant Details'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'view applicant details',
										   'intro-text'=>'view applicant details.',
										   'hidden'=>true,
										   'url'=>'applicant-details'],										   
							'Edit Applicant'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'edit an applicant',
										   'intro-text'=>'edit an applicant.',
										   'hidden'=>true,
										   'url'=>'applicant-edit'],	
										   

        					'New User' =>['icon'=>'fa fa-fw fa-user',
    						                    'title'=>'add new user',
    						                    'intro-text'=>'Add a new user',
    						                    'hidden'=>true,
    						                    'url'=>'new-user'],										   
										   				   
							'Loans'  =>['icon'=>'fa fa-fw fa-usd',
										   'title'=>'manage loans',
										   'divider-top'=>true,
										   'intro-text'=>'manage loans.',
										   'url'=>'manage-loans'],	
										   
							'Debt Collections'  =>['icon'=>'fa fa-fw fa-exclamation-triangle',
										   'title'=>'Debt Collections',
										   'intro-text'=>'manage loans under debt collection.',
										   'url'=>'manage-debt-collections'],
										   
										   										   
							'Edit Loan'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'edit a loan',
										   'intro-text'=>'edit a loan.',
										   'hidden'=>true,
										   'url'=>'loan-edit'],

							'Loan Details'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'view loan details',
										   'intro-text'=>'view loan details.',
										   'hidden'=>true,
										   'url'=>'loan-details'],	
										   
							'Loan Status'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'change status of loan',
										   'intro-text'=>'change status of loan.',
										   'hidden'=>true,
										   'url'=>'loan-change-status'],	
										   
							'Loan Summary'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'loan summary',
										   'intro-text'=>'view loan details.',
										   'hidden'=>true,
										   'url'=>'loan-summary'],											   
										   
						'loan repayment edit' =>['icon'=>'fa fa-fw fa-edit',
						                    'title'=>'add new loan payment',
						                    'intro-text'=>'edit a loan payment',
						                    'hidden'=>true,
						                    'url'=>'loan-repayment-edit'],
						                    
						'edit file' =>['icon'=>'fa fa-fw fa-file-o',
						                    'title'=>'add new loan',
						                    'intro-text'=>'add a new loan',
						                    'hidden'=>true,
						                    'url'=>'file-edit'],
						                    
							/*
							'Scheduled SMS'  =>['icon'=>'fa fa-fw fa-clock-o',
										   'title'=>'manage scheduled sms',
										   'divider-top'=>true,
										   'intro-text'=>'view list of scheduled sms.',
										   'url'=>'manage-sms-scheduled'],	
                            */
							'Sent SMS'  =>['icon'=>'fa fa-fw fa-file-o',
										   'title'=>'manage sent sms',
										   'intro-text'=>'manage sent sms.',
										   'divider-top'=>true,
										   'url'=>'manage-sms-sent'],
										   
						    'Loan Comm Note New' =>['icon'=>'fa fa-fw fa-plus',
    						                    'title'=>'add loan note',
    						                    'intro-text'=>'add a new note',
    						                    'hidden'=>true,
    						                    'url'=>'loan-communication-note-new'],	
    						                    
    						'loan Comm SMS New' =>['icon'=>'fa fa-fw fa-plis',
    						                    'title'=>'send loan SMS',
    						                    'intro-text'=>'send SMS to loan holder',
    						                    'hidden'=>true,
    						                    'url'=>'loan-communication-sms-new'],	
    						                    
										   
                             'users'=>['icon'=>'fa fa-fw fa-users','divider-top'=>true, 'title'=>'manage users','intro-text'=>'Add, edit or delete users and user groups.','url'=>'users'],
 		                       'Edit User' =>['icon'=>'fa fa-fw fa-user',
                		                    'title'=>'edit a user',
                		                    'intro-text'=>'Edit a user',
                		                    'hidden'=>true,
                		                    'url'=>'edit-user'],    

                            'Edit User Password'=>['icon'=>'fa fa-fw fa-edit', 
                                              'title'=>'Update password', 
                                              'intro-text'=>'update password.',
                                              'url'=>'user-update-password', 
                                              'hidden'=>true],	
                                              
							'Delete Handler'  =>['icon'=>'fa fa-fw fa-trash',
										   'title'=>'Delete',
										   'intro-text'=>'Delete any.',
										   'hidden'=>true,
										   'url'=>'delete'],                                              
                    ];
                    
$arr_manage =    [							
    
						    'Loan Comm Note New' =>['icon'=>'fa fa-fw fa-plus',
    						                    'title'=>'add loan note',
    						                    'intro-text'=>'add a new note',
    						                    'hidden'=>true,
    						                    'url'=>'loan-communication-note-new'],	
    						                    
    						'loan Comm SMS New' =>['icon'=>'fa fa-fw fa-plis',
    						                    'title'=>'send loan SMS',
    						                    'intro-text'=>'send SMS to loan holder',
    						                    'hidden'=>true,
    						                    'url'=>'loan-communication-sms-new'],	    

							'Applicants'  =>['icon'=>'fa fa-fw fa-users',
										   'title'=>'manage applicants',
										   'intro-text'=>'manage applicants.',
										   'url'=>'manage-applicants'],	

							'Applicant Details'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'view applicant details',
										   'intro-text'=>'view applicant details.',
										   'hidden'=>true,
										   'url'=>'applicant-details'],										   
							'Edit Applicant'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'edit an applicant',
										   'intro-text'=>'edit an applicant.',
										   'hidden'=>true,
										   'url'=>'applicant-edit'],	
										   				   
							'Loans'  =>['icon'=>'fa fa-fw fa-usd',
										   'title'=>'manage loans',
										   'divider-top'=>true,
										   'intro-text'=>'manage loans.',
										   'url'=>'manage-loans'],
										   
							'Debt Collections'  =>['icon'=>'fa fa-fw fa-exclamation-triangle',
										   'title'=>'Debt Collections',
										   'intro-text'=>'manage loans under debt collection.',
										   'url'=>'manage-debt-collections'],
										   										   
							'Edit Loan'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'edit a loan',
										   'intro-text'=>'edit a loan.',
										   'hidden'=>true,
										   'url'=>'loan-edit'],

							'Loan Details'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'loan details',
										   'intro-text'=>'view loan details.',
										   'hidden'=>true,
										   'url'=>'loan-details'],	
										   
							'Loan Status'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'loan status',
										   'intro-text'=>'change status of loan.',
										   'hidden'=>true,
										   'url'=>'loan-change-status'],	
										   
							'Loan Summary'  =>['icon'=>'fa fa-fw fa-edit',
										   'title'=>'loan summary',
										   'intro-text'=>'view loan details.',
										   'hidden'=>true,
										   'url'=>'loan-summary'],	
										   
							'Unpaid Loans'  =>['icon'=>'fa fa-fw fa-exclamation-triangle',
										   'title'=>'unpaid loans',
										   'intro-text'=>'view loans that are not paid up.',
										   'url'=>'manage-loans-unpaid'],											   
										   
						    'edit file' =>['icon'=>'fa fa-fw fa-file-o',
						                    'title'=>'edit file',
						                    'intro-text'=>'edit loan holder file',
						                    'hidden'=>true,
						                    'url'=>'file-edit'],
						                    
							/*'Scheduled SMS'  =>['icon'=>'fa fa-fw fa-clock-o',
										   'title'=>'manage scheduled sms',
										   'divider-top'=>true,
										   'intro-text'=>'view list of scheduled sms.',
										   'url'=>'manage-sms-scheduled'],	
							*/			   
							'Sent SMS'  =>['icon'=>'fa fa-fw fa-paper-plane',
										   'title'=>'sent sms',
										   'divider-top'=>true,
										   'intro-text'=>'view list of sent sms.',
										   'url'=>'manage-sms-sent'],	
										   
                            'Edit User Password'=>['icon'=>'fa fa-fw fa-edit', 
                                              'title'=>'Update password', 
                                              'intro-text'=>'update password.',
                                              'url'=>'user-update-password', 
                                              'hidden'=>true],											   
                                              
							'Delete Handler'  =>['icon'=>'fa fa-fw fa-trash',
										   'title'=>'Delete',
										   'intro-text'=>'Delete any.',
										   'hidden'=>true,
										   'url'=>'delete'],
                            /*
							'Sent SMS'  =>['icon'=>'fa fa-fw fa-file-o',
										   'title'=>'manage sent sms',
										   'intro-text'=>'manage sent sms.',
										   'url'=>'manage-sms-sent']*/
                    ];

/* notify */
$arr_notify =    [
					/*'Inbox'              =>['icon'=>'fa fa-fw fa-envelope','title'=>'send message','intro-text'=>'Send internal message','url'=>'notify-inbox'],*/
					'SMS'                =>['icon'=>'fa fa-fw fa-mobile','title'=>'send sms','intro-text'=>'Send sms.','url'=>'notify-sms']/*,
					'Push Notifications' =>['icon'=>'fa fa-fw fa-external-link','title'=>'send push notification','intro-text'=>'Send push notification.', 'url'=>'notify-push']*/                              
];

/* profile management */
$arr_profile = [		 						

                  'My profile'=>['icon'=>'fa fa-fw fa-user', 'title'=>'update your account', 'intro-text'=>'Change your login information and personal details.','url'=>'profile'],
                  
  				 'home'=>['icon'=>'fa fa-fw fa-home', 
							   'title'=>'View your options', 
							   'intro-text'=>'View options assigned to your account.',
							   'url'=>'home-user'],
							                     
                    'Edit Password'=>['icon'=>'fa fa-fw fa-edit', 'title'=>'Update password', 'intro-text'=>'update password.','url'=>'profile-update-password', 'hidden'=>true],                  
                  /*'Added by me'=>['icon'=>'fa fa-fw fa-files-o', 'title'=>'documents added by you', 'intro-text'=>'Show documents added by me.','url'=>'my-documents'],*/
                  'assistance'=>['icon'=>'fa fa-fw fa-life-ring','divider-top'=>true, 'title'=>'start the system help wizard','intro-text'=>'Start the help wizard to guide you on how to use the system.','url'=>'help-wizard'],
                  'system info'=>['icon'=>'fa fa-fw fa-info-circle','divider-top'=>true,'title'=>'system info','intro-text'=>'Show system development details.','url'=>'system-about'],
                  /*'user manual'=>['icon'=>'fa fa-fw fa-book', 'title'=>'User manual', 'intro-text'=>'Open the system user manual.','url'=>'user-manual'], */
                  'sign out'=>['icon'=>'fa fa-fw fa-sign-out', 'title'=>'log out of your account', 'divider-top'=>true, 'intro-text'=>'Log out of your account and view the login page.','url'=>'logout']
				  ];

/* dashboard */
$arr_dashboard =    [

          					 'dashboard'=>['icon'=>'fa fa-fw fa-dashboard', 
										   'title'=>'View your dashboard', 
										   'intro-text'=>'View a breakdown of your data by using charts and graphs.',
										   'url'=>'dashboard']
					     ];
					     
$arr_dashboard_agent =    [
    
          					 'home'=>['icon'=>'fa fa-fw fa-home', 
										   'title'=>'View your options', 
										   'intro-text'=>'View options assigned to your account.',
										   'url'=>'home-user'],
										   
              				 'Dashboard'=>['icon'=>'fa fa-fw fa-dashboard', 
            							   'title'=>'dashboard', 
            							   'intro-text'=>'View your dashboard.',
            							   'url'=>'dashboard-agent'],
					     ];

$arr_search = [	
		'Search Applicants'  =>['icon'=>'fa fa-fw fa-search',
									'title'=>'search',
									'intro-text'=>'search for applicant',
									'url'=>'search']
					];
/* settings */
$arr_settings = [	
    
										   
							// settings 												   										   
		'Settings'  =>['icon'=>'fa fa-fw fa-cog',
									'title'=>'manage settings',
									'intro-text'=>'manage settings.',
									'divider-bottom'=>true,
									'url'=>'manage-settings'],

						                    
		'Setting' =>['icon'=>'fa fa-fw fa-cog',
		                    'title'=>'add new setting',
		                    'intro-text'=>'Add a new setting',
		                    'hidden'=>true,
		                    'url'=>'new-setting'],

 		'Edit Setting' =>['icon'=>'fa fa-fw fa-wrench',
		                  'title'=>'edit a setting',
		                  'intro-text'=>'Edit a setting',
		                  'hidden'=>true,
		                  'url'=>'edit-setting'],
									
		// users																   
 		'Edit User' =>['icon'=>'fa fa-fw fa-user',
		                    'title'=>'edit a user',
		                    'intro-text'=>'Edit a user',
		                    'hidden'=>true,
		                    'url'=>'edit-user'],

 		'Edit Sent SMS' =>['icon'=>'fa fa-fw fa-edit',
		                    'title'=>'edit a sent SMS',
		                    'intro-text'=>'Edit a sent SMS',
		                    'hidden'=>true,
		                    'url'=>'edit-sms-sent'],
		                    							                    
 		'Edit Scheduled SMS' =>['icon'=>'fa fa-fw fa-edit',
		                    'title'=>'edit a scheduled SMS',
		                    'intro-text'=>'Edit a scheduled SMS',
		                    'hidden'=>true,
		                    'url'=>'edit-sms-scheduled'],
		                    
                 'query builder'=>['icon'=>'fa fa-fw fa-filter','title'=>'manage database queries',
                                    'intro-text'=>'Add or edit SQL queries against the database.',
                                    'url'=>'queries'],
                 
                 'database'=>['icon'=>'fa fa-fw fa-database','title'=>'manage database', 'intro-text'=>'Manage the database.','url'=>'database'],
				 		        'table size'=>['icon'=>'fa fa-fw fa-table','title'=>'view tables', 'intro-text'=>'View tables.','url'=>'table-size', 'divider-bottom'=>true],
                 'system'=>['icon'=>'fa fa-fw fa-wrench','title'=>'manage system settings', 'intro-text'=>'Manage other aspects of the system.','url'=>'system'],
                            'Pre-flight'=>['icon'=>'fa fa-fw fa-plane','title'=>'Pre-flight checks', 'intro-text'=>'Pre-flight checks.','url'=>'system-test'],
                 'users'=>['icon'=>'fa fa-fw fa-users','divider-top'=>true, 'title'=>'manage users and user groups','intro-text'=>'Add, edit or delete users and user groups.','url'=>'users'],
                 'debug'=>['icon'=>'fa fa-fw fa-paper-plane','divider-top'=>true, 'title'=>'manage users and user groups','intro-text'=>'Add, edit or delete users and user groups.','url'=>'debug-send-sms']  
                ];
                
/* searching */
$arr_filter = ['search'=>['icon'=>'fa fa-fw fa-search', 'divider-bottom'=>true, 'title'=>'search for applications', 'intro-text'=>'Search the database for applications','url'=>'search'],
               'queries view'=>['icon'=>'fa fa-fw fa-filter','title'=>'run database queries','intro-text'=>'Run saved SQL queries against the database.','url'=>'queries-view'],
               /*'Recently added'=>['icon'=>'fa fa-fw fa-plus','divider-top'=>true,'title'=>'Recently added', 'intro-text'=>'Recently added.','url'=>'filter-recently-added'],*/
 			   'Total by region'=>['icon'=>'fa fa-fw fa-map-marker','title'=>'Total by region', 'intro-text'=>'Total by region.','url'=>'filter-total-by-region'],
 			   /*'Total employed by company'=>['icon'=>'fa fa-fw fa-wrench', 'title'=>'Total employed by company', 'intro-text'=>'Total employed by company.','url'=>'filter-total-employed-by-company'],
 			   'With invalid ID number'=>['icon'=>'fa fa-fw fa-exclamation', 'title'=>'With invalid ID number', 'intro-text'=>'With invalid ID number.','url'=>'filter-invalid-idnumber'], 
 			   'Specially abled'=>['icon'=>'fa fa-fw fa-wheelchair', 'title'=>'Specially abled', 'intro-text'=>'Specially abled.','url'=>'filter-specially-abled']*/
              ];

/* searching */
$arr_services = [
                 'Services Handler'=>['icon'=>'fa fa-fw fa-cog','title'=>'services handler','intro-text'=>'services handler','url'=>'services-handler']									   
              ];
              
/* reporting */
$arr_reports = [
                 'dynamic Report'=>['icon'=>'fa fa-fw fa-bar-chart', 'title'=>'view application statistics', 'intro-text'=>'View applications statistics','url'=>'report-dynamic'],    
                 'SMS Sent'=>['icon'=>'fa fa-fw fa-paper-plane', 'title'=>'view application statistics', 'intro-text'=>'View applications statistics', 'divider-top'=>true, 'url'=>'report-sms-sent'],
                                 
                                 /*
                 'sent by category per number'=>['icon'=>'fa fa-fw fa-mobile', 'title'=>'view application statistics',
                                 'intro-text'=>'View applications statistics', 'url'=>'report-sent-numbers'], 
                 'total by numbers'=>['icon'=>'fa fa-fw fa-mobile', 'title'=>'view application statistics',
                                 'intro-text'=>'View applications statistics', 'url'=>'report-total-by-number'],                                 
                 'statistics for specific number'=>['icon'=>'fa fa-fw fa-filter', 'title'=>'view application statistics',
                 						'divider-top'=>true,
                                 'intro-text'=>'View applications statistics', 'url'=>'report-stats-number'], 
                                 */
                ];

/* development */
$arr_reportbug =    [
							 'report a bug'=>['icon'=>'fa fa-fw fa-edit', 'title'=>'Report a bug', 'intro-text'=>'Report a system malfunction, suggest a feature.','url'=>'bug-reporter'],					 
							 /*'dump-schema'=>['icon'=>'fa fa-fw fa-database', 'title'=>'Dump database schema',
											 'intro-text'=>'Dump the database schema'],
							 'db-backup'=>['icon'=>'fa fa-fw fa-database', 'title'=>'Backup database',
											 'intro-text'=>'Backup the database']	*/								 
							];			

/* backup ops */
$arr_tasks_backup = [
		                 'database'=>['icon'=>'fa fa-fw fa-database','title'=>'backup database', 'intro-text'=>'Backup the database.','url'=>'database', 'url'=>'database']
		                ];
					
// rights for users
$role_rights = [
		         'administrators' => [
            				'dashboard'=>['icon'=>'fa fa-fw fa-dashboard', 'menu'=>$arr_dashboard],					
            				'new'=>['icon'=>'fa fa-fw fa-plus', 'menu'=>$arr_forms],  
            				'manage'=>['icon'=>'fa fa-fw fa-edit', 'menu'=>$arr_manage],
            				'search'=>['icon'=>'fa fa-fw fa-search', 'menu'=>$arr_search],
            				'reports'=>['icon'=>'fa fa-fw fa-bar-chart', 'menu'=>$arr_reports],
            				'settings'=>['icon'=>'fa fa-fw fa-wrench', 'menu'=>$arr_settings],									
            				/*'system bugs'=>['icon'=>'fa fa-fw fa-bug', 'menu'=>$arr_reportbug],*/
            				'profile'=>['icon'=>'fa fa-fw fa-user', 'menu'=>$arr_profile]
                         ],

		         'managers' => [						
								'dashboard'=>['icon'=>'fa fa-fw fa-dashboard', 'menu'=>$arr_dashboard],	
								'new'=>['icon'=>'fa fa-fw fa-plus', 'menu'=>$arr_forms],  
            				    'manage'=>['icon'=>'fa fa-fw fa-edit', 'menu'=>$arr_manage_manager],
            				    'search'=>['icon'=>'fa fa-fw fa-search', 'menu'=>$arr_search],
								'reports'=>['icon'=>'fa fa-fw fa-bar-chart', 'menu'=>$arr_reports],
								'profile'=>['icon'=>'fa fa-fw fa-user', 'menu'=>$arr_profile]
                             ],
		                             
		         'agents' => [		
								'optins'=>['icon'=>'fa fa-fw fa-cog', 'menu'=>$arr_dashboard_agent],		             
								'new'=>['icon'=>'fa fa-fw fa-plus', 'menu'=>$arr_forms],  
            				    'manage'=>['icon'=>'fa fa-fw fa-edit', 'menu'=>$arr_manage_agents],
            				    'search'=>['icon'=>'fa fa-fw fa-search', 'menu'=>$arr_search],
							/*'reports'=>['icon'=>'fa fa-fw fa-bar-chart', 'menu'=>$arr_reports],*/
								'profile'=>['icon'=>'fa fa-fw fa-user', 'menu'=>$arr_profile]
                             ],
                             
		         'debt_collectors' => [						
            				    'manage'=>['icon'=>'fa fa-fw fa-edit', 'menu'=>$arr_manage_debtcollectors],
            				    'search'=>['icon'=>'fa fa-fw fa-search', 'menu'=>$arr_search],
								'profile'=>['icon'=>'fa fa-fw fa-user', 'menu'=>$arr_profile]
                             ],                             

              ];

?>
