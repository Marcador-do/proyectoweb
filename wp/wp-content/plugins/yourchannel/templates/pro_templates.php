<script type="text/template" id="yrc-pro-fields-tmpl">
	<% var methods = [['viewCount', '<?php _e('Views', 'YourChannel'); ?>'], ['date', '<?php _e('Latest', 'YourChannel'); ?>'], ['rating', '<?php _e('Likes', 'YourChannel'); ?>'], ['title', '<?php _e('Title', 'YourChannel'); ?>'], ['title_desc', '<?php _e('Title Descending', 'YourChannel'); ?>'], ['none', '<?php _e('None', 'YourChannel'); ?>']];  %>
	<div class="pbc-row">
		<div class="pbc-row-label wpb-inline"><?php _e('Default Sorting', 'YourChannel'); ?></div>
		<div class="pbc-row-field wpb-inline">
			<div class="pbc-field wpb-inline">
				<select id="yrc-default-sorting">
					<% methods.forEach(function(m){ %>
						<option value="<%= m[0] %>" <%- meta.default_sorting === m[0] ? 'selected' : '' %> ><%= m[1] %></option>
					<% }); %>
				</select>
			</div>
		</div>
	</div>
	
	<div class="pbc-row">
		<div class="pbc-row-label wpb-inline"><?php _e('Videos', 'YourChannel'); ?> <?php _e('per load', 'YourChannel'); ?></div>
		<div class="pbc-row-field wpb-inline">
			<input type="number" value="<%= meta.per_page %>" name="per_page" class="wpb-raw"/>
		</div>
	</div>
	
	<div class="pbc-row <%- (parseInt(meta.per_page) === 1) ? '' : 'wpb-force-hide' %>" id="yrc-autoplay-field">
		<div class="pbc-row-label wpb-inline"><?php _e('Autoplay', 'YourChannel'); ?></div>
		<div class="pbc-row-field wpb-inline">
			<label>
			<input type="checkbox" value="<%= meta.autoplay %>" <%- (meta.autoplay && (parseInt(meta.per_page) === 1))? 'checked' : '' %> name="autoplay" class="wpb-raw"/>
			<small><?php _e('Check this if you want that only one video to autoplay', 'YourChannel'); ?>.</small>
			</label>
		</div>
	</div>
	
	<div class="pbc-row">
		<div class="pbc-row-label wpb-inline"><?php _e('Max', 'YourChannel'); ?> <?php _e('Videos', 'YourChannel'); ?></div>
		<div class="pbc-row-field wpb-inline">
			<input type="number" value="<%= meta.maxv %>" name="maxv" class="wpb-raw"/><small><?php _e('Leave empty for no limit', 'YourChannel'); ?>.</small>
		</div>
	</div>
	
	<div class="pbc-row">
		<div class="pbc-row-label wpb-inline"><?php _e('Search', 'YourChannel'); ?></div>
		<div class="pbc-row-field wpb-inline" id="pbc-styles-field">
			<div class="pbc-field wpb-inline">
				<label><input type="checkbox" <%- meta.search.rtc ? 'checked' : '' %> name="rtc" class="wpb-raw"/><?php _e('Restrict to', 'YourChannel'); ?> <?php _e('Channel', 'YourChannel'); ?></label>
			</div>
		</div>
	</div>
	
	<div class="pbc-row">
		<div class="pbc-row-label wpb-inline"><?php _e('Blacklist', 'YourChannel'); ?></div>
		<div class="pbc-row-field wpb-inline">
			<textarea name="blacklist" style="width:100%;" rows="4" class="wpb-raw"><%= meta.blacklist %></textarea>
			<small><?php _e('Enter video IDs separated by commas to not show them', 'YourChannel'); ?>.</small>
		</div>
	</div>

	<div class="pbc-row">
		<div class="pbc-row-label wpb-inline"><?php _e('Colors', 'YourChannel'); ?></div>
		<div class="pbc-row-field wpb-inline" id="pbc-colors-field">
			<a class="button pbc-choose-color pbc-field-toggler"><?php _e('Toggle', 'YourChannel'); ?></a>
			<div id="pbc-colors" class="wpb-force-hide pbc-togglable-field">
				<div class="pbc-row">
					<div class="pbc-row-label wpb-inline"><?php _e('Color', 'YourChannel'); ?></div>
					<div class="pbc-row-field wpb-inline">
						<div class="pbc-field wpb-inline">
							<label><input value="<%= style.colors.color.text %>"
							data-keys="colors-color-text, .yrc-banner, color, 0" class="wpb-color"/><?php _e('Text', 'YourChannel'); ?></label>
						</div>
						
						<div class="pbc-field wpb-inline">
							<label><input value="<%= style.colors.color.link %>"
							data-keys="colors-color-link, .yrc-video a,.yrc-playlist-item,.yrc-menu li, color, 0" class="wpb-color"/><?php _e('Link', 'YourChannel'); ?></label>
						</div>
					</div>
				</div>


				<div class="pbc-row">
					<div class="pbc-row-label wpb-inline"><?php _e('Video', 'YourChannel'); ?>/<?php _e('Playlist', 'YourChannel'); ?></div>
					<div class="pbc-row-field wpb-inline">			
						<div class="pbc-field wpb-inline">
							<label><input value="<%= style.colors.item.background %>" data-default-color="#000"
									data-keys="colors-item-background, .yrc-video,.yrc-playlist-item, background, 0" class="wpb-color"/><?php _e('Background', 'YourChannel'); ?></label>
						</div>
					</div>
				</div>
					
				<div class="pbc-row"> 
					<div class="pbc-row-label wpb-inline"><?php _e('Buttons', 'YourChannel'); ?></div>
					<div class="pbc-row-field wpb-inline">
						<div class="pbc-field wpb-inline">
							<label><input value="<%= style.colors.button.color %>" data-default-color='#000'"
									data-keys="colors-button-color, .yrc-button, color, 0" class="wpb-color"/><?php _e('Color', 'YourChannel'); ?></label>
						</div>
									
						<div class="pbc-field wpb-inline">
							<label><input value="<%= style.colors.button.background %>"
									data-keys="colors-button-background, .yrc-button, background, 0" class="wpb-color"/><?php _e('Background', 'YourChannel'); ?></label>
						</div>
					</div>
				</div>
				
				<div class="pbc-row">
					<div class="pbc-row-label wpb-inline"><?php _e('Input', 'YourChannel'); ?></div>
					<div class="pbc-row-field wpb-inline">
						<div class="pbc-field wpb-inline">
							<label><input value="<%= style.colors.input.color %>"
							data-keys="colors-input-color, .yrc-search input,.yrc-search select,.yrc-search button, color, 0" class="wpb-color"/><?php _e('Color', 'YourChannel'); ?></label>
						</div>
						
						<div class="pbc-field wpb-inline">
							<label><input value="<%= style.colors.input.background %>"
							data-keys="colors-input-background, .yrc-search input,.yrc-search select,.yrc-search button, background, 0" class="wpb-color"/><?php _e('Background', 'YourChannel'); ?></label>
						</div>
					</div>
				</div>
				
				<div class="pbc-row">
					<div class="pbc-row-label wpb-inline"><?php _e('Ratings', 'YourChannel'); ?></div>
					<div class="pbc-row-field wpb-inline">
						<div class="pbc-field wpb-inline">
							<label><input value="<%= style.colors.rating.like %>"
							data-keys="colors-rating-like, .yrc-shell svg .yrc-like, fill, 0" class="wpb-color"/><?php _e('Like', 'YourChannel'); ?></label>
						</div>
						
						<div class="pbc-field wpb-inline">
							<label><input value="<%= style.colors.rating.dislike %>"
							data-keys="colors-rating-dislike, .yrc-shell svg .yrc-dislike, fill, 0" class="wpb-color"/><?php _e('Dislike', 'YourChannel'); ?></label>
						</div>
						
						<div class="pbc-field wpb-inline">
							<label><input value="<%= style.colors.rating.neutral %>"
							data-keys="colors-rating-neutral, .yrc-shell svg .yrc-neutral, fill, 0" class="wpb-color"/><?php _e('None Rated', 'YourChannel'); ?></label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	
	<div class="pbc-row">
		<div class="pbc-row-label wpb-inline"><?php _e('Social media', 'YourChannel'); ?></div>
		<div class="pbc-row-field wpb-inline" id="pbc-social-media-field">
			<div class="pbc-field wpb-inline">
				<select id="pbc-social-medias">
					<% for(var m in YC.medias){ %>
						<option value="<%= m %>"><%= YC.medias[m] %></option>
					<% } %>
				</select>
				<a class="button" id="pbc-add-media"><?php _e('Add', 'YourChannel'); ?></a>
			</div></br>
			<small><?php _e('Leave empty to remove', 'YourChannel'); ?></small>
			<% for(var m in social){ %>
				<%= YC.mediaTemplate({'m':m, 'v':social[m]}) %>
			<% } %>
		</div>
	</div>
