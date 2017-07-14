var YC = {'channels':{}, 'is_pro': true};
jQuery(document).ready(function($){
	YC.EM = $({});
	var yrc_new_button = '<button class="yrc-new button button-primary">'+YC.lang.aui.add_new+'</button>';
		yrc_new_button += '<button class="yrc-clear-keys button">'+YC.lang.aui.delete_all+'</button>';
	
	YC.EM.on('yc.init', function(){
		$('#yrc-do-upgrade').remove();
		$('#yrc-channels').after( YC.template('#yrc-custom-playlist-tmpl') );
		YRC.merge(YC.dummy, YRC.pro_dummy);
		YC.mediaTemplate = YC.template('#yrc-social-media-tmpl');
		YC.playlists.init();
	});
		
	YC.EM.on('yc.deployed', function(){
		$('.yrc-content-header .yrc-content-buttons').append( yrc_new_button );
		
		$('#yrc-channels').on('click', '.yrc-new', function(e){
			YC.channels.createNew();
		});
		
		$('body').on('click', '.yrc-clear-keys', function(e){
			if( !confirm(YC.lang.aui.delete_all+' ?') ) return false;
			YC.post({'action': 'yrc_clear_keys', 'yrc_content': $(this).parents('#yrc-channels').length}, function(re){
				window.location.reload();
			});
		});
		
		$('#yrc-wrapper').append( YC.template('#pbc-license-tmpl') );

	});
			
	YC.EM.on('yc.form', function(e, channel){
		$('.pbc-form-save').before( YC.template('#yrc-pro-fields-tmpl')( YC.channel.data ) );
		$('#pbc-style-field').append( YC.template('#yrc-rating-style-tmpl')( YC.channel.data ) );
		$('#pbc-thumb-margin-field').before( YC.template('#yrc-columns-tmpl')( YC.channel.data ) );
		$('#pbc-form .pbc-row').eq(1).after( YC.template('#yrc-tag-tmpl')( YC.channel.data.meta ) );
		$('#pbc-form #pbc-show-sections').append( YC.template('#yrc-show-tmpl')( YC.channel.data ) );
		$('#pbc-form #yrc-player-options').append( YC.template('#yrc-player-options-tmpl')( YC.channel ) );
		applyColorPicker();
	});
	
	YC.EM.on('yc.save', function(e, o){
		YC.channel.data.social = {};
		$('#pbc-social-media-field input').each(function(){
			if(this.value) YC.channel.data.social[this.name] = this.value;
		});
		o.per_page = Math.min(parseInt( o.per_page ), 50);
		o.maxv = parseInt( o.maxv );
		
		YC.channel.data.meta.tag = o.tag;
		YC.channel.data.style.rating_style = o.rating_style;
		YC.channel.data.meta.per_page = (o.per_page||25);
		YC.channel.data.meta.maxv = (o.maxv||0);
		YC.channel.data.meta.default_sorting = $('#yrc-default-sorting').val();
		YC.channel.data.meta.search.rtc = o.rtc;
		YC.channel.data.meta.autoplay = o.autoplay || o.autoload_play;
		YC.channel.data.style.pagination = o.pagination;
		YC.channel.data.style.subscriber = o.subscriber;
		YC.channel.data.style.subscriber_count = o.subscriber_count;
		YC.channel.data.style.subscribe_button = o.subscribe_button || o.banner || '';
		YC.channel.data.style.load_first = o.load_first;
		YC.channel.data.style.autoplay_next = o.autoplay_next;
		YC.channel.data.style.default_tab = o.default_tab;
		YC.channel.data.style.columns = o.columns;
		YC.channel.data.css = $('textarea.wpb-raw[name=css]').val();
		YC.channel.data.meta.blacklist = $('textarea.wpb-raw[name=blacklist]').val();
		if(o.load_first) YC.channel.data.style.preload = YouTubeGetID( o.preload );
		else delete YC.channel.data.style.preload;
		delete YC.channel.data.meta.custom;
		delete YC.channel.data.meta.custom_vids;
	});
	
	function YouTubeGetID(url){
	  var ID = '';
	  url = url.replace(/(>|<)/gi,'').split(/(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/);
	  if(url[2] !== undefined) {
		ID = url[2].split(/[^0-9a-z_\-]/i);
		ID = ID[0];
	  }
	  else {
		ID = url;
	  }
		return (typeof ID === 'string') ? ID : ID[0];
	}
	
	function applyColorPicker(){
		$('.wpb-color').wpColorPicker({'change': function(e){
				var keys = $(this).data('keys').split(', ');
				changeValue(keys, $(this));
			}
		});
		$('.iris-slider').css({'height': 182, 'margin-left':-5});
	}

	function changeValue(keys, el){
		var num = parseInt(keys[3]), val = num ? parseInt(el.val()) : el.val();
		
		$('#yrc-live '+keys[1]).css(keys[2], val );
		if(keys[1] == '.yrc-button' && keys[2] == 'background'){
			$('.yrc-menu li.yrc-active').css('border-bottom', '3px solid '+val);
			$('.yrc-menu .yrc-sort-trigger').css('border-top', '.5em solid '+val);
		}
		
		if(keys[1] == '.yrc-banner' && keys[2] == 'color')
			$('.yrc-banner svg .yrc-stat-icon').css('fill', val);
		
		keys = keys[0].split('-');
		YC.channel.data.style[keys[0]][keys[1]][keys[2]] = num ? (val / 16) : val;
	}

	$('body').on('change', '#pbc-colors input', function(e){
		var keys = $(this).data('keys').split(', ');
		changeValue(keys, $(this));
	});
	
	$('body').on('change', 'input[name=load_first]', function(e){
		$('#yrc-auto-load-vid, #yrc-auto-load-play-vid').toggleClass('wpb-hidden');
		if(!this.checked) $('#yrc-auto-load-play-vid').removeAttr('checked');
	});
	
	$('body').on('click', '#yrc-common-css', function(e){
		$('#yrc-defined-css').removeClass('wpb-hidden');
		$('html,body').animate({'scrollTop': $('#yrc-defined-css').offset().top-50}, 'slow');
	});
	
	$('body').on('click', '#yrc-playlists h2', function(e){
		$(this).parent().siblings().toggleClass('wpb-force-hide');
		$(this).siblings().toggleClass('wpb-force-hide');
	});
	
	$('body').on('click', '#yrc-license-required a', function(e){
		e.preventDefault();
		$(this).next().toggleClass('pb-hidden');
	});
	
	$('body').on('click', '#pbc-add-media', function(e){
		var m = $('#pbc-social-medias').val();
		if(!YC.channel.data.social[m]){
			YC.channel.data.social[m] = '#';
			$('#pbc-social-media-field').append( YC.mediaTemplate({'m':m, 'v':''}) );
		}	
	});
	
	
	$('body').on('submit', '#pbc-license-formm', function(e){
		e.preventDefault(); var fo = $(this);
		fo.find('.button').text('....');
		YC.post({'action': 'yrc_save_license', 'pb_license_key': fo.find('input[name=key]').val(), 'pb_license_action': fo.find('input[name=action]').val()}, function(re){
			//fo.find('.button').text('Save');
			window.location.reload();
		});
	});
	
	$('body').on('keyup', '.pbc-row input[name=per_page]', function(e){
		if(parseInt(this.value) === 1) $('#yrc-autoplay-field').removeClass('wpb-force-hide');
		else {
			$('#yrc-autoplay-field').addClass('wpb-force-hide');
			$('#yrc-autoplay-field input').removeAttr('checked');
		}
	});
		
	YC.medias = {'facebook':'Facebook', 'twitter':'Twitter', 'soundcloud':'Soundcloud', 'googleplus':'Google Plus',
		'instagram':'Instagram', 'tumblr':'Tumblr', 'lastfm':'Last FM', 'vimeo':'Vimeo', 'xing':'Xing', 'flicker':'Flicker',
		'steam': 'Steam', 'twitch': 'Twitch'
	};
	
	YC.playlists = {};
	YC.playlist = {};
	
	YC.playlists.adminit = function(playlist, key, is_new){
		YC.playlist.data = playlist;
		$('#yrc-playlists tbody').append( YC.template('#yrc-playlist-tmpl')( YC.playlist.data ));
	};
	
	YC.playlists.list = function(d, is_new){
		if(is_new){
			$('#yrc-playlists tbody').find('tr[data-down="nw"]').remove()
				.end().append( YC.template('#yrc-playlist-tmpl')(d) );
		}	
		else	
			$('#yrc-playlists tbody tr[data-down="'+d.key+'"]').replaceWith( YC.template('#yrc-playlist-tmpl')(d) );
	};

	YC.playlists.createNew = function(){
		var dum = {'key': 'nw', 'name': 'Playlist', 'videos': ['qKzH6pRfzoU', 'oIEEIif21g4', 'sDI6HTR9arA', '5o2HHVxt6uU', 'FDYIdBZUl2Y']};
		YC.playlists['nw'] = dum;
		YC.playlists.adminit( dum, 'nw', true );
	};
	
	YC.playlists.deploy = function( playlists ){
		playlists.forEach(function(playlist){
			YC.playlists[ playlist.key ] = playlist;
			YC.playlists.list(playlist, true);
		});
		
		$('#yrc-playlists').toggleClass('wpb-hidden');
		
		$('#yrc-playlists').on('click', 'tr.pbc-down .pbc-edit', function(e){
			$(this).toggleClass('pbc-edit pbc-save button-primary').text( YC.lang.aui.save )
				.parents('tr').find('textarea').attr('rows', 6);
			YC.playlist.data = YC.playlists[ $(this).data('down') ];
		});
		
		$('#yrc-playlists').on('click', 'tr.pbc-down .pbc-save', function(e){
			$(this).text(YC.lang.aui.saving+'...');
			var is_new = (YC.playlist.data.key === 'nw'), tr = $(this).parents('tr');
			
			YC.playlist.data.name = tr.find('input.yrc-playlist-name').val();
			YC.playlist.data.videos = tr.find('textarea').val().split(',').filter(function(v){ return v.trim() !== ''; })
				.map(function(v){ return YouTubeGetID(v.trim()); });
												
			YC.post({'action': 'yrc_save_playlist', 'yrc_playlist': YC.playlist.data}, function(re){
				if(!re) return false;
				YC.playlist.data.key = re;
				YC.playlists.list(YC.playlist.data, is_new);
				
				YC.playlists[re] = YC.playlist.data;
				YC.playlist = {};
			});
		});
		
		$('body').on('click', '#yrc-playlists .yrc-new', function(e){
			YC.playlists.createNew();
		});
	};
	
	YC.playlists.init = function(){
		YC.post({'action': 'yrc_get_playlists'}, function(re){		
			YC.playlists.deploy(re);
		});
	};
			
});
	

	
	
	
var YC = YC || {'channels':{}};
	YC.lang = {'aui': yrc_lang_terms.aui};
	
jQuery(document).ready(function($){
	YC.EM = YC.EM || $({});

	YC.template = function(selector){
		return _.template($(selector).html());
	};
	
	YC.post = function(data, success, error){
		$.ajax({
			url: 'admin-ajax.php',
			type: 'POST',
			data: data,
			dataType: 'json',
			success: success,
			error: error
		});
	};
	
	YC.channel = {};

	YC.channels.adminit = function(channel, key, is_new){
		if(!is_new) YRC.merge(channel, YC.dummy);
		channel.social = channel.social || {};
		YC.channel.data = channel;
		YC.channel.key = key;
		
		$('.yrc-content, #yrc-lang-form').addClass('wpb-hidden');
		$('#yrc-editor').html( YC.template('#yrc-form-tmpl')( YC.channel.data )).removeClass('wpb-hidden');
						
		YC.EM.trigger('yc.form');
						
		$('#yrc-live').removeClass('wpb-hidden');
		YC.channel.setup = new YRC.Setup(0, YC.channel.data, $('#yrc-live'));
	};
	
	YC.redraw = function(){
		$('style.yrc-stylesheet').remove();
		$('#yrc-live').empty();
		YC.channel.setup = new YRC.Setup(0, YC.channel.data, $('#yrc-live'));
	};

	$('body').on('change', 'input[name=apikey]', function(e){
		YC.channel.data.meta.apikey = $(this).val().trim();
		YRC.auth.apikey = $(this).val().trim();
	});
	
	$('body').on('click', '#pbc-delete-form', function(e){
		$(this).text(YC.lang.aui.deleting+'...');
		YC.post({'action': 'yrc_delete', 'yrc_key': YC.channel.data.meta.key}, function(re){
			$('.pbc-down[data-down='+YC.channel.data.meta.key+']').remove();
			delete YC.channels[YC.channel.data.meta.key];
			YC.cleanForm();
			if( !YC.is_pro ) window.location.reload();
		});
	});

	$('body').on('click', '#yrc-get-channel-id', function(e){
		$('.pbc-form-message').text('').removeClass('pbc-form-error');
		if(!YC.channel.data.meta.apikey || YC.channel.data.meta.apikey.length != 39) return YC.formError('apikey');
		var user_box = $('#yrc-username'), channel_input = $('#yrc-channel');
			if(!user_box.val() && !channel_input.val()) return;
			
		YRC.auth.apikey = YRC.auth.apikey || YC.dummy.meta.apikey;	
		var uu = user_box.val() ? YRC.auth.baseUrl('channels?part=snippet,contentDetails,statistics&forUsername='+user_box.val().trim())
				: YRC.auth.baseUrl('channels?part=snippet,contentDetails,statistics&id='+channel_input.val().trim());
		ajax(uu, function(re){
			if(!re.items.length)
				$('#yrc-ac-error').text( (user_box.val() ? user_box.val() : channel_input.val()) + YC.lang.aui.does_not_exist ).addClass('pbc-form-error');
			else {
				if(user_box.val()) channel_input.val(re.items[0].id);
				else user_box.val(re.items[0].snippet.title.replace(/[\[\]']+/g,'-'));
				
				channel_input.val(re.items[0].id);
				YC.channel.data.meta.channel = re.items[0].id;
				YC.channel.data.meta.user = user_box.val();
				YC.channel.data.meta.channel_uploads = re.items[0].contentDetails.relatedPlaylists.uploads;
				YC.channel.data.meta.onlyonce = '';
				YC.redraw();
			}
		}, function(er){
			console.log(er);
		});
	});
	
	$('body').on('change', '#yrc-channel', function(e){ $('#yrc-username').val(''); });
	$('body').on('change', '#yrc-username', function(e){ $('#yrc-channel').val(''); });
	$('body').on('change', '#pbc-show-sections input', function(e){
		if(this.name === 'banner') $('#yrc-show-sub-button').toggleClass('wpb-force-hide');
		YC.channel.data.style[this.name] = this.checked ? true : '';
		if(YC.channel.data.style.search_on_top) YC.channel.data.style.search = true;
		YC.redraw();
	});
	
	$('body').on('change', 'input[name=video_meta], input[name=video_size]', function(e){
		if(this.name === 'video_size')
			YC.channel.data.style.video_style[0] = this.value;
		else 
			YC.channel.data.style.video_style = [(this.value === 'adjacent' ? 'adjacent' : YC.channel.data.style.video_style[0]), this.value];
		
		if(YC.channel.data.style.video_style[1] === 'adjacent'){
			YC.channel.data.style.video_style[0] = 'adjacent';
			$('input[value=small]').attr('checked', 'checked');
		} else {
			if(YC.channel.data.style.video_style[0] === 'adjacent')
				YC.channel.data.style.video_style[0] = 'small';
		}
		
		$('.yrc-video').removeClass('yrc-item-open yrc-item-none yrc-item-closed yrc-item-adjacent yrc-item-small yrc-item-large')
			.addClass('yrc-item-'+YC.channel.data.style.video_style[0]+' yrc-item-'+YC.channel.data.style.video_style[1]);	
		YC.channel.setup.size.resize();
	});

	function ajax(url, success, error){
		$.ajax({
			type: 'GET',
			url: url,
			success: success,
			error: error
		});
	}
	
	function rawValues( inputs ){
		var o = {};
		inputs.each(function(){
			if(this.type === 'radio'){
				if(this.checked) o[this.name] = this.value;
			} else if (this.type === 'checkbox') {
				o[this.name] = this.checked ? 1 : '';
			} else o[this.name] = this.value;
		});
		return o;
	}

	$('body').on('submit', '#pbc-form', function(e){
		e.preventDefault();
		$('.pbc-form-message').text('').removeClass('pbc-form-error');
		if(!YC.channel.data.meta.user || !YC.channel.data.meta.channel|| !YC.channel.data.meta.apikey) 
			return YC.formError('invalid');
		
		var o = rawValues($('input.wpb-raw'));
		YC.channel.data.style.player_mode = o.player_mode;
		YC.channel.data.style.truncate = o.truncate;
		YC.channel.data.style.rtl = o.rtl;
		YC.channel.data.style.thumb_margin = o.thumb_margin || 8;
		YC.channel.data.style.video_style = YC.channel.data.style.video_style.splice(0, 2);
		YC.channel.data.style.player_top = o.player_top;
		YC.channel.data.style.thumb_image_size = o.thumb_image_size;
		YC.channel.data.style.play_icon = o.play_icon;
		YC.channel.data.meta.onlyonce = o.onlyonce;
		
			
		YC.EM.trigger('yc.save', o);	
		
		$('.pbc-form-save .button-primary').text(YC.lang.aui.saving+'...');
		var is_new = (YC.channel.key === 'nw');
		delete YC.channel.data.meta.playlist;
		
		YC.post({'action': 'yrc_save', 'yrc_channel': YC.channel.data}, function(re){
			if(!re) YC.formError('invalid');
			
			YC.channel.data.meta.key = re;
			YC.channels.list(YC.channel.data, is_new);
			
			YC.channels[re] = YC.channel.data;
			YC.cleanForm();
		});
	});
	
	YC.cleanForm = function(){
		delete YC.channels.nw;
		delete YC.channel.data;
		delete YC.channel.key;
		delete YC.channel.setup;
				
		$('style.yrc-stylesheet').remove();
		$('#yrc-editor, #yrc-live').empty();
		$('.yrc-content, #yrc-editor, #yrc-lang-form').toggleClass('wpb-hidden');
		$('#yrc-defined-css').addClass('wpb-hidden');
		$("html, body").animate({ scrollTop: 0 }, "slow");
	};
	
	YC.formError = function(code){
		var messages = {
			'apikey': YC.lang.aui.enter_api_key,
			'invalid': YC.lang.aui.invalid_inputs
		};
		$('.pbc-form-message').text( messages[code] ).addClass('pbc-form-error');
		return false;
	};
			
	YC.dummy = {
		'meta': {
			'user': 'mrsuicidesheep',
			'channel': 'UC5nc_ZtjKW1htCVZVRxlQAQ',
			'key': 'nw',
			'apikey': 'AIzaSyCEhi-95k6t1rX4pDVqugauM-UyTfNHj8Q',
			'channel_uploads': '',
			'onlyonce': '',
			'tag':''
		},
		
		'style': {
			'colors': {
				'item': {
					'background': '#fff'
				},
				'button': {
					'background': '#333',
					'color': '#fff'
				},
				'color': {
					'text': '#fff',
					'link': '#000'
				}
			},
			'fit': false,
			'playlists': true,
			'uploads': true,
			'video_style':['large', 'open'],
			'player_mode': 1,
			'truncate': 1,
			'rtl':'',
			'banner':true,
			'thumb_margin':8,
			'play_icon':'',
			'player_top':'title',
			'thumb_image_size':'medium',
			'default_tab': 'uploads'
		}
	};
		
	YC.lang.form_labels = {
		'Videos': 'Videos',
		'Playlists': 'Playlists',
		'Search': 'Search',
		'Loading': 'Loading',
		'more': 'more',
		'Nothing_found': 'Nothing found',
		'Prev': 'Previous',
		'Next': 'Next'
	};
			
	YC.lang.show = function(){
		if( !YRC.lang.form.Prev && YC.is_pro ){
			YRC.lang.form.Prev = 'Previous';
			YRC.lang.form.Next = 'Next';
		}
		$('#yrc-wrapper').append( YC.template('#yrc-lang-form-tmpl')({'terms': YRC.lang.form}) );
	};
		
	$('body').on('submit', '#yrc-lang-form', function(e){
		e.preventDefault(); var fo = $(this);
		YRC.lang.form = rawValues(fo.find('input'));
		fo.find('button').text(YC.lang.aui.saving+'....');
		YC.post({'action': 'yrc_save_lang', 'yrc_lang': YRC.lang.form}, function(re){
			fo.find('button').text(YC.lang.aui.save);
		});
	});
	
	$('body').on('click', '#yrc-delete-terms', function(e){
		e.preventDefault(); var a = $(this);
		a.text(YC.lang.aui.clearing+'....');
		YC.post({'action': 'yrc_delete_lang'}, function(re){
			a.text(YC.lang.aui.clear);
			window.location.reload();
		});
	});
	
	$('body').on('click', '.pbc-front-form .pbc-front-form-header ', function(e){
		$(this).next().toggleClass('wpb-zero');
	});
	
	$('body').on('click', '.pbc-field-toggler', function(e){
		$(this).next().toggleClass('wpb-force-hide');
	});
	
	YC.channels.remove = function(d){
		$('#yrc-channels tbody tr[data-down="'+d.meta.key+'"]').remove();
	};
		
	YC.channels.list = function(d, is_new){
		if(is_new)
			$('#yrc-channels tbody').append( YC.template('#yrc-channel-tmpl')(d) );
		else	
			$('#yrc-channels tbody tr[data-down="'+d.meta.key+'"]').replaceWith( YC.template('#yrc-channel-tmpl')(d) );
	};

	YC.channels.createNew = function(){
		var dum = JSON.parse( JSON.stringify( YC.dummy ) );
		YC.channels['nw'] = dum;
		YC.channels.adminit( dum, 'nw', true );
	};

	YC.versionCheck = function(){
		if(!window.localStorage) return false;
		if(localStorage.getItem('yrc_version') != $('#yrc-wrapper').data('version')) YC.newVersionInfo();
	};
	
	YC.newVersionInfo = function(){
		$('#yrc-version-info').removeClass('wpb-hidden');
		YC.setVersion();
	};
	
	YC.setVersion = function(){
		localStorage.setItem('yrc_version', $('#yrc-wrapper').data('version'));
	};
	
	YC.channels.deploy = function( channels ){
		$('#yrc-init-loader').addClass('wpb-hidden');
		channels.forEach(function(channel){
			YC.channels[ channel.meta.key ] = channel;
			YC.channels.list(channel, true);
		});
		
		if(channels.length){
			YC.dummy.meta.apikey = channels[0].meta.apikey;
			$('#yrc-channels, #yrc-editor').toggleClass('wpb-hidden');
			YC.versionCheck();
		} else {
			YC.channels.createNew();
			YC.setVersion();
		}
		
		$('#yrc-channels').on('click', 'tr.pbc-down .pbc-edit', function(e){
			YC.channels.adminit( YC.channels[ $(this).data('down') ], $(this).data('down') );
		});
		
		$('body').on('click', '#pbc-cancel-form', function(e){
			YC.cleanForm();
		});
		
		YC.EM.trigger('yc.deployed');
		YC.lang.show();
	};

	YC.channels.init = function(){
		YC.post({'action': 'yrc_get'}, function(re){		
			YC.channels.deploy(re);
		});
		$('#yrc-wrapper').append( YC.template('#yrc-main-tmpl') )
		YC.EM.trigger('yc.init');
	};

	YC.channels.init();

});
