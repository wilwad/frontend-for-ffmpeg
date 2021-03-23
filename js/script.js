 /*
  * Ministry of Home Affairs and Immigration Citizenship Registration System
  * 
  * Centaur Investments
  * William Sengdara
  * Copyright (c) 2015
  */
// javascript for document events

var funcSuccess = function(a) {
					  console.log('result follows');
					  console.log(a);
					  window.location.reload();   
					};
var funcError = function (a,b,c) {
	               console.log(a,b,c);
				  };
	
function getAjax(){
		if (window.XMLHttpRequest){
		  // code for IE7+, Firefox, Chrome, Opera, Safari
		  return new XMLHttpRequest();
		}else{
		  // code for IE6, IE5
		   return new ActiveXObject("Microsoft.XMLHTTP");
		}
 }
    					  
$(document).ready(function(){
   /* when you click forgot login */ 
   $('#btn-forgotlogin').click(function(){ 
		BootstrapDialog.alert({title: 'Forgot login',
		                       message: 'Please contact the system administrator to reset your password.'});
   });
   
	$('.fancybox').fancybox({
		  beforeLoad: function(){
		  						this.title = $(this.element).attr('data-title');
		  				  },
		  'overlayShow':true,
		  'hideOnContentClick':false,
		  iframe: {preload:false, afterLoad: function () {console.log('iframe afteload');}},
		  afterLoad: function () {
		  	console.log('fancybox event afterLoad fired');
		  	/*$('.fancybox-skin').css('padding',0).css('background-color','transparent');*/
		  }
	});
	
   console.log('fancybox inited');
   $('.timeago').timeago()
   
   /* when you click login */
   $('#btn-login').click(function(){
	   $u = $('#user_name');
	   $p = $('#user_password');
	   
	   $u.val($u.val().trim());
	   $p.val($p.val().trim());
	   
	   if (!$u.val().length)
	   {
		   $u.focus();
		   return false;
	   }
	   
	   if (!$p.val().length)
	   {
		   $p.focus();
		   return false;
	   }	

	   disable( [$('#user_name'), 
	             $('#user_password'), 
				 $('#btn-login'),$('#btn-forgotlogin')], true);	
	   
	   $('#btn-login').html("<li class='fa fa-fw fa-spinner fa-spin'></li>&nbsp;Logging in");

	   payload = {'view':'authenticate', 
	              'user_name': $u.val(), 
				     'user_password': $p.val()	
				    };
	   console.log(payload);
	   
	   var funcSuccess = function(a) {
							  console.log('result follows');
							  console.log(a);
							  
							  if (a.result == true || a.status.toLowerCase() == 'you are already logged in.')
							  {
							  		window.location.reload();
							  }   
							  else
							  {
								  $('#loginMsg').removeClass('hide').html(a.html);
								  $('#btn-login').html("Login");
								  disable( [$('#user_name'), $('#user_password'), $('#btn-login'),$('#btn-forgotlogin')], false);	
							  }
							};
	   var funcError = function (a,b,c) {
	   					console.log('Extra dump',a,b,c);

		   							  var html = "<div class='alert alert-danger'><span><li class='fa fa-fw fa-exclamation-circle'></li>Error: " + b +"</span></div>";									
									  var html = "<div class='alert alert-danger'><span><li class='fa fa-fw fa-exclamation-circle'></li>Login failed: check username/password or contact administrator.</span></div>";									
									  $('#loginMsg').removeClass('hide').html(html);		
									  $('#btn-login').html("<li class='fa fa-fw fa-key'></li>&nbsp;Login");
									  disable( [$('#user_name'), $('#user_password'), $('#btn-login'),$('#btn-forgotlogin')], false);	
						  };
	
      // call authenticate	
	  ajax('ui.php', 
	       'post',
	       'json', 
		   payload, 
		   funcSuccess,
		   funcError);
	});
				
	// theme tooltips			
	$('[data-toggle=\"tooltip\"]').tooltip();	
	
	// margin-top: 65px !important; /* 25px for container, 65px for container-fluid */
	// adjust body padding-top
	/*
	if ($('.container-fluid').length)
		$('body').css('margin-top','65px');
	else
		$('body').css('margin-top','25px');
		*/
});

/*
 * This will show a variety of dialogs
 */