</script>

<script type="text/template" id="yrc-show-tmpl">
	<div class="pbc-field wpb-inline">
		<label class="pbc-field-label"><input type="checkbox" name="menu" <%- style.menu ? 'checked' : '' %>/>: <?php _e('Menu', 'YourChannel'); ?> </label>
	</div>
	<div class="pbc-field wpb-inline">
		<label class="pbc-field-label"><input type="checkbox" name="search" <%- style.search ? 'checked' : '' %>/>: <?php _e('Search', 'YourChannel'); ?> </label>
	</div>
	<div class="pbc-field wpb-inline">
		<label class="pbc-field-label"><input type="checkbox" name="search_on_top" <%- style.search_on_top ? 'checked' : '' %>/>: <?php _e('Search on Top', 'YourChannel'); ?></label>
	</div>
	<div class="pbc-field wpb-inline">
		<label class="pbc-field-label"><input type="checkbox" name="ratings" <%- style.ratings ? 'checked' : '' %>/>: <?php _e('Ratings', 'YourChannel'); ?> </label>
	</div>
</script>

<script type="text/template" id="yrc-player-options-tmpl">
	<% var style = data.style; %>
	<b>|</b> &nbsp; &nbsp; <div class="pbc-field wpb-inline">
		<label><input type="checkbox" name="autoplay_next" class="wpb-raw" <%- style.autoplay_next ? 'checked' : ''  %>/><?php _e('Autoplay next video', 'YourChannel'); ?></label>
	</div>

	<b>|</b> &nbsp; &nbsp; <div class="pbc-field wpb-inline">
		<label><input type="checkbox" name="load_first" class="wpb-raw" <%- style.load_first ? 'checked' : ''  %>/><?php _e('Pre-load a video', 'YourChannel'); ?></label>
	</div>
		
	<div class="pbc-field <%- style.load_first ? '' : 'wpb-hidden'  %>" id="yrc-auto-load-vid">
		<label><input name="preload" class="wpb-raw" value="<%- (style.preload || '') %>"/><?php _e('Video ID or URL. Leave empty to load first video', 'YourChannel'); ?></label>
	</div>
	
	<div class="pbc-field <%- style.load_first ? '' : 'wpb-hidden'  %>" id="yrc-auto-load-play-vid">
			<label><input type="checkbox" <%- data.meta.autoplay ? 'checked' : '' %> name="autoload_play" class="wpb-raw"/>
				<?php _e('Autoplay', 'YourChannel'); ?>
			</label>
	</div>
