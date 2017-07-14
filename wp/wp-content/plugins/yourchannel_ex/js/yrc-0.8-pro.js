
var YRC = YRC || {};
jQuery(document).ready(function($){
	YRC.template = YRC.template || {};
	var YRCPRO = {};

	YRCPRO.extendClasses = function(){
		YRC.Search.prototype.events = function(){
			var yc = this;
			$('body').on('submit', this.ref.sel+' .yrc-search-form-el', function(e){
				e.preventDefault();
				if($(this).data('top'))$(yc.ref.sel+' .yrc-menu .yrc-menu-item[data-section=search]').trigger('click');
				$(yc.coresel).find('.yrc-load-more-button').remove();
				$(yc.coresel + ' .yrc-core').empty();
				yc.request.times = 0;
				var f = $(this);
				yc.criteria = {'order': (l('order')||'relevance'), 'term': l('term'), 'duration': (l('duration')||'any')};
				var df = (l('date')||search_date).split(',');
				yc.criteria.after = df[0];
				yc.criteria.before = df[1];
				yc.fetch();
				$(yc.ref.sel+' .yrc-search-form .yrc-search-term').val(yc.criteria.term);
				$(yc.coresel).parents('.yrc-sections').css('height', $(yc.coresel).parent().height());
				function l(n){ return f.find('.yrc-search-'+n).val() || '';}
			});
			
			var search_date = iso( new Date(+new Date - (15*365*24*60*60*1000) )) +','+ iso( new Date() );
		};
		
		YRC.Search.prototype.fetchAtSetup = function(){
			this.restrict_to_channel = this.ref.data.meta.search.rtc;
			this.coresel = this.ref.sel + ' .yrc-search-videos';
		};
				
		YRC.Uploads.prototype.events = function(){
			var yc = this;
			$('body').on('click', yc.ref.sel+' .yrc-menu-items .yrc-menu-item[data-section=uploads]', function(e){
				if(yc.ref.section === 'uploads')
					$(this).parents('.yrc-menu').children('.yrc-sort-uploads').toggleClass('pb-hidden');
			});
			
			$('body').on('click', yc.ref.sel+' .yrc-sort-uploads li', function(){
				$(this).addClass('yrc-active').siblings().removeClass('yrc-active');
				yc.criteria = $(this).data('value');
				$(yc.coresel + ' .yrc-core').empty();
				yc.request.page = '';
				yc.request.times = 0;
				yc.fetch();
			});
		};
		
		YRC.Uploads.prototype.preloader = function(){
			if(!this.ref.data.style.preload) return false;
			if(this.per_page === 1) this.per_page = 0;
			var upl = this, url = YRC.auth.baseUrl('videos?part=snippet,contentDetails,statistics&id='+this.ref.data.style.preload);
			upl.ref.preloading = true;
			$.get(url, function(re){ 
				delete upl.ref.preloading;
				var core = $(upl.ref.sel +' .yrc-uploads .yrc-core');
					re = re.items[0];
					re.snippet.resourceId = {'videoId' : re.id};
					core.prepend( YRC.template.video( re , 1, upl.ref.data.style.video_style) );
					core.find('.yrc-video').eq(0).find('figure').addClass('yrc-full-scale');
				upl.ref.adjust(core, '.yrc-video', 'uploads');
				var rates = {}; rates[re.id] = YRCPRO.ratings.rerate(re);
				YRCPRO.ratings.appendMeta( core.find('.yrc-video').eq(0), rates);
				upl.ref.first_loaded = true;
				YRC.EM.trigger('yrc.first_load', [[upl.ref, core]]);
			});
		};
		
		YRC.Uploads.prototype.proSetup = function(){
			if(this.ref.data.meta.custom){
				this.temp_label = 'Custom';
				this.custom_vids = this.ref.data.meta.custom_vids;
				this.custom_vids_length = this.custom_vids.length;
			}
			this.preloader();
		};
	};
	
	function iso(d){
	 function pad(n){return n<10 ? '0'+n : n}
	 return d.getUTCFullYear()+'-'
		  + pad(d.getUTCMonth()+1)+'-'
		  + pad(d.getUTCDate())+'T'
		  + pad(d.getUTCHours())+':'
		  + pad(d.getUTCMinutes())+':'
		  + pad(d.getUTCSeconds())+'Z'
	}
	
	YRC.pro_dummy = {
		'meta': {
			'search': {
				'rtc': ''
			},
			'default_sorting': 'date',
			'maxv':500,
			'per_page':25,
			'autoplay': '',
			'blacklist':''
		},
		'style': {
			'colors': {
				'rating': {
					'like': '#30DBBF',
					'dislike': '#111111',
					'neutral': '#bbbbbb'
				},
				'input': {
					'background': '#333',
					'color': '#fff'
				}
			},
			'rating_style': 1,
			'search':true,
			'menu':true,
			'search_on_top':'',
			'ratings': true,
			'load_first':'',
			'autoplay_next':1,
			'pagination': '',
			'subscriber':'default',
			'subscriber_count':'default',
			'subscribe_button':true,
			'columns': 0
		},
		'css':'',
		'social':{}
	};	
	
	YRC.template.search = function(){		
		var template = 
			'<form class="yrc-search-form yrc-search-form-el">\
				<div class="pb-row">\
					<div class="pb-row-label pb-inline"></div>\
					<div class="pb-row-field pb-inline">\
						<input type="text" placeholder="'+YRC.lang.form.Search+'.." class="yrc-search-term"/>\
					</div>\
				</div>\
				<div class="pb-row pb-inline">\
					<div class="pb-row-label pb-inline">'+YRC.lang.fui.sort_by+'</div>\
					<div class="pb-row-field pb-inline">\
						<select class="yrc-search-order">\
							<option value="relevance">'+YRC.lang.fui.relevant+'</option>\
							<option value="date">'+YRC.lang.fui.latest+'</option>\
							<option value="rating">'+YRC.lang.fui.liked+'</option>\
							<option value="title">'+YRC.lang.fui.title+'</option>\
							<option value="viewCount">'+YRC.lang.fui.views+'</option>\
						</select>\
					</div>\
				</div>\
				<div class="pb-row pb-inline">\
					<div class="pb-row-label pb-inline">'+YRC.lang.fui.duration+'</div>\
					<div class="pb-row-field pb-inline">\
						<select class="yrc-search-duration">\
							<option value="any">'+YRC.lang.fui.any+'</option>\
							<option value="short">'+YRC.lang.fui._short+'</option>\
							<option value="medium">'+YRC.lang.fui.medium+'</option>\
							<option value="long">'+YRC.lang.fui._long+'</option>\
						</select>\
					</div>\
				</div>\
				<div class="pb-row pb-inline">\
					<div class="pb-row-label pb-inline">'+YRC.lang.fui.uploaded+'</div>\
					<div class="pb-row-field pb-inline">\
						<select class="yrc-search-date">\
							<option value="'+ iso( new Date(+new Date - (15*365*24*60*60*1000) )) +','+ iso( new Date() ) +'">'+YRC.lang.fui.all_time+'</option>\
							<option value="'+ iso( new Date(+new Date - (24*60*60*1000) )) +','+ iso( new Date() ) +'">'+YRC.lang.fui.today+'</option>\
							<option value="'+ iso( new Date(+new Date - (7*24*60*60*1000) )) +','+ iso( new Date() ) +'">'+YRC.lang.fui.last+' '+YRC.lang.fui.week+'</option>\
							<option value="'+ iso( new Date(+new Date - (30*24*60*60*1000) )) +','+ iso( new Date() ) +'">'+YRC.lang.fui.last+' '+YRC.lang.fui.month+'</option>\
							<option value="'+ iso( new Date(+new Date - (365*24*60*60*1000) )) +','+ iso( new Date() ) +'">'+YRC.lang.fui.last+' '+YRC.lang.fui.year+'</option>\
							<option value="'+ iso( new Date(+new Date - (15*365*24*60*60*1000) )) +','+ iso( new Date(+new Date - (365*24*60*60*1000) ) ) +'">'+YRC.lang.fui.older+'..</option>\
						</select>\
					</div>\
				</div>\
				<div><button class="yrc-search-submit">'+YRC.lang.form.Search+'</button></div>\
			</form>';	
			
		return '<div class="yrc-section pb-inline"><div class="yrc-search yrc-sub-section">'+
					template +
				'</div>\
			<div class="yrc-search-videos yrc-sub-section"><ul class="yrc-core"></ul></div></div>';
	};
	
	var uploadSorter = function(){
		return '<ul class="yrc-sort-uploads pb-absolute pb-hidden">\
			<li data-value="date">'+YRC.lang.fui.latest+'</li>\
			<li data-value="rating">'+YRC.lang.fui.liked+'</li>\
			<li data-value="title">'+YRC.lang.fui.title+'</li>\
			<li data-value="viewCount">'+YRC.lang.fui.views+'</li>\
		</ul>'
	};
		
	var search_menu_item = function(){ 
		return '<li class="pb-inline yrc-menu-item" data-section="search">'+ YRC.lang.form.Search +'</li>';
	};

	var subscriberButton = function(c){
		return '<div class="yrc-subscriber pb-absolute" data-channelid="'+c.meta.channel+'">\
			<div class="g-ytsubscribe" data-channelid="'+ c.meta.channel +'" data-layout="'+c.style.subscriber+'" data-count="'+c.style.subscriber_count+'"></div>\
		</div>';
	};	

	
	YRC.EM = $({});
		
	YRC.EM.on('yrc.extras', function(e){
		YRC.extras.search = {'sel': ' .yrc-search', 'label': 'Search'};
	});
	
	YRC.EM.on('yrc.classes_defined', function(e){
		YRCPRO.extendClasses();
		YRC.is_pro = true;
	});
	
	YRC.EM.on('yrc.defaults', function(e, channel){
		YRC.merge(channel, YRC.pro_dummy);
		if(channel.meta.default_sorting === 'title_desc') {
			channel.meta.default_sorting = 'title';
			channel.meta.temp_sort = 'title_desc';
		}
	});
		
	YRC.EM.on('yrc.setup', function(e, setup){
		$('body').on('click', setup.sel+'-player-shell .yrc-page-nav', function(e){
			yrcPlayNext(setup, $(this).is('.yrc-next'));
		});
	
		if(setup.active_sections.search){
			setup.search = new YRC.Search().init(setup, 'search');
			$(setup.sel).find('.yrc-menu-items').append( search_menu_item() );
		}
		YRCPRO.ratings.colors = setup.data.style.colors.rating;
		YRCPRO.ratings.style = setup.data.style.rating_style;
		YRCPRO.ratings.show = setup.data.style.ratings;
		if((setup.data.meta.playlist && !setup.onlyonce) || setup.data.meta.custom) return false;
		sorter(setup.sel, setup.data.meta.default_sorting);
	});
	
	YRC.EM.on('yrc.style', function(e, opts){
		var colors = opts[1].style.colors, sel = opts[0];
		var css = sel +' .yrc-sort-trigger{	border-top:.5em solid '+ colors.button.background +';}\
			'+ sel +' .pb-row{\
				border-bottom:1px solid #ddd;\
			}\
			'+ sel +' .yrc-shell input, .yrc-shell select{\
				background:'+ colors.input.background +';\
				color:'+ colors.input.color +';\
				border:none;\
			}\
			'+ sel +' .yrc-video-duration{\
				color: '+ colors.color.link +';\
				background: '+ colors.item.background +';\
			}';
		var cc = opts[1].css.trim().split('}');
		cc.forEach(function( st ){ if(st) css += sel + ' ' + st + '}'; });
		$('head').append('<style class="yrc-stylesheet">'+ css + '</style>');	
	});
	
	YRC.EM.on('yrc.deployed', function(e, d){
		var sel = d[0], sstyle = (d[1].style.subscriber === 'full');
		$(sel+' .yrc-banner').eq(0).append( subscriberButton(d[1]) );
		var si = '<div class="yrc-social-links">';
		for(var s in d[1].social){
			si += '<a href="'+d[1].social[s]+'" target="_blank">'+YRC.template.social[s]+'</a>';
		}
		$(sel+' .yrc-banner').eq(0).append(si);
		$(sel+' .yrc-social-links svg')
			.css({'width':30, 'height':30, 'background':d[1].style.colors.button.background})
			.find('path').css('fill', d[1].style.colors.button.color).end()
			.find('rect').css('fill', 'none');
		
		if(!d[1].style.banner && d[1].style.subscribe_button){
			$('head').append('<style>'+sel+' .yrc-banner{ display:block !important; height:40px !important;}'+
				sel+' .yrc-brand{ display:none !important;}'+
				sel+' .yrc-subscriber{ left:0px !important; bottom:0px !important; }'+
				'.yrc-mobile '+sel+' .yrc-banner{ height:auto !important; }</style>');
		} else {
			$(sel+' .yrc-subscriber').css('bottom', -(parseInt( $(sel+' .yrc-menu').css('margin-top') ) + (sstyle ? 45 : 36))).css((d[1].style.rtl ? 'left' : 'right'), 0);
		}
		addSubscribeButton();
		if(!d[1].style.search_on_top)return false;
		$(sel+' .yrc-sections').before( YRC.template.topSearch() );
		$(sel+' .yrc-search-form-top svg').css({
			'height': function(){ return $(this).parent().height(); },
			'width': function(){ return $(this).parent().height(); }
		}).parents('form').find('input').css('padding-right', function(){return $(this).parent().height()+4; });
	});
	
	YRC.EM.on('yrc.videos_listed', function(e, cont){
		YRCPRO.ratings.get( cont );
	});
	
	YRC.EM.on('yrc.newchannel', function(e){
		YRC.run();
	});
	
	YRCPRO.restOf = function(){
		$('.yrc-shell-cover').each(function(){ YRC.run( $(this) ); });
	};
	
	YRC.EM.on('yrc.run', function(e){
		YRCPRO.restOf();
	});
	
	YRC.EM.on('yrc.api_loaded', function(e){
		YRCPRO.toload.forEach(autoPlayInInit);
		delete YRCPRO.toload;
		YRC.yt_api_loaded = true; 
	});
	
	YRCPRO.toload = [];
		
	YRC.EM.on('yrc.first_load', function(e, p){
		if(!p[0].data.style.load_first && !p[0].data.meta.autoplay) return false;
		if(!YRC.yt_api_loaded) YRCPRO.toload.push(p);
		else autoPlayInInit(p);
	});
	
	function autoPlayInInit(p){
		var yc = p[0], core = p[1], li = core.children('li').eq(0);
		if(!li.siblings().length) core.find('.yrc-video').addClass('yrc-onlyone-video');
		li.addClass('yrc-playing');
		core.prepend( YRC.template.player( li, p[0]) );
		$('.yrc-player-frame').css('height', ((9/16) * $('.yrc-player').width()) );
		yc.player.player = YRC.Player(yc, yc.data.meta.autoplay);
		yc.player.list = core;
	}
	
	function yrcPlayNext(yc, next){
		var to_play = yc.player.list.find('li[data-video='+(yc.player.player.getVideoData().video_id)+']');
		if(next)
			to_play = to_play.nextAll('.yrc-video').first();
		else	
			to_play = to_play.prevAll('.yrc-video').first();
			
		to_play  = to_play.length ? to_play : yc.player.player.list.find('.yrc-video').eq( (next ? 0 : (yc.player.player.list.length-1)) );
		
		yc.player.list.find('.yrc-playing').removeClass('yrc-playing');
		to_play.addClass('yrc-playing');
			
		$('.yrc-player-bar .yrc-sub-section-name').html( YRC.template.playerTop(to_play, yc.data.style.player_top)[1] );		
		yc.player.player.loadVideoById( to_play.data('video') );
	}
	
	YRC.EM.on('yrc.player_state_change', function(e, p){
		if(p[1].data === 0 && p[0].data.style.autoplay_next){
			yrcPlayNext(p[0], true);
		}
	});
			
	YRCPRO.ratings = {
		'arc': function(end){
			var x1,x2,y1,y2 ;
			var startAngle = 0;
			var endAngle = end || 0;
			x1 = parseInt(Math.round(200 + 195*Math.cos(Math.PI*startAngle/180)));
			y1 = parseInt(Math.round(200 + 195*Math.sin(Math.PI*startAngle/180)));

			x2 = parseInt(Math.round(200 + 195*Math.cos(Math.PI*endAngle/180)));
			y2 = parseInt(Math.round(200 + 195*Math.sin(Math.PI*endAngle/180)));
			
			var d = "M200,200  L" + x1 + "," + y1 + "  A195,195 0 " + 
					((endAngle-startAngle > 180) ? 1 : 0) + ",1 " + x2 + "," + y2 + " z";
			return d;		
		},
	
		'draw': function(likes, norating, bar){
			var r_bg = {
				'c': (norating ? this.colors.neutral:(likes === 360 ? this.colors.like : this.colors.dislike)),
				'cl': ( norating ? 'yrc-neutral':(likes === 360 ? 'yrc-like' : 'yrc-dislike'))
			};
			if(bar) return '<div class="'+r_bg.cl+' yrc-bar-bg" style="background:'+r_bg.c+'">\
				<div class="yrc-like" style="width:'+((likes/360)*100)+'%;background:'+ this.colors.like +'"></div></div>';
			
			var xmlns = "http://www.w3.org/2000/svg";
			var svg = document.createElementNS(xmlns, 'svg');
				svg.setAttributeNS("http://www.w3.org/2000/xmlns/", "xmlns:xlink", "http://www.w3.org/1999/xlink");
				svg.setAttribute('viewBox', '0 0 400 400');
				svg.setAttribute('width', '40px');
				svg.setAttribute('height', '40px');
				
			var pie = document.createElementNS(xmlns, 'circle');
				pie.setAttribute('r', 190);
				pie.setAttribute('cx', 200);
				pie.setAttribute('cy', 200);
				pie.setAttribute('class', r_bg.cl);
				pie.setAttribute('style', 'fill:'+r_bg.c);
				
			var path = document.createElementNS(xmlns, 'path');
				path.setAttribute('style', 'fill:'+this.colors.like);
				path.setAttribute('d', this.arc(likes));
				path.setAttribute('class', 'yrc-like');
			svg.appendChild(pie);
			svg.appendChild(path);
			return svg;
		},
		
		'get': function( opts ){
			if(opts[2].data.meta.custom){
				this.rate(opts, {'items':opts[1]});
				return false;
			}
			var ids = [];
			opts[1].forEach(function(v){ ids.push( (v.kind === 'youtube#playlistItem') ? v.snippet.resourceId.videoId : v.id.videoId ); });
			var u = 'videos?part=contentDetails,statistics&id=' + ids.join(','), r = this;
			$.get(YRC.auth.baseUrl(u), function(re){ r.rate( opts, re); });
		},
		
		'rerate': function(item){
			return {
				'stats': item.statistics, 'content': item.contentDetails, 'snippet': item.snippet || {},
				'likes' : ((item.statistics.likeCount * 1) * 360) / ((item.statistics.likeCount * 1) + (item.statistics.dislikeCount * 1))
			};	
		},
	
		'rate': function(opts, re){
			var rates = {}, r = this;
			re.items.forEach(function(item){
				rates[item.id] = r.rerate( item );
			});
						
			var srt = opts[2].uploads ? opts[2].uploads.criteria : 'date', i;
			if((opts[2].onlyonce || opts[2].data.meta.playlist || opts[2].data.meta.custom) && opts[3] && (srt !== 'none') && (opts[2].data.meta.temp_sort !== 'none')){
				opts[1].sort(function(v1, v2){
					v1 = rates[v1.snippet.resourceId.videoId];
					v2 = rates[v2.snippet.resourceId.videoId];
					if(v1 && v2){
						i =  srt === 'viewCount' ? ( parseInt(v1.stats.viewCount) < parseInt(v2.stats.viewCount) )
							: ( zero4NaN(v1.likes) < zero4NaN(v2.likes) );
						return i ? 1 : -1;	
					} return 0;
				});
				opts[2].lstVideos(opts[1], opts[0], true);
			}
			
			r.appendMeta(opts[0].find('.yrc-video.yrc-just-listed'), rates);
		},
		
		'appendMeta': function(vids, rates){
			var r = this, bar = !r.style;
			
			vids.each(function(){
				var el = $(this), vid = rates[ el.data('video') ];
				if(vid){
					if(r.show){
						el.find(bar ? '.yrc-thumb' : '.yrc-item-meta')
							.append('<div class="pb-inline yrc-ratings '+(bar ? 'yrc-rate-bar' : 'yrc-rate-pie')+'"></div>');
						el.find('.yrc-ratings').append(r.draw(vid.likes, isNaN(vid.likes), bar));
					}
					el.find('.yrc-video-views').text(function(){
						return YRC.template.num( vid.stats.viewCount ) + ' ' +YRC.lang.fui.views.toLowerCase();
					});
					el.find('.yrc-thumb').append('<span class="pb-inline pb-absolute yrc-video-duration">'+ r.duration(vid.content.duration) +'</span>');
				}
				el.removeClass('yrc-just-listed');
			});
		},
		
		'duration': function( duration ){
			var matches = duration.match(/[0-9]+[HMS]/g);
			var dur = [0,0,0];
			
			matches.forEach(function (part) {
				var unit = part.charAt(part.length-1);
				var amount = parseInt(part.slice(0,-1));

				switch (unit) {
					case 'H':
						dur[0] = amount;
						break;
					case 'M':
						dur[1] = amount;
						break;
					case 'S':
						dur[2] = (amount < 10) ? ('0'+amount) : amount;
						break;
					default:
				}
			});
			if(!dur[0]) dur.splice(0, 1);
			else dur[1] = (dur[1] < 10) ? ('0'+dur[1]) : dur[1];
			return dur.join(':');
		}	
	};	
		
	YRC.searchUrl = function( page, res_id, search, limit){
		return 'search?order='+ search.order +'&publishedAfter='+ search.after
			+'&publishedBefore='+ search.before +'&q='+ search.term +'&part=snippet'+(res_id ? ('&channelId='+res_id) : '')
			+'&videoDuration='+ search.duration +'&type=video&pageToken='+page+'&maxResults='+limit;
	};		
	
	function addSubscribeButton(){
		var gp = document.createElement('script');
			gp.setAttribute('data-cfasync', false);
			gp.setAttribute('type', 'text/javascript');
			gp.src = 'https://apis.google.com/js/platform.js';
		$('head').append(gp);
	}
	
	function sorter(sel, crit){
		var vid_menu = $(sel +' .yrc-menu-items li[data-section=uploads]');
		if(!vid_menu.length) return false;
		$(sel +' .yrc-menu').append( uploadSorter() );
		vid_menu.append('<span class="yrc-sort-trigger"></span>');
		$(sel+' .yrc-sort-uploads li[data-value='+(crit||'viewCount')+']').addClass('yrc-active');
	}
	
	function zero4NaN(num){
		return isNaN(num) ? 0 : num;
	}
	
	YRC.template.social = {};
	YRC.template.social.facebook = '<svg class="yrc-icon-facebook" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" style="height: 100px; width: 100px;"><rect class="outer-shape" x="0" y="0" width="100" height="100" style="opacity: 1; fill: rgb(0, 108, 175);"></rect><path class="inner-shape" style="opacity: 1; fill: rgb(247, 247, 247);" transform="translate(25,25) scale(0.5)" d="M82.667,1H17.335C8.351,1,1,8.351,1,17.336v65.329c0,8.99,7.351,16.335,16.334,16.335h65.332 C91.652,99.001,99,91.655,99,82.665V17.337C99,8.353,91.652,1.001,82.667,1L82.667,1z M84.318,50H68.375v42.875H50V50h-8.855V35.973 H50v-9.11c0-12.378,5.339-19.739,19.894-19.739h16.772V22.3H72.967c-4.066-0.007-4.57,2.12-4.57,6.078l-0.023,7.594H86.75 l-2.431,14.027V50z"></path></svg>';
	YRC.template.social.googleplus = '<svg class="yrc-icon-googleplus" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" style="height: 100px; width: 100px;"><rect class="outer-shape" x="0" y="0" width="100" height="100" style="opacity: 1; fill: rgb(72, 131, 0);"></rect><path class="inner-shape" style="opacity: 1; fill: rgb(247, 247, 247);" transform="translate(25,25) scale(0.5)" d="M1.079,84.227c-0.024-0.242-0.043-0.485-0.056-0.73C1.036,83.742,1.055,83.985,1.079,84.227z M23.578,55.086 c8.805,0.262,14.712-8.871,13.193-20.402c-1.521-11.53-9.895-20.783-18.701-21.046C9.264,13.376,3.357,22.2,4.878,33.734 C6.398,45.262,14.769,54.823,23.578,55.086z M98.999,25.501v-8.164c0-8.984-7.348-16.335-16.332-16.335H17.336 c-8.831,0-16.078,7.104-16.323,15.879c5.585-4.917,13.333-9.026,21.329-9.026c8.546,0,34.188,0,34.188,0l-7.651,6.471H38.039 c7.19,2.757,11.021,11.113,11.021,19.687c0,7.201-4.001,13.393-9.655,17.797c-5.516,4.297-6.562,6.096-6.562,9.749 c0,3.117,5.909,8.422,8.999,10.602c9.032,6.368,11.955,12.279,11.955,22.15c0,1.572-0.195,3.142-0.58,4.685h29.451 C91.652,98.996,99,91.651,99,82.661V31.625H80.626v18.374h-6.125V31.625H56.127V25.5h18.374V7.127h6.125V25.5H99L98.999,25.501z M18.791,74.301c2.069,0,3.964-0.057,5.927-0.057c-2.598-2.52-4.654-5.608-4.654-9.414c0-2.259,0.724-4.434,1.736-6.366 c-1.032,0.073-2.085,0.095-3.17,0.095c-7.116,0-13.159-2.304-17.629-6.111v6.435l0.001,19.305 C6.116,75.76,12.188,74.301,18.791,74.301L18.791,74.301z M1.329,85.911c-0.107-0.522-0.188-1.053-0.243-1.591 C1.141,84.858,1.223,85.389,1.329,85.911z M44.589,92.187c-1.442-5.628-6.551-8.418-13.675-13.357 c-2.591-0.836-5.445-1.328-8.507-1.36c-8.577-0.092-16.566,3.344-21.074,8.457c1.524,7.436,8.138,13.068,16.004,13.068h27.413 c0.173-1.065,0.258-2.166,0.258-3.295C45.007,94.502,44.86,93.329,44.589,92.187z"></path></svg>';
	YRC.template.social.instagram = '<svg id="instagram" class="custom-icon" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" style="height: 100px; width: 100px;"><rect class="outer-shape" x="0" y="0" width="100" height="100" style="opacity: 1; fill: rgb(72, 131, 0);"></rect><path class="inner-shape" style="opacity: 1; fill: rgb(247, 247, 247);" transform="translate(25,25) scale(0.5)" d="M88.2,1H11.8C5.85,1,1.026,5.827,1.026,11.781V88.22C1.026,94.174,5.85,99,11.8,99H88.2c5.95,0,10.774-4.826,10.774-10.78 V11.781C98.973,5.827,94.149,1,88.2,1z M49.946,31.184c10.356,0,18.752,8.4,18.752,18.762c0,10.361-8.396,18.761-18.752,18.761 s-18.752-8.4-18.752-18.761S39.589,31.184,49.946,31.184z M87.513,83.615c0,2.165-1.753,3.919-3.917,3.919H16.341 c-2.164,0-3.917-1.755-3.917-3.919v-41.06h8.508c-0.589,2.35-0.904,4.807-0.904,7.34c0,16.612,13.459,30.079,30.063,30.079 s30.063-13.466,30.063-30.079c0-2.533-0.315-4.99-0.904-7.34h8.263L87.513,83.615L87.513,83.615z M87.764,27.124 c0,2.165-1.754,3.919-3.918,3.919H72.723c-2.164,0-3.917-1.755-3.917-3.919v-11.13c0-2.165,1.754-3.919,3.917-3.919h11.123 c2.165,0,3.918,1.755,3.918,3.919V27.124z"></path></svg>';
	YRC.template.social.flicker = '<svg class="yrc-icon-flicker" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" style="height: 100px; width: 100px;"><rect class="outer-shape" x="0" y="0" width="100" height="100" style="opacity: 1; fill: rgb(0, 108, 175);"></rect><path class="inner-shape" style="opacity: 1; fill: rgb(247, 247, 247);" transform="translate(25,25) scale(0.5)" d="M1,50c0-11.839,9.598-21.438,21.438-21.438S43.875,38.161,43.875,50s-9.598,21.438-21.438,21.438S1,61.839,1,50z M56.125,50 c0-11.839,9.598-21.438,21.438-21.438S99,38.161,99,50s-9.598,21.438-21.438,21.438S56.125,61.839,56.125,50z"></path></svg>';
	YRC.template.social.lastfm = '<svg class="yrc-icon-lastfm" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" style="height: 100px; width: 100px;"><rect class="outer-shape" x="0" y="0" width="100" height="100" style="opacity: 1; fill: rgb(0, 108, 175);"></rect><path class="inner-shape" style="opacity: 1; fill: rgb(247, 247, 247);" transform="translate(25,25) scale(0.5)" d="M79.436,78.867c-10.209-0.04-15.648-5.032-19.426-13.83l-1.081-2.4L49.61,41.391c-3.091-7.52-10.759-12.602-19.412-12.602 c-11.708,0-21.206,9.499-21.206,21.215s9.498,21.214,21.206,21.214c8.167,0,15.268-4.618,18.813-11.388l3.768,8.693 c-5.351,6.524-13.482,10.679-22.581,10.679C14.072,79.201,1,66.137,1,50.003c0-16.126,13.071-29.206,29.197-29.206 c12.171,0,21.996,6.5,26.988,18.016c0.378,0.894,5.279,12.354,9.554,22.108c2.648,6.037,4.906,10.041,12.226,10.288 c7.194,0.247,12.131-4.132,12.131-9.667c0-5.408-3.764-6.707-10.12-8.82c-11.425-3.764-17.322-7.545-17.322-16.605 c0-8.837,6.013-14.73,15.783-14.73c6.364,0,10.958,2.831,14.141,8.478l-6.245,3.19c-2.353-3.294-4.949-4.594-8.247-4.594 c-4.585,0-7.86,3.19-7.86,7.433c0,6.022,5.387,6.93,12.92,9.403C94.287,48.599,99,52.372,99,61.79 C99,71.679,90.515,78.881,79.436,78.867L79.436,78.867z"></path></svg>';
	YRC.template.social.pinterest = '<svg class="yrc-icon-pinterest" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" style="height: 100px; width: 100px;"><rect class="outer-shape" x="0" y="0" width="100" height="100" style="opacity: 1; fill: rgb(0, 108, 175);"></rect><path class="inner-shape" style="opacity: 1; fill: rgb(247, 247, 247);" transform="translate(25,25) scale(0.5)" d="M50.001,1.489C22.94,1.489,0.999,23.208,0.999,50c0,19.864,12.066,36.929,29.331,44.432 c-0.138-3.388-0.024-7.454,0.853-11.14c0.942-3.939,6.305-26.433,6.305-26.433s-1.566-3.098-1.566-7.676 c0-7.188,4.209-12.558,9.451-12.558c4.457,0,6.61,3.314,6.61,7.283c0,4.435-2.858,11.071-4.327,17.215 c-1.228,5.146,2.606,9.343,7.734,9.343c9.285,0,15.537-11.805,15.537-25.791c0-10.633-7.233-18.59-20.39-18.59 c-14.864,0-24.124,10.974-24.124,23.232c0,4.226,1.258,7.207,3.23,9.515c0.906,1.06,1.032,1.488,0.704,2.704 c-0.235,0.893-0.775,3.042-0.999,3.894c-0.326,1.229-1.332,1.668-2.453,1.214C20.05,63.877,16.861,56.455,16.861,48.11 c0-13.781,11.739-30.304,35.022-30.304c18.708,0,31.021,13.403,31.021,27.79c0,19.029-10.687,33.247-26.44,33.247 c-5.289,0-10.266-2.831-11.971-6.047c0,0-2.845,11.177-3.448,13.335c-1.039,3.74-3.072,7.478-4.932,10.392 C40.52,97.81,45.175,98.511,50,98.511c27.059,0,49-21.719,49-48.511S77.06,1.489,50.001,1.489z"></path></svg>';
	YRC.template.social.soundcloud = '<svg class="yrc-icon-soundcloud" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" style="height: 100px; width: 100px;"><rect class="outer-shape" x="0" y="0" width="100" height="100" style="opacity: 1; fill: rgb(0, 108, 175);"></rect><path class="inner-shape" style="opacity: 1; fill: rgb(247, 247, 247);" transform="translate(25,25) scale(0.5)" d="M17.378,49.398c-0.354,0-0.637,0.281-0.659,0.655l-0.815,10.052l0.815,10.57c0.023,0.372,0.305,0.654,0.659,0.654 c0.349,0,0.631-0.282,0.658-0.653l0.925-10.571l-0.925-10.056C18.01,49.677,17.728,49.398,17.378,49.398z M25.299,39.191 c-0.444,0-0.807,0.363-0.827,0.826c0,0.001-0.716,20.094-0.716,20.094l0.718,10.504c0.019,0.459,0.381,0.822,0.825,0.822 c0.44,0,0.804-0.363,0.826-0.827l0.809-10.499l-0.81-20.094C26.101,39.552,25.74,39.191,25.299,39.191z M13.465,48.697 c-0.304,0-0.552,0.246-0.577,0.572l-0.864,10.837l0.864,10.484c0.024,0.324,0.272,0.569,0.577,0.569 c0.299,0,0.548-0.245,0.575-0.569l0.982-10.484l-0.982-10.839C14.012,48.942,13.765,48.697,13.465,48.697z M21.321,43.012 c-0.395,0-0.722,0.327-0.74,0.743l-0.767,16.354l0.767,10.569c0.02,0.413,0.345,0.739,0.74,0.739c0.394,0,0.72-0.327,0.743-0.741 v0.003l0.868-10.57l-0.869-16.354C22.041,43.339,21.714,43.012,21.321,43.012z M9.583,49.071c-0.258,0-0.464,0.204-0.491,0.485 c0,0.001-0.915,10.547-0.915,10.547l0.915,10.161c0.026,0.282,0.232,0.486,0.491,0.486c0.255,0,0.462-0.203,0.491-0.484 l1.039-10.163l-1.038-10.547C10.044,49.273,9.838,49.071,9.583,49.071z M5.733,50.813c-0.208,0-0.38,0.168-0.409,0.399 c0,0.003-0.963,8.889-0.963,8.889l0.963,8.691c0.028,0.233,0.2,0.402,0.409,0.402c0.205,0,0.373-0.164,0.407-0.399l1.096-8.694 l-1.095-8.889C6.107,50.976,5.939,50.813,5.733,50.813z M2.11,54.216c-0.205,0-0.367,0.159-0.394,0.388L1,60.101l0.717,5.403 c0.027,0.229,0.188,0.388,0.394,0.388c0.199,0,0.359-0.158,0.392-0.386l0.849-5.405l-0.849-5.499 C2.471,54.375,2.308,54.216,2.11,54.216z M49.767,31.36c-0.724,0-1.319,0.6-1.329,1.338l-0.544,27.426l0.545,9.954 c0.009,0.727,0.604,1.326,1.328,1.326c0.723,0,1.316-0.599,1.326-1.337v0.012l0.592-9.955l-0.592-27.428 C51.083,31.961,50.49,31.36,49.767,31.36z M86.948,47.141c-1.652,0-3.229,0.338-4.663,0.945c-0.96-10.973-10.071-19.58-21.182-19.58 c-2.719,0-5.369,0.541-7.71,1.455c-0.91,0.356-1.151,0.723-1.161,1.431v38.641c0.011,0.744,0.582,1.365,1.303,1.438 c0.03,0.003,33.196,0.021,33.411,0.021c6.657,0,12.054-5.45,12.054-12.176C99,52.593,93.605,47.141,86.948,47.141z M45.653,33.722 c-0.677,0-1.235,0.561-1.244,1.252l-0.468,25.136c0,0.017,0.468,10.109,0.468,10.109c0.01,0.684,0.57,1.245,1.244,1.245 c0.674,0,1.231-0.562,1.242-1.252l0.525-10.092l-0.525-25.147C46.885,34.285,46.327,33.722,45.653,33.722z M29.307,37.431 c-0.494,0-0.894,0.4-0.91,0.911c0,0.001-0.668,21.771-0.668,21.771l0.668,10.394c0.016,0.508,0.414,0.907,0.909,0.907 c0.49,0,0.89-0.399,0.909-0.91l0.752-10.391L30.216,38.34C30.196,37.83,29.797,37.431,29.307,37.431z M33.346,36.61 c-0.541,0-0.978,0.437-0.992,0.998l-0.618,22.506l0.619,10.331c0.014,0.555,0.451,0.992,0.991,0.992c0.54,0,0.973-0.437,0.992-0.994 v-0.002l0.696-10.325l-0.696-22.508C34.321,37.049,33.884,36.61,33.346,36.61z M41.518,37.82c-0.637,0-1.148,0.513-1.161,1.168 l-0.518,21.131l0.518,10.168c0.013,0.646,0.523,1.159,1.161,1.159c0.637,0,1.144-0.511,1.16-1.166v0.008L43.26,60.12l-0.581-21.134 C42.662,38.332,42.155,37.82,41.518,37.82z M37.416,37.104c-0.59,0-1.065,0.475-1.077,1.081l-0.568,21.933l0.569,10.23 c0.013,0.6,0.487,1.077,1.077,1.077c0.586,0,1.06-0.476,1.076-1.083v0.009l0.638-10.235l-0.639-21.931 C38.476,37.578,38.002,37.104,37.416,37.104z"></path></svg>';
	YRC.template.social.tumblr = '<svg class="yrc-icon-tumblr" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" style="height: 100px; width: 100px;"><rect class="outer-shape" x="0" y="0" width="100" height="100" style="opacity: 1; fill: rgb(0, 108, 175);"></rect><path class="inner-shape" style="opacity: 1; fill: rgb(247, 247, 247);" transform="translate(25,25) scale(0.5)" d="M82.671,1H17.336C8.351,1,1,8.351,1,17.336v65.329c0,8.99,7.351,16.335,16.335,16.335h65.335 C91.654,99.001,99,91.655,99,82.665V17.336C98.999,8.352,91.654,1.001,82.671,1L82.671,1z M71.028,79.925 c-2.886,1.358-5.506,2.315-7.848,2.865c-2.344,0.545-4.877,0.819-7.599,0.819c-3.092,0-4.917-0.388-7.291-1.166 c-2.375-0.784-4.402-1.902-6.077-3.337c-1.681-1.447-2.841-2.986-3.49-4.612c-0.65-1.628-0.972-3.99-0.972-7.082V43.698h-9.187 v-9.576c2.655-0.861,5.736-2.099,7.626-3.708c1.899-1.615,3.418-3.547,4.564-5.807c1.149-2.255,1.938-5.132,2.369-8.62h9.618v15.642 h15.635v12.071H52.739v17.34c0,3.924-0.051,6.185,0.366,7.297c0.413,1.106,1.447,2.255,2.574,2.919 c1.498,0.898,3.207,1.346,5.132,1.346c3.425,0,6.832-1.112,10.216-3.338v10.665V79.925z"></path></svg>';
	YRC.template.social.twitter = '<svg class="yrc-icon-twitter" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" style="height: 100px; width: 100px;"><rect class="outer-shape" x="0" y="0" width="100" height="100" style="opacity: 1; fill: rgb(0, 108, 175);"></rect><path class="inner-shape" style="opacity: 1; fill: rgb(247, 247, 247);" transform="translate(25,25) scale(0.5)" d="M99.001,19.428c-3.606,1.608-7.48,2.695-11.547,3.184c4.15-2.503,7.338-6.466,8.841-11.189 c-3.885,2.318-8.187,4-12.768,4.908c-3.667-3.931-8.893-6.387-14.676-6.387c-11.104,0-20.107,9.054-20.107,20.223 c0,1.585,0.177,3.128,0.52,4.609c-16.71-0.845-31.525-8.895-41.442-21.131C6.092,16.633,5.1,20.107,5.1,23.813 c0,7.017,3.55,13.208,8.945,16.834c-3.296-0.104-6.397-1.014-9.106-2.529c-0.002,0.085-0.002,0.17-0.002,0.255 c0,9.799,6.931,17.972,16.129,19.831c-1.688,0.463-3.463,0.71-5.297,0.71c-1.296,0-2.555-0.127-3.783-0.363 c2.559,8.034,9.984,13.882,18.782,14.045c-6.881,5.424-15.551,8.657-24.971,8.657c-1.623,0-3.223-0.096-4.796-0.282 c8.898,5.738,19.467,9.087,30.82,9.087c36.982,0,57.206-30.817,57.206-57.543c0-0.877-0.02-1.748-0.059-2.617 C92.896,27.045,96.305,23.482,99.001,19.428z"></path></svg>';
	YRC.template.social.vimeo = '<svg class="yrc-icon-vimeo" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" style="height: 100px; width: 100px;"><rect class="outer-shape" x="0" y="0" width="100" height="100" style="opacity: 1; fill: rgb(0, 108, 175);"></rect><path class="inner-shape" style="opacity: 1; fill: rgb(247, 247, 247);" transform="translate(25,25) scale(0.5)" d="M1,30.512l3.981,5.22c0,0,8.208-6.47,10.945-3.235c2.736,3.235,13.182,42.297,16.669,49.501 c3.043,6.319,11.438,14.673,20.645,8.707c9.2-5.966,39.797-32.088,45.274-62.935c5.472-30.837-36.818-24.378-41.299,2.489 c11.196-6.722,17.172,2.731,11.443,13.434C62.938,54.386,57.713,61.36,54.976,61.36c-2.731,0-4.832-7.156-7.961-19.662 c-3.235-12.93-3.214-36.22-16.664-33.579C17.669,10.608,1,30.512,1,30.512L1,30.512z"></path></svg>';
	YRC.template.social.xing = '<svg class="yrc-icon-xing" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" style="height: 100px; width: 100px;"><rect class="outer-shape" x="0" y="0" width="100" height="100" style="opacity: 1; fill: rgb(0, 108, 175);"></rect><path class="inner-shape" style="opacity: 1; fill: rgb(247, 247, 247);" transform="translate(25,25) scale(0.5)" d="M37.277,57.478L18.19,46.249c-0.019-0.012-0.042-0.015-0.068-0.013l0.008-0.018l-0.037,0.023 c-0.068,0.015-0.15,0.067-0.238,0.149L2.002,56.358l7.483,4.514c0.011,0.037,0.026,0.068,0.054,0.084l11.001,6.471 c-0.054,0.11-0.087,0.232-0.087,0.363V99l16.168-8.547c0.175-0.03,0.331-0.115,0.449-0.237l0.028-0.015l-0.012-0.004 c0.135-0.148,0.22-0.344,0.22-0.561V67.79c0-0.064-0.008-0.125-0.022-0.186l0.02-9.727C37.361,57.679,37.356,57.525,37.277,57.478z M97.998,88.962l-11.505-6.99l-25.467-15.77V1L44.381,9.799l0.062,0.022c-0.282,0.133-0.478,0.417-0.478,0.749v64.502 c0,0.415,0.305,0.756,0.703,0.819l-0.218,0.013l6.355,3.972c0.023,0.054,0.057,0.099,0.102,0.127l10.927,6.766l3.146,1.965 l-0.016-0.027l15.255,9.446c0.116,0.072,0.28,0.035,0.445-0.075l-0.018,0.209L97.998,88.962z"></path></svg>';
	YRC.template.social.twitch = '<svg class="yrc-icon-twitch" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 115 115" style="height: 100px; width: 100px;"><g transform="matrix(1.25,0,0,-1.25,-760.56837,2110.8162)"><g transform="matrix(9.5324427,0,0,9.5324427,1348.9493,1188.2344)"><path fill="#FFF" d="M-70.567,47.182l-0.848-0.848h-1.332l-0.727-0.726v0.726 h-1.09v3.512h3.996V47.182z M-75.048,50.33l-0.242-0.969v-4.359h1.09v-0.606h0.605l0.606,0.606h0.969l1.938,1.937v3.391H-75.048 L-75.048,50.33z"></path></g><path fill="#FFF" d="M650.88,1640.297h4.618v13.855h-4.618V1640.297z M663.577,1640.297h4.617v13.855h-4.617V1640.297z"></path></g></svg>';
	YRC.template.social.steam = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 612 254.294" enable-background="new 0 0 612 254.294" xml:space="preserve" style="padding-left:5px;"><path d="M429.718,36.571c25.258,0,45.717,20.47,45.717,45.717s-20.459,45.724-45.717,45.724c-25.241,0-45.701-20.477-45.701-45.724  S404.477,36.571,429.718,36.571z M64,18.288c35.347,0,64,28.653,64,64l-0.344,6.711l135.455,70.254  c10.737-8.144,24.113-12.971,38.617-12.971l45.701-63.994C347.43,36.841,384.276,0,429.718,0C475.164,0,512,36.841,512,82.288  c0,45.452-36.836,82.288-82.282,82.288L365.723,210.3c0,35.347-28.646,63.994-63.994,63.994c-35.357,0-64.011-28.647-64.011-63.994  c0-0.812,0.017-1.602,0.045-2.402L98.952,135.906C88.903,142.471,76.897,146.282,64,146.282c-35.347,0-64-28.647-64-63.994  S28.653,18.288,64,18.288z M320.711,189.118c13.5,6.88,18.868,23.392,11.988,36.893c-6.88,13.5-23.392,18.868-36.892,11.988  l-39.21-20.335C260.122,239.409,278.991,256,301.729,256c25.241,0,45.701-20.459,45.701-45.7c0-25.253-20.46-45.724-45.701-45.724  c-7.071,0-13.76,1.602-19.737,4.467L320.711,189.118z M64,36.571c-25.247,0-45.717,20.47-45.717,45.717S38.753,128.012,64,128.012  c5.222,0,10.235-0.88,14.91-2.493L48.56,109.779c-13.5-6.88-18.869-23.397-11.989-36.892c6.879-13.501,23.391-18.863,36.892-11.989  l36.176,18.762C108.279,55.637,88.367,36.571,64,36.571z M429.718,18.288c-35.347,0-63.995,28.653-63.995,64  s28.648,63.994,63.995,63.994c35.341,0,64.011-28.647,64.011-63.994S465.059,18.288,429.718,18.288z" fill="#ffffff"/></svg>';
	
	YRC.template.sicon = '<svg height="40" version="1.1" width="40" xmlns="http://www.w3.org/2000/svg" style="overflow: hidden;"><path fill="none" stroke="#ffffff" d="M29.772,26.433L22.645999999999997,19.307C23.605999999999998,17.724,24.168999999999997,15.871999999999998,24.169999999999998,13.886C24.169,8.093,19.478,3.401,13.688,3.399C7.897,3.401,3.204,8.093,3.204,13.885C3.204,19.674,7.897,24.366,13.688,24.366C15.675,24.366,17.527,23.803,19.11,22.843L26.238,29.97L29.772,26.433ZM7.203,13.885C7.2090000000000005,10.303,10.106,7.407,13.687000000000001,7.399C17.266000000000002,7.407,20.165,10.303,20.171,13.885C20.163999999999998,17.465,17.266,20.361,13.687,20.369C10.106,20.361,7.209,17.465,7.203,13.885Z" stroke-width="3" stroke-linejoin="round" opacity="0" transform="matrix(1,0,0,1,4,4)" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); stroke-linejoin: round; opacity: 0;"></path><path fill="#333333" stroke="none" d="M29.772,26.433L22.645999999999997,19.307C23.605999999999998,17.724,24.168999999999997,15.871999999999998,24.169999999999998,13.886C24.169,8.093,19.478,3.401,13.688,3.399C7.897,3.401,3.204,8.093,3.204,13.885C3.204,19.674,7.897,24.366,13.688,24.366C15.675,24.366,17.527,23.803,19.11,22.843L26.238,29.97L29.772,26.433ZM7.203,13.885C7.2090000000000005,10.303,10.106,7.407,13.687000000000001,7.399C17.266000000000002,7.407,20.165,10.303,20.171,13.885C20.163999999999998,17.465,17.266,20.361,13.687,20.369C10.106,20.361,7.209,17.465,7.203,13.885Z" transform="matrix(1,0,0,1,4,4)" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><rect x="0" y="0" width="32" height="32" r="0" rx="0" ry="0" fill="#000000" stroke="#000" opacity="0" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); opacity: 0;"></rect></svg>';
	YRC.template.topSearch = function(){ return '<div class="yrc-search-form-top"><form class="yrc-search-form-el pb-relative" data-top="1"><input  type="text" placeholder="'+YRC.lang.form.Search+'.." class="yrc-search-term"/><button class="pb-absolute">'+YRC.template.sicon+'</button></form></div>'; };
});	


