<?php

	/*
	Plugin Name: Google Transliteration
	Plugin URI: http://www.moallemi.ir/en/blog/2009/10/10/google-transliteration-for-wordpress/
	Description: Google Transliteration support for wordpress.
	Version: 1.0
	Author: Reza Moallemi
	Author URI: http://www.moallemi.ir/blog
	*/

	load_plugin_textdomain('google-transliteration', NULL, dirname(plugin_basename(__FILE__)) . "/languages");
	
	add_action('admin_menu', 'g_trans_menu');

	function g_trans_menu() 
	{
		add_options_page('Google Transliteration Options', __('Google Transliteration', 'google-transliteration'), 8, 'google-transliteration', 'g_trans_options');
	}

	function get_g_trans_options()
	{
		$g_trans_options = array('default_language' => 'fa',
								'enable_comment_form' => 'true',
								'enable_default_comment_form' => 'true',
								'comment_form_id' => 'comment',
								'control_type' => 'single',
								'place_after_text_area' => 'false');
		$g_trans_save_options = get_option('g_trans_options');
		if (!empty($g_trans_save_options))
		{
			foreach ($g_trans_save_options as $key => $option)
			$g_trans_options[$key] = $option;
		}
		update_option('g_trans_options', $g_trans_options);
		return $g_trans_options;
	}
	
	function comment_form()
    {
		$g_trans_options = get_g_trans_options();
		if ($g_trans_options['enable_comment_form'] == 'true') 
		{
			if($g_trans_options['control_type'] == 'single')
			{
				?>
				<div style="padding-top:7px;" id='translControl'>
					<input type="checkbox" style="width:20px;margin:0;" id="chbxGtransliterate" onclick="g_transliteration();" />
					<small><?php _e("Enable Google Transliteration.(To type in English, press Ctrl+g)", "google-transliteration"); ?></small>
				</div>
				<div id="errorDiv"></div>
				<?php
			}
			else
			{
				?>
				<div style="padding-top:7px;" id='translControl'>
					<input type="checkbox" style="width:20px;margin:0;" id="chbxGtransliterate" onclick="g_transliteration();" />
					<small><?php _e("Enable Google Transliteration.(To type in English, press Ctrl+g)", "google-transliteration"); ?></small>
					<select id="languageDropDown" onchange="languageChangeHandler()"></select>
				</div>
				<div id="errorDiv"></div>	
				<?php
			}
				?>
				<script language="JavaScript" type="text/javascript">
				var urlp;            
				var mozilla = document.getElementById && !document.all;			
				var url = document.getElementById("url");
				try {
					if (mozilla)
						urlp = url.parentNode;
					else
						urlp = url.parentElement;
				}
				catch(ex){
					urlp = document.getElementById("commentform").children[0];
				}
				var sub = document.getElementById("translControl");
				<?php if($g_trans_options['place_after_text_area'] == 'true') {  ?>
				urlp = document.getElementById("commentform");
				<?php } ?>
				urlp.appendChild(sub, url);
			</script>
				<?php
		}
	}
	
	function post_form()
    {
		?>
		<div style="display:left;align:left; padding-top:7px;" id='translControl'>
		<small>(To type in English, press Ctrl+g)</small>
		</div>
		<div id="errorDiv"></div>
		<script language="JavaScript" type="text/javascript">
			var urlp;            
			var mozilla = document.getElementById && !document.all;			
            var url = document.getElementById("quicktags");
			if (mozilla)
	            urlp = url.parentNode;
			else
				    urlp = url.parentElement;
            var sub = document.getElementById("translControl");
            urlp.appendChild(sub, url);
        </script>	
		<?php
	}
	
	function wp_post_admin_scripts()
	{
		$g_trans_options = get_g_trans_options();
		?>		
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript">
		  // Load the Google Transliteration API
		  google.load("elements", "1", {
			packages: "transliteration"
		  });	  
		  google.setOnLoadCallback(onLoad);
		  var transliterationControl;
		  function onLoad() {
			var options = {
				sourceLanguage: google.elements.transliteration.LanguageCode.ENGLISH,
				destinationLanguage: ['<?php echo $g_trans_options['default_language']; ?>'],
				transliterationEnabled: true,
				shortcutKey: 'ctrl+g'
			};
			transliterationControl = new google.elements.transliteration.TransliterationControl(options);	
			var ids = [ "content", "title"];
			transliterationControl.makeTransliteratable(ids);		
			transliterationControl.enableTransliteration();
		  }
		</script> 
		<?php
	}
	
	function GoogleTransliteration() 
	{
		add_action('wp_head', 'wp_head_scripts');
		add_action('comment_form', 'comment_form');
	}

	function g_trans_options()
	{
		$g_trans_options = get_g_trans_options();
		if (isset($_POST['update_g_trans_settings']))
		{
			$g_trans_options['default_language'] = isset($_POST['default_language']) ? $_POST['default_language'] : 'fa';
			$g_trans_options['enable_comment_form'] = isset($_POST['enable_comment_form']) ? $_POST['enable_comment_form'] : 'false';
			$g_trans_options['enable_default_comment_form'] = isset($_POST['enable_default_comment_form']) ? $_POST['enable_default_comment_form'] : 'false';
			$g_trans_options['comment_form_id'] = (isset($_POST['comment_form_id']) and $_POST['comment_form_id'] != '') ? $_POST['comment_form_id'] : 'comment';
			$g_trans_options['control_type'] = (isset($_POST['control_type']) and $_POST['control_type'] != '') ? $_POST['control_type'] : 'single';
			$g_trans_options['place_after_text_area'] = (isset($_POST['place_after_text_area']) and $_POST['place_after_text_area'] != '') ? $_POST['place_after_text_area'] : 'false';

			update_option('g_trans_options', $g_trans_options);
			
			?>
			<div class="updated">
				<p><strong><?php _e("Settings Saved.","google-transliteration");?></strong></p>
			</div>
			<?php
		} ?>
		<div class=wrap>
		<?php if(function_exists('screen_icon')) screen_icon(); ?>
			<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
				<h2><?php _e('Google Transliteration Settings', 'google-transliteration'); ?></h2>
				<h3><?php _e('General Settings:', 'google-transliteration'); ?></h3>
				<p>
					<input type="radio" value="multi" name="control_type" <?php if ($g_trans_options['control_type'] == 'multi' ) echo ' checked="checked" '; ?> /> 
					<?php _e('Show list of languages to user.', 'google-transliteration'); ?>
				</p>
				<p><input type="radio" value="single" name="control_type" <?php if ($g_trans_options['control_type'] == 'single' ) echo ' checked="checked" '; ?> /> 
					<?php _e('Default Language:', 'google-transliteration'); ?> <select name="default_language">
								  
								  <option value="fa" <?php if ($g_trans_options['default_language'] == 'fa' ) echo ' selected="selected" '; ?> >Persian</option>
								  <option value="ar" <?php if ($g_trans_options['default_language'] == 'ar' ) echo ' selected="selected" '; ?> >Arabic</option>
								  <option value="bn" <?php if ($g_trans_options['default_language'] == 'bn' ) echo ' selected="selected" '; ?> >Bengali</option>
								  <option value="gu" <?php if ($g_trans_options['default_language'] == 'gu' ) echo ' selected="selected" '; ?> >Gujarati</option>
								  <option value="hi" <?php if ($g_trans_options['default_language'] == 'hi' ) echo ' selected="selected" '; ?> >Hindi</option>
								  <option value="kn" <?php if ($g_trans_options['default_language'] == 'kn' ) echo ' selected="selected" '; ?> >Kannada</option>
								  <option value="ml" <?php if ($g_trans_options['default_language'] == 'ml' ) echo ' selected="selected" '; ?> >Malayalam</option>
								  <option value="mr" <?php if ($g_trans_options['default_language'] == 'mr' ) echo ' selected="selected" '; ?> >Marathi</option>
								  <option value="ne" <?php if ($g_trans_options['default_language'] == 'ne' ) echo ' selected="selected" '; ?> >Nepali</option>
								  <option value="pa" <?php if ($g_trans_options['default_language'] == 'pa' ) echo ' selected="selected" '; ?> >Punjabi</option>
								  <option value="ta" <?php if ($g_trans_options['default_language'] == 'ta' ) echo ' selected="selected" '; ?> >Tamil</option>
								  <option value="te" <?php if ($g_trans_options['default_language'] == 'te' ) echo ' selected="selected" '; ?> >Telugu</option>
								  <option value="ur" <?php if ($g_trans_options['default_language'] == 'ur' ) echo ' selected="selected" '; ?> >Urdu</option>
								</select>
				</p>
				<h3><?php _e('Comment Settings:', 'google-transliteration'); ?></h3>
				<p><input name="enable_comment_form" value="true" type="checkbox" <?php if ($g_trans_options['enable_comment_form'] == 'true' ) echo ' checked="checked" '; ?> /> <?php _e('Enable for comment form.', 'google-transliteration'); ?></p>
				<p><input name="enable_default_comment_form" value="true" type="checkbox" <?php if ($g_trans_options['enable_default_comment_form'] == 'true' ) echo ' checked="checked" '; ?> /> <?php _e('Enable Google Transliteration by default.', 'google-transliteration'); ?></p>
				<p><input name="place_after_text_area" value="true" type="checkbox" <?php if ($g_trans_options['place_after_text_area'] == 'true' ) echo ' checked="checked" '; ?> /> <?php _e('Put the settings after comment textarea.', 'google-transliteration'); ?></p>
				<p><?php _e('Comment text field id: ', 'google-transliteration'); ?> 
					<input name="comment_form_id" style="direction:ltr;" type="text" value="<?php echo $g_trans_options['comment_form_id']; ?>" /> 
					<small><?php _e('Default for Wordpress Themes is <b>comment</b>', 'google-transliteration'); ?></small>
				</p>
				<div class="submit">
					<input class="button-primary" type="submit" name="update_g_trans_settings" value="<?php _e('Save Changes', 'google-transliteration') ?>" />
				</div>
				<hr />
				<div>
					<h4><?php _e('My other plugins for wordpress:', 'google-transliteration'); ?></h4>
					<ul>
						<li><b>- <?php _e('Advanced User Agent Displayer ', 'google-transliteration'); ?></b>
							(<a href="http://wordpress.org/extend/plugins/advanced-user-agent-displayer/"><?php _e('Download', 'google-transliteration'); ?></a> | 
							<a href="<?php _e('http://www.moallemi.ir/en/blog/2009/09/20/advanced-user-agent-displayer/', 'google-transliteration'); ?>"><?php _e('More Information', 'google-transliteration'); ?></a>)
						</li>
						<li><b>- <?php _e('Behnevis Transliteration ', 'google-transliteration'); ?></b> 
							(<a href="http://wordpress.org/extend/plugins/behnevis-transliteration/"><?php _e('Download', 'google-transliteration'); ?></a> | 
							<a href="http://www.moallemi.ir/blog/1388/07/25/%D8%A7%D9%81%D8%B2%D9%88%D9%86%D9%87-%D9%86%D9%88%DB%8C%D8%B3%D9%87-%DA%AF%D8%B1%D8%AF%D8%A7%D9%86-%D8%A8%D9%87%D9%86%D9%88%DB%8C%D8%B3-%D8%A8%D8%B1%D8%A7%DB%8C-%D9%88%D8%B1%D8%AF%D9%BE%D8%B1%D8%B3/"><?php _e('More Information', 'google-transliteration'); ?></a> )
						</li>
						<li><b>- <?php _e('Comments On Feed ', 'google-transliteration'); ?></b> 
							(<a href="http://wordpress.org/extend/plugins/comments-on-feed/"><?php _e('Download', 'google-transliteration'); ?></a> | 
							<a href="<?php _e('http://www.moallemi.ir/en/blog/2009/12/18/comments-on-feed-for-wordpress/', 'google-transliteration'); ?>"><?php _e('More Information', 'google-transliteration'); ?></a>)
						</li>
						<li><b>- <?php _e('Feed Delay ', 'google-transliteration'); ?></b> 
							(<a href="http://wordpress.org/extend/plugins/feed-delay/"><?php _e('Download', 'google-transliteration'); ?></a> | 
							<a href="<?php _e('http://www.moallemi.ir/en/blog/2010/02/25/feed-delay-for-wordpress/', 'google-transliteration'); ?>"><?php _e('More Information', 'google-transliteration'); ?></a>)
						</li>
						<li><b>- <?php _e('Contact Commenter ', 'google-transliteration'); ?></b> 
							(<a href="http://wordpress.org/extend/plugins/contact-commenter/"><?php _e('Download', 'google-transliteration'); ?></a> | 
							<a href="<?php _e('http://www.moallemi.ir/blog/1388/12/27/%d9%87%d8%af%db%8c%d9%87-%da%a9%d8%a7%d9%88%d8%b4%da%af%d8%b1-%d9%85%d9%86%d8%a7%d8%b3%d8%a8%d8%aa-%d8%b3%d8%a7%d9%84-%d9%86%d9%88-%d9%88%d8%b1%d8%af%d9%be%d8%b1%d8%b3/', 'google-transliteration'); ?>"><?php _e('More Information', 'google-transliteration'); ?></a>)
						</li>
					</ul>
				</div>
			</form>
		</div>
		<?php
	}
							
	function wp_head_scripts() 
	{	
		$g_trans_options = get_g_trans_options();
		if ((is_single() || is_page()) and $g_trans_options['enable_comment_form'] == 'true') 
		{
			
		?>		
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript">
		// Load the Google Transliteration API
		google.load("elements", "1", { packages: "transliteration" });	  
		google.setOnLoadCallback(onLoad);
		var transliterationControl;
		function onLoad() {
			var options = {
				sourceLanguage: google.elements.transliteration.LanguageCode.ENGLISH,
				<?php if($g_trans_options['control_type'] == 'single') {?>
				destinationLanguage: ['<?php echo $g_trans_options['default_language']; ?>'],
				<?php } else { ?>
				destinationLanguage: ['ar', 'bn', 'gu', 'hi', 'kn', 'ml', 'mr', 'ne', 'fa', 'pa', 'ta', 'te', 'ur'],
				<?php } ?>
				transliterationEnabled: true,
				shortcutKey: 'ctrl+g'
			};
			transliterationControl = new google.elements.transliteration.TransliterationControl(options);	
			var ids = ['<?php echo $g_trans_options['comment_form_id']; ?>'];
			transliterationControl.makeTransliteratable(ids);
			<?php if($g_trans_options['enable_default_comment_form'] == 'true') {  ?>
				transliterationControl.enableTransliteration();
			<?php } else { ?>
				transliterationControl.disableTransliteration();
			<?php } ?>
			transliterationControl.addEventListener(
				google.elements.transliteration.TransliterationControl.EventType.STATE_CHANGED,
				transliterateStateChangeHandler);
			document.getElementById('chbxGtransliterate').checked = transliterationControl.isTransliterationEnabled();
			
			<?php if($g_trans_options['control_type'] == 'multi') {?>
			var destinationLanguage =
			  transliterationControl.getLanguagePair().destinationLanguage;
			var languageSelect = document.getElementById('languageDropDown');
			var supportedDestinationLanguages =
			  google.elements.transliteration.getDestinationLanguages(
				google.elements.transliteration.LanguageCode.ENGLISH);
				
			for (var lang in supportedDestinationLanguages) {
			  var opt = document.createElement('option');
			  opt.text = lang;
			  opt.value = supportedDestinationLanguages[lang];
			  if (destinationLanguage == opt.value) {
				opt.selected = true;
			  }
			  try {
				languageSelect.add(opt, null);
			  } catch (ex) {
				languageSelect.add(opt);
			  }
			}
		
			<?php } ?>
			
			}
		function transliterateStateChangeHandler(e) {
			document.getElementById('chbxGtransliterate').checked = e.transliterationEnabled;
		 }
		function g_transliteration() {
			transliterationControl.toggleTransliteration();
		  }
		  
		<?php if($g_trans_options['control_type'] == 'multi') {?>
			function languageChangeHandler() {
			var dropdown = document.getElementById('languageDropDown');
			transliterationControl.setLanguagePair(
				google.elements.transliteration.LanguageCode.ENGLISH,
				dropdown.options[dropdown.selectedIndex].value);
		}
		<?php } ?>
		
		</script>
		<?php
		}
	}
	
	$plugin = plugin_basename(__FILE__); 
	add_filter("plugin_action_links_$plugin", 'g_trans_links' );
	
	function g_trans_links($links)
	{ 
		$settings_link = '<a href="options-general.php?page=google-transliteration">'.__('Settings', 'google-transliteration').'</a>';
		array_unshift($links, $settings_link); 
		return $links; 
	}
	
	GoogleTransliteration();
			
?>