</script>

<script type="text/template" id="yrc-tag-tmpl">
	<div class="pbc-row">
		<div class="pbc-row-label wpb-inline"><?php _e('Tag', 'YourChannel'); ?></div>
		<div class="pbc-row-field wpb-inline">
			<input name="tag" value="<%= tag %>" class="wpb-raw"/>
			<small><?php _e('Give this channel a unique tag if you\'re creating multiple channels with same username but different settings', 'YourChannel'); ?>.</small>
		</div>
	</div>	
</script>

<script type="text/template" id="yrc-custom-playlist-tmpl">
	<div id="yrc-playlists" class="wpb-hidden  yrc-content">
		<div class="yrc-content-header wpb-clr">
			<h2 class="wpb-float-left wpb-pointer"><?php _e('Custom Playlists', 'YourChannel'); ?></h2>
			<div class="yrc-content-buttons wpb-float-right wpb-force-hide"></div>
		</div>
		<table class="widefat wpb-force-hide">
			<thead>
				<tr>
					<th><?php _e('Name', 'YourChannel'); ?></th>
					<th><?php _e('Videos', 'YourChannel'); ?> <small>(<?php _e('ID or URLs', 'YourChannel'); ?>)</small></th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		<p class="wpb-force-hide"><?php printf(__('Add %s to shortcode', 'YourChannel'), '<code>custom="your_playlist_name"</code>') ?>.</p>
	</div>
</script>