function show_dialog(context, userid) {
	var dialogcss = '';
	
	switch (context) {
		case 'users-add':
			  dialogcss = "<div class='table-responsive'> \
							<table class='table table-no-border'> \
							 <tbody> \
							  <tr><th>Username <span class='required'>*</span></th><td><input type='text' value='' class='form-control' placeholder='username' id='username'></td></tr> \
							  <tr><th>Password <span class='required'>*</span></th><td><input type='password' value='' class='form-control' placeholder='password' id='password'></td></tr> \
							  <tr><th>Confirm <span class='required'>*</span></th><td><input type='password' value='' class='form-control' placeholder='password2' id='password2'></td></tr> \
							  <tr><th>First name <span class='required'>*</span></th><td><input type='text' value='' class='form-control' placeholder='first name' id='fname'></td></tr> \
							  <tr><th>Surname <span class='required'>*</span></th><td><input type='text' value='' class='form-control' placeholder='surname' id='sname'></td></tr> \
							  <tr><th>Is active <span class='required'>*</span></th><td><input type='checkbox' checked class='' placeholder='isactive' id='isactive'></td></tr> \
							  <tr><th>Role <span class='required'>*</span></th><td><select class='form-control' id='role'><option value='admins'>Admins</option><option value='toplevels'>Top Levels</option><option value='dataentry'>Data Entry</option></select></td></tr> \
							 </tbody> \
							</table></div> \
							<style>";
			  
				BootstrapDialog.show({
					title: 'Add a new user',
					message: function(dialog) {
						var content = $(dialogcss);
						return content;
					},
					buttons: [
						{
						label: 'Create user',
						action: function(){
							var PASSWORD_MAX = 7;
							
							$('#username').focus();
							
							var controls = ['#username', '#password','#password2', '#fname', '#sname','#role'];
							for (i=0; i < controls.length; i++)
							{
								console.log('reading ' + controls[i] + '...');
								
								var ctl = $(controls[i]);
								ctl.val(ctl.val().trim());
								if (ctl.val().length == 0)
								{
									alertify.error('Please specify ' + controls[i].split('#')[1]);
									ctl.focus();
									return false;
								}
								
								// passwords length
								if (controls[i] == '#password')
								{
									if ($('#password').val().length < PASSWORD_MAX ){
										alertify.error('Password cannot be less than ' + PASSWORD_MAX + 'characters');
										ctl.focus();
										return false;
									}
								}
								
								// passwords must match
								if (controls[i] == '#password2')
								{
									if ($('#password').val() != $('#password2').val()){
										alertify.error('Passwords do not match.');
										ctl.focus();
										return false;
									}
								}
							}
							
						   var funcSuccess = function(data) {
												  console.log('result follows');
												  console.log(data);
												  alertify.success(data);
												  window.setTimeOut(window.location.reload(),1000);
												};
						   var funcError = function (a,b,c) {
												alertify.error(b + ' ' + c);
											  };
						  
						  var payload = {'view': 'users-add', 
										 'userid': userid,
						                 'username':$('#username').val(),
										 'password': $('#password').val(),
										 'fname': $('#fname').val(),
										 'sname': $('#sname').val(),
										 'isactive':$('#isactive').prop('checked')?1:0,
										 'role':$('#role').val()
										 };
										 
						  ajax('api/api.php', 
							   'post',
							   'text', 
							   payload, 
							   funcSuccess,
							   funcError);
						}},					
						{
						label: 'Cancel',
						action: function(dialogItself){
							dialogItself.close();
						}
					}]
				});					
			break;
			
		case 'discussionadd':
			  dialogcss = "<div class='table-responsive'> \
							<table class='table table-no-border'> \
							 <tbody> \
							  <tr><th>Subject <span class='required'>*</span></th><td><input type='text' value='' class='form-control' placeholder='subject' id='subject'></td></tr> \
							  <tr><th>Body <span class='required'>*</span></th><td><textarea class='form-control' id='body'></textarea></td></tr> \
							 </tbody> \
							</table></div> \
							<style>th {text-align:right !important;}</style>";
			  
				BootstrapDialog.show({
					title: 'Add a new discussion post',
					message: function(dialog) {
						var $content = $(dialogcss);
						
						/*
						var $footerButton = dialog.getButton('btn-1');
						// {$footerButton: $footerButton}
						$content.find('button').click({$footerButton: $footerButton}, function(event) {
							event.data.$footerButton.enable();
							event.data.$footerButton.stopSpin();
							dialog.setClosable(true);
						});
						*/
						return $content;
					},
					buttons: [
						{
						label: 'Create',
						action: function(){
							$subject = $('#subject');
							$body = $('#body');
							
							$subject.val($subject.val().trim());
							$body.val($body.val().trim());
							
							if (!$subject.val().length)
							{
								$subject.focus();
								return false;
							}
							
							if (!$body.val().length)
							{
								$body.focus();
								return false;
							}	

						   var funcSuccess = function(a) {
												  console.log('result follows');
												  console.log(a);
												  
												  if (a.result == true || a.status.toLowerCase() == 'you are already logged in.')
													  window.location.reload();   
												  else
												  {
													  $('#loginMsg').removeClass('hide').html(a.html);
													  $('#btn-login').html("Login");
													  disable( [$('#username'), $('#password'), $('#btn-login'),$('#btn-forgotlogin')], true);	
												  }
												};
						   var funcError = function (a,b,c) {
														  var html = "<div class='alert alert-danger'><span><li class='fa fa-fw fa-exclamation-circle'></li>Error: " + b +"</span></div>";									
														  var html = "<div class='alert alert-danger'><span><li class='fa fa-fw fa-exclamation-circle'></li>Login failed: check username/password or contact administrator.</span></div>";									
														  $('#loginMsg').removeClass('hide').html(html);		
														  $('#btn-login').html("Login");
														  disable( [$('#username'), $('#password'), $('#btn-login'),$('#btn-forgotlogin')], false);	
											  };
						  
						  var payload = {'view': 'discussionpost-add', 
										 'userid': userid,
						                 'subject':$subject.val(),
										 'body':$body.val()};
						  ajax('ui.php', 
							   'post',
							   'json', 
							   payload, 
							   funcSuccess,
							   funcError);
						}},					
						{
						label: 'Cancel',
						action: function(dialogItself){
							dialogItself.close();
						}
					}]
				});					
			break;
			
		default:
			console.log('unhandled show_dialog() context: ' + context);
			break
	}
}

