var Main = {
	defaults : {
		status : "What's new?",
		reply : "Reply to this post...",
		link : "http://www.google.com/",
		code : "<?php\necho 'This website is written in PHP!';\n?>",
		custom_name : "Enter a custom name",
		friends : "Enter specific friends here",
		group : "Enter a group here"
	}
}

$('.container .title .right input[name="query"]').live({
	focus : function(){
		if($(this).val() == "Search")
			$(this).css('color','#000').val('');
	},
	blur : function(){
		if($(this).val() == "")
			$(this).val('Search').css('color','#ccc');
	}
});

$('.likes_area').live({
	click : function()
	{
		$(this).parent().children('.likers').slideToggle();
		return false;
	}
});

$('.like,.unlike').live({
	click : function()
	{
		var that = this;
		var profile_post = $(this).parent().parent();
		$.post (
			"/ajax.php",
			{
				action: $(this).attr('class'),
				post_id: profile_post.find('input[name="post_id"]').val()
			},
			function(data)
			{
				var result = $.parseJSON(data);
				if(result.response == 'error')
				{
					if(result.data == 'not_logged_in')
					{
						window.location = '/login.php?eid=2';
					}
				}
				else if(result.response == 'success')
				{

					var split = profile_post.find('span.likes_area a').html().split(' ');
					if($(that).attr('class') == 'like')
					{
						profile_post.find('span.likes_area a').html((parseInt(split[0]) + 1) + ' like' + ((parseInt(split[0]) + 1) == 1 ? '' : 's'));
						if(profile_post.find('div.likers').html().length == 0)
							profile_post.find('div.likers').html('You');
						else
							profile_post.find('div.likers').html('You, ' + profile_post.find('div.likers').html());
						$(that).attr('class','unlike').html('Unlike');
					}
					else
					{
						profile_post.find('span.likes_area a').html((parseInt(split[0]) - 1) + ' like' + ((parseInt(split[0]) - 1) == 1 ? '' : 's'));
						if(profile_post.find('div.likers').html() == 'You')
							profile_post.find('div.likers').html('');
						else
							profile_post.find('div.likers').html(profile_post.find('div.likers').html().substr(5));
						$(that).attr('class','like').html('Like');
					}
				}
			}
		);
		return false;
	}
});

$('.remove_friend,.remove_friend_request,.accept_friend_request,.deny_friend_request').live({
	click : function()
	{
		if(confirm('Are you sure you want to make this change?'))
		{
			var that = this;
			$.post (
				"/ajax.php",
				{
					action: $(this).attr('class'),
					profile_id: $(this).parent().find('input[name="profile_id"]').val() || $(this).parent().find('input[name="friend_id"]').val()
				},
				function(data)
				{
					var result = $.parseJSON(data);
					if(result.response == 'error')
					{
						if(result.data == 'not_logged_in')
							window.location = '/login.php?eid=2';
					}
					else if(result.response == 'success')
					{
						window.location.reload();
					}
				}
			);
		}
		return false;
	}
});

$('.share').live({
	click : function()
	{
		if(confirm('This post will show up on your profile.\n\nAre you sure you\'d like to do this?'))
		{
			var that = this;
			$.post (
				"/ajax.php",
				{
					action: $(this).attr('class'),
					post_id: $(this).parent().find('input[name="post_id"]').val()
				},
				function(data)
				{
					var result = $.parseJSON(data);
					if(result.response == 'error')
					{
						if(result.data == 'not_logged_in')
							window.location = '/login.php?eid=2';
					}
					else if(result.response == 'success')
					{
						window.location.reload();
					}
				}
			);
		}
		return false;
	}
});

$('.delete').live({
	click : function()
	{
		if(confirm('This post will be removed from your profile.\n\nAre you sure you\'d like to do this?'))
		{
			var that = this;
			$.post (
				"/ajax.php",
				{
					action: $(this).attr('class'),
					post_id: $(this).parent().find('input[name="post_id"]').val()
				},
				function(data)
				{
					var result = $.parseJSON(data);
					if(result.response == 'error')
					{
						if(result.data == 'not_logged_in')
							window.location = '/login.php?eid=2';
					}
					else if(result.response == 'success')
					{
						$(that).parent().parent().fadeOut('fast',function(){
							$(this).remove();
						});
					}
				}
			);
		}
		return false;
	}
});

/* handle tab switching on new_post.php */
$('b[class^="new_post_"]').live({
	click : function(){
		if(!$(this).hasClass('new_post_selected'))
		{
			var tab = $(this).attr('class').replace(/new_post_/,'');
			$('b[class^="new_post_"]').each(function(){
				$(this).removeClass('new_post_selected');
			});
			$(this).addClass('new_post_selected');

			$('.new_post input[type!="hidden"][type!="submit"][name!="group_alias"][name!="custom_name"],textarea').remove();
			switch(tab)
			{
				case 'image':
				case 'video':
					$('form').attr('enctype','multipart/form-data');
					$('input[name="what"]').attr('value',tab);
					$('<input tabindex="1" type="file" name="content" class="' + tab + '" />').prependTo('.new_post');
				break;
				case 'link':
					$('form').removeAttr('enctype');
					$('input[name="what"]').attr('value',tab);
					$('<input tabindex="1" name="content" class="link input_gray" value="' + Main.defaults.link + '" />').prependTo('.new_post');
				break;
				case 'status':
					$('form').removeAttr('enctype');
					$('input[name="what"]').attr('value',tab);
					$('<textarea tabindex="1" name="content" class="status input_gray" style="width:100%">' + Main.defaults.status + '</textarea>').prependTo('.new_post');
				break;
				case 'code':
					$('form').removeAttr('enctype');
					$('input[name="what"]').attr('value',tab);
					$('<textarea tabindex="1" name="content" class="' + tab + '" rows="10" style="width:100%">' + Main.defaults.code + '</textarea>').prependTo('.new_post');
				break;
			}
		}
	}
});