<script type="text/template" id="yrc-playlist-tmpl">
	<tr data-down="<%= key %>" class="pbc-down">
		<td><input class="yrc-playlist-name" value="<%= name %>"/></td>
		<td colspan="2"><textarea rows="<%- key === 'nw' ? 6 : 1 %>"><%- videos.join(', ') %></textarea></td>
		<td><a class="button <%- key === 'nw' ? 'pbc-save button-primary' : 'pbc-edit' %>" data-down="<%= key %>"><%- key === 'nw' ? '<?php _e('Save', 'YourChannel'); ?>' : '<?php _e('Edit', 'YourChannel'); ?>'%></a></td>
	</tr>
</script>

<script type="text/template" id="yrc-social-media-tmpl">
	<div class="pbc-field">
		<label><%= YC.medias[m] %><input type="text" name="<%= m %>" value="<%= v %>" /></label>
	</div>
</script>

<script type="text/template" id="yrc-columns-tmpl">
	<div class="pbc-row">
		<div class="pbc-row-label wpb-inline"><?php _e('Columns', 'YourChannel'); ?></div>
		<div class="pbc-row-field wpb-inline">
			<label><input type="number" min="0" max="50" value="<%- (style.columns) %>" name="columns" class="wpb-raw"/></label>
			<p><small><?php _e('Above 2 options will be ignored if you choose columns. Furthermore, this number might be changed by YourChannel in small screens', 'YourChannel'); ?>.</small></p>
		</div>
	</div>
</script>
					

<script type="text/template" id="yrc-rating-style-tmpl">
	<div class="pbc-row">
		<div class="pbc-row-label wpb-inline"><?php _e('Default Tab', 'YourChannel'); ?></div>
		<div class="pbc-row-field wpb-inline">
			<div class="pbc-field wpb-inline">
				<label><input type="radio" name="default_tab" value="uploads" <%- (style.default_tab === 'uploads') ? 'checked' : '' %> class="wpb-raw"/><?php _e('Videos', 'YourChannel'); ?></label>
			</div>
			
			<div class="pbc-field wpb-inline">
				<label><input type="radio" name="default_tab" value="playlists" <%- (style.default_tab === 'playlists') ? 'checked' : '' %> class="wpb-raw"/><?php _e('Playlists', 'YourChannel'); ?></label>
			</div>
			
			<div class="pbc-field wpb-inline">
				<label><input type="radio" name="default_tab" value="search" <%- (style.default_tab === 'search') ? 'checked' : '' %> class="wpb-raw"/><?php _e('Search', 'YourChannel'); ?></label>
			</div>
		</div>
	</div>

	<div class="pbc-row">
		<div class="pbc-row-label wpb-inline"><?php _e('Ratings', 'YourChannel'); ?> <?php _e('Style', 'YourChannel'); ?></div>
		<div class="pbc-row-field wpb-inline">
			<div class="pbc-field wpb-inline">
				<label><input type="radio" name="rating_style" value="1" <%- parseInt(style.rating_style) ? 'checked' : '' %> class="wpb-raw"/><?php _e('Pie', 'YourChannel'); ?></label>
			</div>
			
			<div class="pbc-field wpb-inline">
				<label><input type="radio" name="rating_style" value="0" <%- parseInt(style.rating_style) ? '' : 'checked' %> class="wpb-raw"/><?php _e('Bar', 'YourChannel'); ?></label>
			</div>
		</div>
	</div>
	
	<div class="pbc-row">
		<div class="pbc-row-label wpb-inline"><?php _e('Pagination', 'YourChannel'); ?></div>
		<div class="pbc-row-field wpb-inline">
			<div class="pbc-field wpb-inline">
				<label><input type="checkbox" name="pagination" <%- style.pagination ? 'checked' : '' %> class="wpb-raw"/><?php _e('Previous / Next buttons', 'YourChannel'); ?></label>
			</div>
		</div>
	</div>
	
	<div class="pbc-row">
		<div class="pbc-row-label wpb-inline"><?php _e('Subscribe button', 'YourChannel'); ?></div>
		<div class="pbc-row-field wpb-inline">
			<div id="yrc-show-sub-button" class="pbc-field wpb-inline <%- style.banner ? 'wpb-force-hide' : ''  %>">
				<label><input type="checkbox" name="subscribe_button" <%- (style.subscribe_button || style.banner) ? 'checked' : '' %> class="wpb-raw"/><?php _e('Show', 'YourChannel'); ?></label>
			&nbsp; <b>|</b> </div>
		
			<div class="pbc-field wpb-inline">
				<label><input type="radio" name="subscriber" value="default" <%- (style.subscriber === 'default') ? 'checked' : '' %> class="wpb-raw"/><?php _e('Small', 'YourChannel'); ?></label>
			</div>
			
			<div class="pbc-field wpb-inline">
				<label><input type="radio" name="subscriber" value="full" <%- (style.subscriber === 'full') ? 'checked' : '' %> class="wpb-raw"/><?php _e('With name', 'YourChannel'); ?></label>
			</div>
			
			<b>|</b> &nbsp; &nbsp; <div class="pbc-field wpb-inline">
				<label><input type="radio" name="subscriber_count" value="default" <%- (style.subscriber_count === 'default') ? 'checked' : '' %> class="wpb-raw"/><?php _e('Show count', 'YourChannel'); ?></label>
			</div>
			
			<div class="pbc-field wpb-inline">
				<label><input type="radio" name="subscriber_count" value="hidden" <%- (style.subscriber_count === 'hidden') ? 'checked' : '' %> class="wpb-raw"/><?php _e('Hide count', 'YourChannel'); ?></label>
			</div>
		</div>
	</div>
	
	<div class="pbc-row">
		<div class="pbc-row-label wpb-inline"><?php _e('Custom', 'YourChannel'); ?> CSS</div>
		<div class="pbc-row-field wpb-inline">
			<textarea name="css" style="width:109%;" rows="6" class="wpb-raw"><%= css %></textarea>
			<a id="yrc-common-css" class="button"><?php _e('Pre-defined', 'YourChannel'); ?></a>
		</div>
	</div>