/*
 * this will ask the user if he wants to delete an item
 */
function confirmdelete(table, id) {
		payload = {'view': 'deleteitem', 
		           'table':table,
				   'id':id};
		funcSuccess = function(a) { window.location.reload(); };
		funcError = function(a,b,c) { alert(b); };
		
		BootstrapDialog.show({
					title: 'Confirm delete item',
					message: 'Are you sure you would like to delete the selected item?',
					buttons: [{
						label: "<li class='fa fa-fw fa-trash'></li>&nbsp;Delete",
						action: function(dialogItself){
							  // call authenticate	
							  ajax('ui.php', 
								   'post',
								   'json', 
								   payload, 
								   funcSuccess,
								   funcError);
						}						
					},
					{
						label: 'Cancel',
						action: function(dialogItself){
							dialogItself.close();
						}
					}]
				});		
}

/*
 * this will ask the user if he wants to log out
 */
function confirmlogout(url) {
		BootstrapDialog.show({
					title: 'Confirm sign out',
					message: 'Are you sure you would like to sign out?',
					buttons: [{
						label: 'Sign out',
						action: function(dialogItself){
							window.location.href = 'logout.php';
						}						
					},
					{
						label: 'Cancel',
						action: function(dialogItself){
							dialogItself.close();
						}
					}]
				});		
}

/* 
 * ajax func
 */
function ajax(url, method, datatype, payload, funcSuccess, funcError) {
	  $.ajax({url: url, 
			  method: method,
			  dataType: datatype,
			  data: payload,
			  success: funcSuccess,
			  error: funcError					
			});	
}

/* this will enable or disable all controls in array
 */
function  disable(objs, newstate) {
	for(var i=0; i < objs.length; i++)
		objs[i].prop('disabled',newstate);
		
}

// shortened ajax
function ajax_proxy(url, method, options, func_success) {
					$.ajax({type: method,
										url: url,
										data: options,
										error: function(xhr, ajaxOptions, thrownError){
											console.log(thrownError);
										},
										success: func_success
									});		
}												

/* asks to delete data */
function delete_row(table, fldname,id) {
		BootstrapDialog.show({
					title: 'Confirm delete',
					message: 'Delete row with ' + fldname + ' matching ' + id + ' from ' + table + '?',
					buttons: [{
						label: 'Delete',
						action: function(dialogItself){
							var url = "api/api.php";
							var method = "POST";
							options = { 'view': 'delete-' + table, 
							            'field': fldname, 
										'id': id};
							ajax_proxy(url, method, options, funcSuccess);
						}						
					},
					{
						label: 'Cancel',
						action: function(dialogItself){
							dialogItself.close();
						}
					}]
				});	
}