/* handle textarea events on new_post.php */
$('*[class~="input_gray"],*[class~="input_gray_remove"]').live({
	focus : function(){
		if($(this).hasClass('input_gray'))
			$(this).toggleClass('input_gray input_gray_remove').val('');
	},
	blur : function(){
		if($(this).val() == '')
		{
			if($(this).hasClass('status'))
				$(this).val(Main.defaults.status);
			else if($(this).hasClass('reply'))
				$(this).val(Main.defaults.reply);
			else if($(this).hasClass('link'))
				$(this).val(Main.defaults.link);
			else if($(this).hasClass('custom_name'))
				$(this).val(Main.defaults.custom_name);
			else if($(this).hasClass('friends'))
				$(this).val(Main.defaults.friends);
			else if($(this).hasClass('group'))
				$(this).val(Main.defaults.group);
			$(this).toggleClass('input_gray input_gray_remove');
		}
	}
});

/* handle select changes */
$('select[name="who"]').live({
	change : function(){
		switch($(this).val())
		{
			case 'anonymous':
				$('select[name="where"] option').each(function(){
					if($(this).attr('value') == "friends" || $(this).attr('value') == "specific_friends")
					{
						if($(this).attr('selected'))
							$('select[name="where"]').children('option[value=""]').attr('selected','selected');
						$(this).attr('disabled','disabled');
					}
				});
				$('input[name="custom_name"]').remove();
			break;
			case 'myself':
				$('select[name="where"] option').each(function(){
					if($(this).attr('value') == "friends" || $(this).attr('value') == "specific_friends")
						$(this).removeAttr('disabled');
				});
				$('input[name="custom_name"]').remove();
			break;
			case 'custom':
				$('select[name="where"] option').each(function(){
					if($(this).attr('value') == "friends" || $(this).attr('value') == "specific_friends")
					{
						if($(this).attr('selected'))
							$('select[name="where"]').children('option[value=""]').attr('selected','selected');
						$(this).attr('disabled','disabled');
					}
				});
				$('<input tabindex="25" name="custom_name" class="custom_name input_gray" value="Enter a custom name ..." />').appendTo($(this).parent());
			break;
		}
	}
});

/* handle post to changes */
$('select[name="where"]').live({
	change : function(){
		$('input[name="friends"],input[name="group_alias"]').remove();
		switch($(this).val())
		{
			case 'myself':
				$('input[name="group_alias"],input[name="friends"]').remove();
			break;
			case 'friends':
				$('input[name="group_alias"],input[name="friends"]').remove();
			break;
			case 'specific_friends':
				$('<input tabindex="35" name="friends" class="friends input_gray" value="' + Main.defaults.friends + '" />').appendTo($(this).parent());
			break;
			case 'group':
				$('<input tabindex="35" name="group_alias" class="group input_gray" value="' + Main.defaults.group + '" />').appendTo($(this).parent());
			break;
			case 'everyone':
			break;
		}
	}
});

var group_alias_changed = false;

/* handle group alias field change */
$('input[name="group_alias"]').live({
	change : function(){
		var that = this;
		$.post (
			"/ajax.php",
			{
				action: 'check_group_alias',
				group_alias: $(this).val()
			},
			function(data)
			{
				var result = $.parseJSON(data);
				if(result.response == 'error')
				{
					if(result.data == 'not_logged_in')
						window.location = '/login.php?eid=2';
				}
				else if(result.response == 'success')
				{
					if(result.data[0] == 'exists' && result.data[1] == 'password_protected')
					{
						$('<p class="group_password"><b><font color="red">This group requires a password. Enter it here:</font></b> <input tabindex="37" type="password" name="group_password" /></p>').appendTo('span[class="new_post"]');
					}
					else if(result.data[0] == 'exists' && result.data[1] == 'profile_protected')
					{
						$(that).css('border','1px solid #f00');
						$('<p class="group_password"><b><font color="red">This group is user protected.</font></b></p>').after('span[class="new_post"]');
					}
				}
			}
		);
	},
	keypress : function(){
		$('p[class="group_password"],input[name="group_password"]').remove();
	}
});

$('input[name="content"]').live({
	change : function(){
		if($(this).hasClass('link'))
		{
			if($(this).val().substr(0,7) != 'http://')
				$(this).val('http://' + $(this).val());
			if(/^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test($(this).val()))
			{
				$(this).attr('style','background:url(\'/check.png\') #CF9 no-repeat right middle');
			}
			else
			{
				$(this).attr('style','background:url(\'/error.png\') #FCC no-repeat right middle');
			}
		}
	}
});

/* image hover */
$('.image_post img').live({
	mouseover : function(){
		$(this).css('border','1px solid #000')
	},
	mouseout : function(){
		$(this).css('border','1px solid #ccc')
	}
});

/* hide success/error messages upon click */
/*$('.success_message,.error_message').live({
	click : function(){
		$(this).fadeOut(function(){
			$(this).remove();
		});
	}
});*/