</script>


<script type="text/template" id="pbc-license-tmpl">
	<?php
	
		$license = get_option('yrc_license_key');
		$status = get_option('yrc_license_status');
		
		$license_action = 'Activate';
		if( $status === 'valid' ) $license_action = 'Deactivate';
		if( $status === 'deactivated' ) $license_action = 'Activate';
		if( $status === 'invalid' ) $license_action = 'Renew';
		
		if( $status === 'invalid' ) $license = '(Expired or Invalid) '.$license;
		
	 ?>
	
	<form actionn="<?php echo get_admin_url(); ?>admin-post.php" method="post" id="pbc-license-form" class="pbc-front-form">
		<h2 class="pbc-front-form-header wpb-pointer"><?php _e('License key', 'YourChannel'); ?></h2>
		<div class="pbc-front-form-inputs wpb-zero">
			<input type="text" name="yrc_license_key" required value="<?php echo $license; ?>"/>
			<input type="hidden" name="yrc_license_action" value="<?php echo $license_action; ?>"/>
			
			<?php if( $status !== 'invalid' ): ?>
				<div><button class="button button-primary"><?php _e($license_action.' License', 'YourChannel'); ?></button></div>
			<?php else: ?>
				<div>
					<a href="http://plugin.builders/checkout/?edd_license_key=<?php echo $license; ?>" class="button button-primary" target="_blank"><?php _e($license_action.' License', 'YourChannel'); ?></a>
					<button class="button"><?php _e('Activate License', 'YourChannel'); ?></button>
				</div>
			<?php endif; ?>
			
			</br><small><?php _e('License key is provided with your purchase receipt.', 'YourChannel'); ?></small>
			
			<div id="yrc-license-required">
				<a href="#"><?php _e('Bought before March 22, 2016', 'YourChannel'); ?> ?</a>
				<p class="pb-hidden">
					<?php _e('License keys are required to receive updates from YourChannel version 0.7 on.', 'YourChannel'); ?></br>
					<?php printf(__('If you bought the plugin before March 15th, your license key has been sent to your email address you used to buy YourChannel. Please check it.
						If for some reason you haven\'t got it, please write to us at %s', 'YourChannel'), '<a href="mailto:service@plugin.builders">service@plugin.builders</a>'); ?></br>
					<?php _e('Thanks a lot for co-operating, and sorry for the trouble, some customers have been abusing the update & copy system.', 'YourChannel'); ?>
				</p>
			</div>
		</div>
	</form>
</script>


<?php
	function yrcAddProAdminUITerms( $terms ){
		return array_merge($terms, array(
			'add_new' => __('Add New', 'YourChannel'),
			'delete_all' => __('Delete All', 'YourChannel')
		));
	}
	add_filter('yrc_admin_ui_terms', 'yrcAddProAdminUITerms');
?>