var YRC = YRC || {};
jQuery(document).ready(function($){
	window.YD && window.YD.version();
	YRC.EM = YRC.EM || $({});
	YRC.template = YRC.template || {};
	YRC.counter = 0;
	
	function yrcStyle( sel, data ){
		var colors = data.style.colors;
		var css = 
			sel+' li.yrc-active{\
				border-bottom: 3px solid '+ colors.button.background +';\
			}\
			'+ sel +' .yrc-menu li{\
				color:'+ colors.color.link +';\
			}\
			'+ sel +' .yrc-item{\
				margin-bottom:'+ (parseInt(data.style.thumb_margin)||8) +'px;\
			}\
			'+ sel +' .yrc-video, '+ sel +' .yrc-brand, .yrc-placeholder-item, '+ sel +' .yrc-playlist-item{\
				background: '+ colors.item.background +';\
			}\
			'+ sel +' .yrc-section-action, '+ sel +' .yrc-section-action, '+ sel +' .yrc-load-more-button, .yrc-search button, .yrc-player-bar, .yrc-player-bar span, .yrc-search-form-top button{\
				background: '+ colors.button.background +';\
				color: '+ colors.button.color +';\
				border:none;\
			}\
			'+ sel +' .yrc-section-action a{\
				color: '+ colors.button.color +';\
			}\
			.yrc-player-bar .yrc-close span{\
				color: '+ colors.button.background +';\
				background: '+ colors.button.color +';\
			}\
			'+ sel +' .yrc-brand{\
				color: '+ colors.color.text +';\
			}\
			'+ sel +' .yrc-search-form-top svg path{\
				fill: '+ colors.button.color +';\
			}\
			.yrc-loading-overlay:after{ content: "'+ YRC.lang.form.Loading +'..."; }\
			'+ sel +' .yrc-stats svg .yrc-stat-icon{\
				fill: '+ colors.color.text +'\
			}\
			'+ sel +' a, '+ sel +' .yrc-playlist-item { color: '+ colors.color.link +'; }\
			'+ sel +' .yrc-item-title { height: '+ (data.style.truncate ? '1.5em' : 'auto') +'; }';
			
			if(data.style.play_icon === 'all') css += '.yrc-item .yrc-thumb:before{ content:""; }';
			if(data.style.play_icon === 'hover') css += '.yrc-item:hover .yrc-thumb:before{ content:""; }';
			
		$('head').append('<style class="yrc-stylesheet">'+ css + '</style>');
		YRC.EM.trigger('yrc.style', [[sel, data]]);
	}

	function miti(stamp){
		stamp = +new Date - stamp;
		var days = Math.round( Math.floor( ( stamp/60000 )/60 )/24 );
		if(days < 7)
			stamp = days + ' ' + (days > 1 ? YRC.lang.fui.days : YRC.lang.fui.day);
		else if( Math.round(days/7) < 9)
			stamp = Math.round(days/7) + ' ' + (Math.round(days/7) > 1 ? YRC.lang.fui.weeks : YRC.lang.fui.week);
		else if( Math.round(days/30) < 12)
			stamp = Math.round(days/30) + ' ' + (Math.round(days/30) > 1 ? YRC.lang.fui.months : YRC.lang.fui.month);
		else stamp = Math.round( days/365 ) + ' ' + (Math.round(days/365) > 1 ? YRC.lang.fui.years : YRC.lang.fui.year);	
		stamp = (stamp === ('0 '+YRC.lang.fui.day)) ? YRC.lang.fui.today : stamp;
		if(stamp === YRC.lang.fui.today) return stamp;
		return (YRC.lang.fui.wplocale === 'de_DE') ? (YRC.lang.fui.ago + ' ' + stamp) : (stamp + ' ' + YRC.lang.fui.ago);
	}	
	
	window.onYouTubeIframeAPIReady = function() {console.log('YRC_API_LOADED');YRC.EM.trigger('yrc.api_loaded');};
	function loadYouTubeAPI(){
		var tag = document.createElement('script');
			tag.innerHTML = "if (!window['YT']) {var YT = {loading: 0,loaded: 0};}if (!window['YTConfig']) {var YTConfig = {'host': 'http://www.youtube.com'};}if (!YT.loading) {YT.loading = 1;(function(){var l = [];YT.ready = function(f) {if (YT.loaded) {f();} else {l.push(f);}};window.onYTReady = function() {YT.loaded = 1;for (var i = 0; i < l.length; i++) {try {l[i]();} catch (e) {}}};YT.setConfig = function(c) {for (var k in c) {if (c.hasOwnProperty(k)) {YTConfig[k] = c[k];}}};var a = document.createElement('script');a.type = 'text/javascript';a.id = 'www-widgetapi-script';a.src = 'https:' + '//s.ytimg.com/yts/jsbin/www-widgetapi-vflYlgBFi/www-widgetapi.js';a.async = true;var b = document.getElementsByTagName('script')[0];b.parentNode.insertBefore(a, b);})();}";
		var firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
	}	
	loadYouTubeAPI();
		
	var watch_video = 'https://www.youtube.com/watch?v=';
	
	YRC.auth = {
		//'apikey': 'AIzaSyBHM34vx2jpa91sv4fk8VzaEHJbeL5UuZk',
		'baseUrl': function ( rl ){ return 'https://www.googleapis.com/youtube/v3/' + rl +'&key=' + this.apikey; },
		
		'url': function uuu(type, page, res, search, limit, vst){
			var url = '';
			switch(type){
				case 'Playlist':
					url = this.baseUrl('playlistItems?part=snippet%2C+contentDetails&maxResults='+limit+'&pageToken='+page+'&playlistId='+res);
				break;
				case 'Uploads':
					url = this.baseUrl('search?order='+ ((search || 'viewCount')) +'&q='+(vst.t)+'&part=snippet'+( vst.own ? ('&channelId='+ res) : '') +'&type=video&pageToken='+page+'&maxResults='+limit);
				break;
				case 'channel':
					url = this.baseUrl('channels?part=contentDetails,snippet,statistics,brandingSettings&id='+ res);
				break;
				case 'Playlists':
					url = this.baseUrl('playlists?part=snippet,status,contentDetails&channelId='+ res +'&pageToken='+page+'&maxResults='+limit);
				break;
				case 'Search':
					url = this.baseUrl( YRC.searchUrl( page, res, search, limit ) );
				break;
				case 'Custom':
					url = this.baseUrl('videos?part=contentDetails,statistics,snippet&id=' + res);
				break;	
			}
			return url;
		}
	};
									
	YRC.extras = {
		'playlists': {'sel': ' .yrc-playlists', 'label': 'Playlists'},
		'uploads': {'sel': ' .yrc-uploads', 'label': 'Uploads'},
		'playlist': {'sel': ' .yrc-playlist-videos', 'label': 'Playlist'}
	};
	
	YRC.EM.trigger('yrc.extras');
	
	YRC.Base = function(){};
	
	YRC.Base.prototype = {		
		'more': function( nextpage , more){
			this.request.page = nextpage;
			$(this.coresel).append( YRC.template.loadMoreButton( more, this.request.pagetokens.prev, this.multi_page) );
		},
		
		'moreEvent': function(){
			var yc = this;
			$('body').off('click', this.coresel+' .yrc-load-more-button').on('click', this.coresel+' .yrc-load-more-button', function(e){
				//$(this).text(YRC.lang.form.more +'...');
				$(this).text('...');
				yc.request.dir = $(this).is('.yrc-nextpage') ? 1 :-1;
				yc.request.page = yc.request.dir > 0 ? yc.request.pagetokens.next : yc.request.pagetokens.prev;
				yc.fetch();
			});
		},
		
		'channelOrId': function(){
			if(this.temp_label === 'Custom') return this.custom_vids.splice(0, this.per_page).join(',');
			return ((this.label === 'Playlist' || this.temp_label === 'Playlist') ? this.request.id : (this.label === 'Search' ? (this.restrict_to_channel ? this.ref.channel : '') : this.ref.channel));
		},
		
		'fetch': function(){
			var url = YRC.auth.url( this.temp_label || this.label, this.request.page, this.channelOrId(), this.criteria, this.per_page, this.vst ), yc = this;
			$(this.coresel).addClass('yrc-loading-overlay');
			$.get(url, function(re){
				$(yc.coresel).removeClass('yrc-loading-overlay');
				yc.onResponse( re );
			});
		},
		
		'onResponse': function( re ){
			$(this.coresel+' .yrc-pagination').remove();
			if(!re.items.length) return this.nothingFound();
			this.request.times += this.request.dir;
			
			if(this.temp_label === 'Custom'){
				re.nextPageToken = ((this.request.times*this.per_page) < this.custom_vids_length);
				re.pageInfo.totalResults = this.custom_vids_length;
				re.items.forEach(function(item){
					item.snippet.resourceId = {'videoId' : item.id};
				});	
			}
			
			if((re.nextPageToken && ((this.request.times*this.per_page) < this.max)) || re.prevPageToken){
				this.request.pagetokens = {'prev': re.prevPageToken, 'next': re.nextPageToken};
				this.more( re.nextPageToken, Math.min(this.max, re.pageInfo.totalResults) - (this.request.times*this.per_page) );
			}
			this.list( re.items );
		},
		
		'init': function(s, label){
			this.page = 0;
			this.ref = s;
			this.label = YRC.extras[label].label;
			this.secsel = this.ref.sel + YRC.extras[label].sel;
			this.max = window.parseInt(this.ref.data.meta.maxv) || 10000;
			this.coresel = this.secsel;
			this.multi_page = this.ref.data.style.pagination;
			this.ref.blacklist = this.ref.data.meta.blacklist ? this.ref.data.meta.blacklist.split(',').map(function(v){ return v.trim(); }) : [];
			this.request = {'id':'', 'page':'', 'times':0, 'pagetokens': [], 'dir' : 1};
			this.criteria = this.ref.data.meta.default_sorting || '';
			this.per_page = window.parseInt(this.ref.data.meta.per_page) || 25;
			this.fetchAtSetup();
			this.moreEvent();
			this.events();
			return this;
		},
		
		'events': function(){},
		'fetchAtSetup': function(){ this.fetch(); },
		
		'list': function( items ){
			var bl= this.ref.blacklist;
			this.page++;
			var i = items.length;
			while(i--){
				var item = items[i];
				if(item.snippet.title === 'Private video') items.splice(i, 1);
				if( typeof item.id === 'object' ){
					if( bl.indexOf(item.id.videoId) > -1) items.splice(i, 1);
				}
				else {
					if( bl.indexOf(item.snippet.resourceId.videoId) > -1) items.splice(i, 1);
				}
			}
			
			this.ref.calcThumbSize( );
			this.ref.listVideos( items, $(this.coresel), (this.label === 'Playlist' || this.temp_label === 'Playlist' || this.temp_label === 'Custom'));
		},
		
		'nothingFound': function(){
			if(this.label !== 'Uploads' && !this.ref.data.style.preload)
				$(this.coresel + ' ul').html(YRC.lang.form.Nothing_found);
			return false;
		}
	};
	
	Object.keys( YRC.extras ).forEach(function(section){
		section = YRC.extras[section].label;
		YRC[ section ] = function(){};
		YRC[ section ].prototype = new YRC.Base();
		YRC[ section ].prototype.constructor = YRC[ section ];
	});
							
	YRC.Uploads.prototype.fetchAtSetup = function(){
		this.ref.data.meta.search_own = (this.ref.data.meta.search_own  === undefined) ? 1 : this.ref.data.meta.search_own;
		this.vst = {'t':(this.ref.data.meta.search_term || ''), 'own': parseInt(this.ref.data.meta.search_own)};
		if(this.ref.data.meta.playlist){
			this.temp_label =  'Playlist';
			this.request.id = this.ref.data.meta.playlist;
		}
		
		this.proSetup && this.proSetup();
		this.fetch();
	};
							
	YRC.Playlist.prototype.fetchAtSetup = function(){};
			
	YRC.Playlists.prototype.list = function (lists){
		this.ref.calcThumbSize( true );
		var cont = $(this.coresel), core = cont.children('.yrc-core'), yc = this.ref;
		lists.forEach(function(list, i){
			core.append( YRC.template.playlistItem( list, yc.gridstyle.img ) );
		});
		this.ref.adjust(core, '.yrc-playlist-item', this.ref.section, true);
	};
						
	YRC.Playlists.prototype.events = function(){
		var yc = this, pl = this.ref.playlist;
		
		$('body').on('click', yc.secsel+' .yrc-playlist-item', function(e){ 
			pl.request.id = $(this).data('playlist');
			pl.request.page = '';
			pl.request.times = 0;
			$(yc.secsel).css('margin-top', function(){return -$(this).height(); }).find('.yrc-section-action').remove();
			$(yc.secsel).append( YRC.template.subSectionBar( $(this).find('.yrc-item-meta div').text() ));
			pl.fetch();
		});
	};		
	
	YRC.EM.trigger('yrc.classes_defined');
	
	YRC.merge = function(o, n, ox, ke){
		for(var k in n){
			if(typeof n[k] !== 'object'){
				if(o === undefined) ox[ke] = n;
				else {
					if(o[k] === undefined) o[k] = n[k];
				}
			} else {
				YRC.merge(o[k], n[k], o, k);
			}	
		}
	};
	
	YRC.backwardCompatible = function(channel){
		var bc = {
			'style': {
				'video_style': ['large', 'open'],
				'thumb_image_size': 'medium',
				'player_top': 'title',
				'default_tab': 'uploads',
				'uploads': 1,
				'banner': 1,
				'menu': 1
			}
		};
		
		YRC.merge(channel, bc);
		YRC.EM.trigger('yrc.defaults', channel);
		return channel;
	};
		
	YRC.Setup = function(id, channel, host){
		if(channel.meta.playlist) channel.meta.onlyonce = false;
		if(channel.meta.onlyonce){
			channel.meta.playlist = channel.meta.channel_uploads;
			this.onlyonce = true;
			channel.meta.maxv = parseInt(channel.meta.maxv) || 0;
			channel.meta.per_page = parseInt(channel.meta.per_page) || 50;
		}
		if(!channel.meta.playlist){
			channel.meta.default_sorting = (channel.meta.default_sorting === 'none') ? '' : channel.meta.default_sorting;
			channel.meta.temp_sort = 'none';
		}
		
		channel = YRC.backwardCompatible( channel );
		if(host.find('.yrc-cu-pl').length){
			channel.meta.custom = channel.meta.custom_vids = host.find('.yrc-cu-pl').data('cupl').videos;
		}
						
		this.id = id;
		this.data = channel;
		this.channel = channel.meta.channel;
		this.host = host;
		this.rtl = channel.style.rtl ? 'yrc-rtl' : '';
		this.player = {};
				
		this.size = YRC.sizer();		
		this.active_sections = {};
		
		if(this.data.style.playlists)this.active_sections.playlists = true;
		if(this.data.style.search)this.active_sections.search = true;
		if(this.data.style.uploads)this.active_sections.uploads = true;
		
		this.size.size(this);
		this.init();
		if(this.active_sections.uploads)
			this.uploads = new YRC.Uploads().init(this, 'uploads');
		
		if(this.active_sections.playlists){
			this.playlist = new YRC.Playlist().init(this, 'playlist');
			this.playlists = new YRC.Playlists().init(this, 'playlists');
		}
		
		YRC.EM.trigger('yrc.setup', this);
		this.section = this.data.style.default_tab;
		if( !$(this.sel+' .yrc-menu-items li[data-section='+this.section+']').length )
			this.section = $(this.sel+' .yrc-menu-items li').first().data('section');
		$(this.sel+' .yrc-menu-items li[data-section='+this.section+']').addClass('yrc-active');
		this.size.sections();
		return this;
	};
	
	YRC.Setup.prototype = {
		'init': function(){
			this.player_mode = window.parseInt(this.data.style.player_mode);
			this.data.style.rating_style = this.data.style.video_style[0] === 'large' ? window.parseInt(this.data.style.rating_style) : 0;
			this.data.style.video_style.push(this.data.style.rating_style ? 'pie' : 'bar');
			this.data.style.video_style.push(this.data.style.thumb_image_size);
			
			this.host.append('<div class="yrc-shell '+this.rtl+ (YRC.is_pro ? ' yrc-pro-v' : ' yrc-free-v') +'" id="yrc-shell-'+ this.id +'">'+ YRC.template.content( this.active_sections ) +'</div>')
			this.sel = '#yrc-shell-'+ this.id;
			yrcStyle( this.sel, this.data );
			this.load();
			
			if(Object.keys(this.active_sections).length < 2){
				if(!YRC.is_pro || (YRC.is_pro && (!this.data.style.uploads || !this.data.style.menu)))
					$(this.sel+' .yrc-menu').addClass('pb-hidden');
			}
		},
		
		'load': function(){
			YRC.auth.apikey = this.data.meta.apikey;
			var yc = this, url = YRC.auth.baseUrl('channels?part=snippet,contentDetails,statistics,brandingSettings&id='+this.channel);
			//var channel = JSON.parse( localStorage.getItem( this.channel || '{}') );
			
			//if( !channel || ((+new Date - channel[1]) > 24*60*60*1000))
			if(this.data.style.banner){
				$.get(url, function(re){ yc.deploy( re.items[0] ); });
			} else {
				this.events();
				$(this.sel + ' .yrc-banner').css('display', 'none');
				YRC.EM.trigger('yrc.deployed', [[this.sel, this.data]]);
			}
			//else
				//yc.deploy( channel[0] );
		},
		
		'deploy': function(channel){
			//localStorage.setItem(channel.id, JSON.stringify([channel, +new Date]));
			var image = this.size.ww > 640 ? 'bannerTabletImageUrl' : 'bannerMobileImageUrl';
				image = channel.brandingSettings.image[ image ];
			var brands = $(this.sel).find('.yrc-brand');
				brands.css('background', 'url('+ (image || channel.brandingSettings.image.bannerImageUrl)+ ') no-repeat '+this.data.style.colors.item.background);
				brands.eq(0).append( YRC.template.header(channel) );
			$(this.sel +' .yrc-stats').css('top', function(){ return 75 - ($(this).height()/2); })		
			this.events();
			YRC.EM.trigger('yrc.deployed', [[this.sel, this.data]]);
		},
		
		'events': function(){
			var sel = this.sel, yc = this;
			$('body').on('click', sel+' .yrc-menu-item', function(e){ 
				var idx = $(this).index();
				yc.section = $(this).data('section');
				$(this).addClass('yrc-active').siblings().removeClass('yrc-active');
				$(sel+' .yrc-sections').css({'height': function(){
					return $(this).find('.yrc-section:eq('+ idx +')').height();
				}}).css('margin-'+(yc.rtl ? 'right': 'left'), (idx * -yc.size.ww));
				if(yc.section === 'search') $(sel+' .yrc-search-form-top').css('display', 'none');
				else $(sel+' .yrc-search-form-top').css('display', '');
			});
			
			$('body').on('click', sel+' .yrc-playlist-bar .yrc-close span', function(e){ 
				var t = $(this);
				t.parents('.yrc-sub-section').css('margin-top', 0);
				window.setTimeout(function(){
					$(sel).find('.yrc-playlist-videos .yrc-core').empty().end().find('.yrc-playlist-videos .yrc-load-more-button').remove();
					t.parents('.yrc-sections').css('height', function(){ return $(this).find('.yrc-playlists').height(); });
					t.parents('li').remove();
				}, 500);
			});
						
			$('body').on('click', sel+' .yrc-video a', function(e){
				if(yc.player_mode !== 2){
					e.preventDefault();
					YRC.play(yc, sel, $(this));
				}
				$('body')
					.off('click', '.yrc-player-bar .yrc-close span')
					.on('click', '.yrc-player-bar .yrc-close span', function(e){
						yc.closePlayer(e, yc);
					});
					
				$('body')
					.off('click', '.yrc-player-shell')
					.on('click', '.yrc-player-shell', function(e){
						if(!$(e.target).is('.yrc-player-shell')) e.stopPropagation();
						yc.closePlayer(e);
					});
				
				$(document).keyup(function(e) {
				  if (e.keyCode == 27) yc.closePlayer(e);
				});	
			});	
			
			
			$(window).on('resize', function(e){
					yc.size.resize();

			});
			
		},
		
		'closePlayer': function(e, yc){
			if(e.isPropagationStopped && e.isPropagationStopped()) return false;
			this.player.player.destroy();
			$('.yrc-player-shell').remove();
			$('.yrc-onlyone-video, .yrc-playing').removeClass('yrc-onlyone-video, yrc-playing');
			$(this.sel+' .yrc-sections').css('height', this.player.list.parents('.yrc-section').height());
			this.player = {};
		},
		
		'listVideos': function(vids, cont, res){
			var core = cont.children('.yrc-core'), append = 1, i;
			var srt = this.data.meta.temp_sort || (this.uploads ? (this.uploads.criteria || 'etad') : 'etad');
			if((srt !== 'none') && ( this.onlyonce || this.data.meta.playlist || this.data.meta.custom) ){
				if((srt === 'date' || srt === 'title' || srt === 'etad' || srt === 'title_desc')){
					vids.sort(function(a, b){
							if(srt === 'date') {i = (new Date(a.snippet.publishedAt) < new Date(b.snippet.publishedAt));}
							else if (srt === 'title_desc') {i = (a.snippet.title < b.snippet.title);}
							else if (srt === 'title') {i = (a.snippet.title > b.snippet.title);}
							else {i = (new Date(a.snippet.publishedAt) > new Date(b.snippet.publishedAt));}
						return i ? 1 : -1;	
					});
					append = 0;
					this.lstVideos(vids, core, res);
				}
			} else {
				if(srt==='title_desc')vids.sort(function(a, b){i = (a.snippet.title < b.snippet.title); return i ? 1 : -1;});
				this.lstVideos(vids, core, res);
			}
			
			YRC.EM.trigger('yrc.videos_listed', [[core, vids, this, append]]);
		},
		
		'lstVideos': function(vids, core, res){
			//if(this.data.style.pagination && this.uploads.page > 1){
			if(this.data.style.pagination){
				core.empty();
				if((core.offset().top - $(window).scrollTop()) < 0){
					$('html,body').animate({'scrollTop': core.offset().top-50}, 'fast');
				}
			}
			
			var yc = this;
			vids.forEach(function( vid ){
				core.append( YRC.template.video( vid, res, yc.data.style.video_style, yc.gridstyle.img ) );
			});
			core.find('.yrc-onlyone-video').removeClass('yrc-onlyone-video');
			this.adjust(core, '.yrc-video', this.section);	
			core.find('.yrc-just-listed img').load(function(e){
				$(this).parent().addClass('yrc-full-scale');
			});	
			
			if(!this.first_loaded && !this.preloading){
				this.first_loaded = true;
				YRC.EM.trigger('yrc.first_load', [[this, core]]);
			}
		},
		
		'adjust': function(core, item, section){
			var items = core.find(item), in_row = this.gridstyle.in_row, rem = this.gridstyle.rem; 
			var margin_dir = this.rtl ? 'right' : 'left';
			var lastrow = items.length - (items.length % in_row) - 1;
			core.find(item+'.yrc-has-left').css(('margin-'+margin_dir), 0).removeClass('yrc-has-left');
			
			core.find(item).css('width', this.gridstyle.fw).css(('margin-'+(margin_dir ==='left'?'right':'left')), function(i){
				if(i > lastrow) $(this).css(('margin-'+margin_dir), rem).addClass('yrc-has-left');
				if((i+1)%in_row) return rem;
				return 0;
			}).addClass('yrc-full-scale');
			
			core.parents('.yrc-sections').css('height', 'auto');
		},
		
		'calcThumbSize': function(pl){
			var fw = 0, rem = (parseInt(this.data.style.thumb_margin)||8), ww = this.size.ww, in_row = 0, yc = this;
			
			function determineColumns( s ){
				var vid_f = s ? s : (pl || yc.data.style.video_style[0] !== 'small') ? 2 : 1,
					fxw = 160*vid_f;
				fw = fxw;
				in_row = Math.round(ww/fw);
					
				if(in_row > 1) ww -= (in_row - 1) * rem; 
				
				fw = ww/in_row;
				//fw = fw > fxw ? fxw : fw;
				//fw += (yc.size.ww - ((fw*in_row) + (in_row-1)*rem)) / in_row;
				//console.log(yc.size.ww, ww, in_row, fw);
			}
			 
			function thumbWidthFixedColumns(){
				 in_row = parseInt(yc.data.style.columns);
				 fw = (yc.size.ww - ( (in_row-1)*rem )) / ( in_row );
				 if( fw < 140 ) determineColumns( 1 );
			}
						
			if( !parseInt(this.data.style.columns) || pl ) determineColumns();
			else thumbWidthFixedColumns();
			
			if(!pl)this.size.per_row = in_row;
			this.gridstyle = {'fw':fw, 'in_row':in_row, 'rem':rem, 'img': fw > 330 ? 'high' : 'medium'};
		}
	};
	
	YRC.play = function(yc, sel, a){
		var li = a.parent();
		if( li.is('.yrc-playing') ) return false;
		
		$('.yrc-player-shell').remove();
				
		$('.yrc-onlyone-video, .yrc-playing').removeClass('yrc-onlyone-video, yrc-playing');
		if(!li.siblings().length) li.addClass('yrc-onlyone-video');
		li.addClass('yrc-playing');
		
		if(yc.player_mode){	// inline
			if( parseInt(yc.player_mode) === 3){
				li.parent().prepend( YRC.template.player( li, yc ) );
			} else {
				var idx = li.index()+1;
					idx = idx - idx%yc.size.per_row;
					idx = idx ? idx : yc.size.per_row;
					
				var v = a.parents('ul').children('li');
					v = v.eq(idx-1).length ? v.eq(idx-1) : v.last();
					v.after( YRC.template.player( li, yc ) );
			}
			
			var ofs = ($(window).height() - (((9/16) * $('.yrc-player').width()) + $(sel+' .yrc-player-bar').outerHeight()) ) / 2;
				
			$('html,body').animate({'scrollTop': $(sel+' .yrc-player').offset().top-ofs}, 'slow');
		} else {
			$('body').append( YRC.template.player( li, yc, true) );
		}
		$(sel+' .yrc-sections').css('height', 'auto');
		$(sel+' .yrc-player-frame, .yrc-lightbox .yrc-player-frame').css('height', ((9/16) * $('.yrc-player').width()) );
		yc.player.player = YRC.Player(yc, true);
		yc.player.list = li.parent();
	};
	
	YRC.Player = function(yc, play){
		return new YT.Player('yrc-player-frame', {
			events: {
				'onReady':function(e){
					if(play && !YRC.iOS)e.target.playVideo();
				},
				'onStateChange': function(e){YRC.EM.trigger('yrc.player_state_change', [[yc, e]]); }
			}
		});
	};	
	
	YRC.sizer = function(){
		return {
			'size': function(ref){
				this.ref = ref || this.ref;
				var th = this.ref.host.css('height', $(window).height()+5);
				this.ww = this.ref.host.parent().width() || 280;
				this.ref.host.css('height', 'auto').removeClass('yrc-mobile yrc-desktop').addClass((this.ww < 481 ? 'yrc-mobile' : 'yrc-desktop'));
			},
			
			'resize': function(){
				this.size();
				this.sections();
				this.ref.calcThumbSize();
				this.ref.adjust($(this.ref.sel+' .yrc-core'), '.yrc-video');
				this.ref.calcThumbSize(true);
				this.ref.adjust($(this.ref.sel+' .yrc-core'), '.yrc-playlist-item', '', true);
				$(this.ref.sel+' .yrc-sections').css('height', $(this.ref.sel+' .yrc-'+this.ref.section).parent().height());
				var ref = this.ref;
				window.setTimeout(function(){
					$(ref.sel+' .yrc-sections').css('height', $(ref.sel+' .yrc-'+ref.section).parent().height());
				}, 250);
			},
			
			'sections': function(){
				var yc = this, section;
				$(yc.ref.sel+'.yrc-shell, '+yc.ref.sel+' .yrc-section').css('width', this.ww);
				$(yc.ref.sel+' .yrc-sections').css('width', this.ww*Object.keys(yc.ref.active_sections).length).css('margin-'+(yc.ref.rtl ? 'right': 'left'), function(){
					section = $(this).parent().find('.yrc-menu-items .yrc-active').data('section');
					return -($(this).parent().find('.yrc-menu-items .yrc-active').index() * yc.ww);
				});
				$(yc.ref.sel+' .yrc-sections').css('height', 'auto');
				$('.yrc-player-frame').css('height', ((9/16) * $('.yrc-player').width()) );
			}
		};
	};

		
	YRC.template.header = function(channel){
		return '<div class="yrc-name pb-absolute">\
					<img src="'+ channel.snippet.thumbnails.default.url +'"/>\
					<span>'+ channel.brandingSettings.channel.title +'</span>\
				</div>\
				<div class="yrc-stats pb-absolute">\
					<span class="yrc-subs"></span>\
					<span class="yrc-videos pb-block">'+ YRC.template.vicon +'<span class="pb-inline">'+ YRC.template.num( channel.statistics.videoCount ) +'</span></span>\
					<span class="yrc-views pb-block">'+ YRC.template.eyecon +'<span class="pb-inline">'+ YRC.template.num( channel.statistics.viewCount ) +'</span></span>\
				</div>';
	};	
	
	YRC.template.search = YRC.template.search || function(){ return '';};
	YRC.template.playlists = '<div class="yrc-section pb-inline">\
								<div class="yrc-playlists yrc-sub-section"><ul class="yrc-core"></ul></div>\
								<div class="yrc-playlist-videos yrc-sub-section"><ul class="yrc-core"></ul></div>\
							</div>';
				
	YRC.template.content = function( secs ){
		return '<div class="yrc-banner pb-relative"><div class="yrc-brand pb-relative"></div></div>\
		<div class="yrc-content">\
			<div class="yrc-menu pb-relative">\
				<ul class="yrc-menu-items">'+
					(secs.uploads ? '<li class="pb-inline yrc-menu-item" data-section="uploads">'+ YRC.lang.form.Videos +'</li>' : '') +
					(secs.playlists ? '<li class="pb-inline yrc-menu-item" data-section="playlists">'+ YRC.lang.form.Playlists +'</li>' : '') +
				'</ul>\
			</div>\
			<div class="yrc-sections">' +
				(secs.uploads ? '<div class="yrc-section pb-inline"><div class="yrc-uploads yrc-sub-section"><ul class="yrc-core"></ul></div></div>': '') +
				(secs.playlists ? YRC.template.playlists : '') + (secs.search ? YRC.template.search() : '') +
			'</div>\
		</div>\
		<div class="yrc-banner"><div class="yrc-brand pb-relative"></div></div>';
	};	
	
	YRC.template.loadMoreButton = function (more, prev, multi){
		var wrap = '<div class="yrc-pagination '+ (multi ? 'yrc-multi-page' : '') +'">';
			if(multi && prev) wrap += '<li class="yrc-load-more-button yrc-button yrc-prevpage">'+ YRC.lang.form.Prev +'</li>';
			if(more > 0) wrap += '<li class="yrc-load-more-button yrc-button yrc-nextpage">'+ ( multi ? YRC.lang.form.Next : (YRC.template.num(more) +' '+ YRC.lang.form.more)) +'</li>';
		wrap += '</div>';
		return wrap;
	};
	
	YRC.template.num = function( num ){
		return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	};
	
	YRC.template.subSectionBar = function( title , player, type){
		return '<li class="yrc-section-action yrc-player-top-'+type+' '+(player ? 'yrc-player-bar':'yrc-playlist-bar')+'">\
			<span class="yrc-sub-section-name">'+ title
			+'</span><span class="yrc-close"><span>x</span></span>\
		</li>';
	};
	
	YRC.template.playerTop = function(li, type){
		return [li.data('video'), li.find('.yrc-video-'+type).html()||''];
	};
		
	YRC.template.player = function( li, yc, lightbox){
		var type = yc.data.style.player_top;
		var v =  this.playerTop(li, type);
		return '<div class="yrc-player-shell '+(lightbox ? 'yrc-lightbox' : 'yrc-inline-player')+'" id="'+yc.sel.replace('#', '')+'-player-shell">\
			<div class="yrc-player">'
				+ YRC.template.subSectionBar(v[1], true, type) +
				'<div class="yrc-player-frame">\
					<iframe id="yrc-player-frame" style="width:100%;height:100%" src="//www.youtube.com/embed/'+v[0]+'?enablejsapi=1&rel=0&origin='+(window.location.origin)+'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>\
					<span class="pb-absolute yrc-prev yrc-page-nav"><</span><span class="pb-absolute yrc-next yrc-page-nav">></span>\
				</div>\
			</div></div>';
	};
			
	YRC.template.video = function( vid, res, style, img ){
		var vid_id = res ? vid.snippet.resourceId.videoId : vid.id.videoId,
			cl = style[0] +(style[0] === 'adjacent' ? '' : ' yrc-item-'+style[1]);
		return '<li class="yrc-video yrc-item-'+ cl +' yrc-item pb-inline yrc-just-listed" data-video="'+ vid_id +'">\
			<a href="'+ watch_video + vid_id +'" class="yrc-video-link pb-block" target="_blank">\
				<figure class="yrc-thumb pb-inline pb-relative"><img src="'+ (vid.snippet.thumbnails ? vid.snippet.thumbnails[ img ].url : '') +'"/>\
				</figure><div class="yrc-item-meta pb-inline">\
					<div class="yrc-name-date yrc-nd-'+style[2]+'">\
						<span class="pb-block yrc-video-title yrc-item-title">'+ vid.snippet.title +'</span>\
						<span class="yrc-video-date">'+ miti( new Date(vid.snippet.publishedAt) ) +'</span>\
						<span class="yrc-video-views"></span>\
					</div></div></a><div class="pb-hidden yrc-video-desc">'+YRC.template.urlify(vid.snippet.description)+'</div>\
			</li>';
	};
	
	YRC.template.urlify = function(text) {
		var urlRegex = /(https?:\/\/[^\s]+)/g;
		return text.replace(urlRegex, function(url) {
			return '<a href="' + url + '" target="_blank">' + url + '</a>';
		});
	};
	
	YRC.iOS = (function(){
		var userAgent = navigator.userAgent || navigator.vendor || window.opera;
		return ( userAgent.match( /iPad/i ) || userAgent.match( /iPhone/i ) || userAgent.match( /iPod/i ) );
	}());
	
	YRC.template.playlistItem = function( item, img ){
		return '<li class="yrc-playlist-item yrc-item-adjacent pb-inline yrc-item" data-playlist="'+ item.id +'">\
				<figure class="yrc-thumb pb-inline yrc-full-scale"><img src="'+ item.snippet.thumbnails[ img ].url +'"\>\
					</figure><div class="pb-inline yrc-item-meta"><div class="pb-block yrc-item-title">'+ item.snippet.title +'</div>\
					<span class="pb-block">'+ item.contentDetails.itemCount +' '+YRC.lang.form.Videos.toLowerCase()+'</span>\
					<span class="pb-block">'+ miti( new Date(item.snippet.publishedAt) ) +'</span></div>\
			</li>';
	};
	
	
	YRC.template.eyecon = '<svg height="40" version="1.1" width="40" xmlns="http://www.w3.org/2000/svg" style="overflow: hidden;"><path fill="#fff" stroke="#ffffff" d="M16,8.286C8.454,8.286,2.5,16,2.5,16S8.454,23.715,16,23.715C21.771,23.715,29.5,16,29.5,16S21.771,8.286,16,8.286ZM16,20.807C13.350999999999999,20.807,11.193,18.65,11.193,15.999999999999998S13.350999999999999,11.192999999999998,16,11.192999999999998S20.807000000000002,13.350999999999997,20.807000000000002,15.999999999999998S18.649,20.807,16,20.807ZM16,13.194C14.451,13.194,13.193999999999999,14.450000000000001,13.193999999999999,16C13.193999999999999,17.55,14.45,18.806,16,18.806C17.55,18.806,18.806,17.55,18.806,16C18.806,14.451,17.55,13.194,16,13.194Z" stroke-width="3" stroke-linejoin="round" opacity="0" transform="matrix(1,0,0,1,4,4)" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); stroke-linejoin: round; opacity: 0;"></path><path class="yrc-stat-icon" stroke="none" d="M16,8.286C8.454,8.286,2.5,16,2.5,16S8.454,23.715,16,23.715C21.771,23.715,29.5,16,29.5,16S21.771,8.286,16,8.286ZM16,20.807C13.350999999999999,20.807,11.193,18.65,11.193,15.999999999999998S13.350999999999999,11.192999999999998,16,11.192999999999998S20.807000000000002,13.350999999999997,20.807000000000002,15.999999999999998S18.649,20.807,16,20.807ZM16,13.194C14.451,13.194,13.193999999999999,14.450000000000001,13.193999999999999,16C13.193999999999999,17.55,14.45,18.806,16,18.806C17.55,18.806,18.806,17.55,18.806,16C18.806,14.451,17.55,13.194,16,13.194Z" transform="matrix(1,0,0,1,4,4)" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><rect x="0" y="0" width="32" height="32" r="0" rx="0" ry="0" fill="#000000" stroke="#000" opacity="0" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); opacity: 0;"></rect></svg>';			
	YRC.template.vicon = '<svg height="40" version="1.1" width="40" xmlns="http://www.w3.org/2000/svg" style="overflow: hidden;"><path fill="#fff" stroke="#ffffff" d="M27.188,4.875V5.969H22.688V4.875H8.062V5.969H3.5619999999999994V4.875H2.5619999999999994V26.125H3.5619999999999994V25.031H8.062V26.125H22.686999999999998V25.031H27.186999999999998V26.125H28.436999999999998V4.875H27.188ZM8.062,23.719H3.5619999999999994V20.594H8.062V23.719ZM8.062,19.281H3.5619999999999994V16.156H8.062V19.281ZM8.062,14.844H3.5619999999999994V11.719H8.062V14.844ZM8.062,10.406H3.5619999999999994V7.281H8.062V10.406ZM11.247,20.59V9.754L20.628999999999998,15.172L11.247,20.59ZM27.188,23.719H22.688V20.594H27.188V23.719ZM27.188,19.281H22.688V16.156H27.188V19.281ZM27.188,14.844H22.688V11.719H27.188V14.844ZM27.188,10.406H22.688V7.281H27.188V10.406Z" stroke-width="3" stroke-linejoin="round" opacity="0" transform="matrix(1,0,0,1,4,4)" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); stroke-linejoin: round; opacity: 0;"></path><path class="yrc-stat-icon" stroke="none" d="M27.188,4.875V5.969H22.688V4.875H8.062V5.969H3.5619999999999994V4.875H2.5619999999999994V26.125H3.5619999999999994V25.031H8.062V26.125H22.686999999999998V25.031H27.186999999999998V26.125H28.436999999999998V4.875H27.188ZM8.062,23.719H3.5619999999999994V20.594H8.062V23.719ZM8.062,19.281H3.5619999999999994V16.156H8.062V19.281ZM8.062,14.844H3.5619999999999994V11.719H8.062V14.844ZM8.062,10.406H3.5619999999999994V7.281H8.062V10.406ZM11.247,20.59V9.754L20.628999999999998,15.172L11.247,20.59ZM27.188,23.719H22.688V20.594H27.188V23.719ZM27.188,19.281H22.688V16.156H27.188V19.281ZM27.188,14.844H22.688V11.719H27.188V14.844ZM27.188,10.406H22.688V7.281H27.188V10.406Z" transform="matrix(1,0,0,1,4,4)" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><rect x="0" y="0" width="32" height="32" r="0" rx="0" ry="0" fill="#000000" stroke="#000" opacity="0" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); opacity: 0;"></rect></svg>';
	
	$('body').on('click', '.yrc-shell .yrc-banner, .yrc-shell .yrc-sections', function(e){
		e.stopPropagation();
		$('.yrc-sort-uploads').addClass('pb-hidden');
	});	
		
	YRC.Setups = {};	
	YRC.run = function(shell){
		if(!shell.attr('data-yrc-setup') && shell.length){
			shell.attr('data-yrc-setup', 1);
			YRC.Setups[YRC.counter] = new YRC.Setup(YRC.counter++, shell.data('yrc-channel'), shell);
		}
	};
	
	YRC.lang = YRC.lang || yrc_lang_terms;
	
	// Backward compatible
	
	//YRC.lang.form.prev = 'Prev';
	//YRC.lang.form.next = 'Next';
		
	if (!window.location.origin) {
	  window.location.origin = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port: '');
	}
	
	YRC.run( $('.yrc-shell-cover').eq(0) );
	YRC.EM.trigger('yrc.run');
	
	
});
