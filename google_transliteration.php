<?php

	/*
	Plugin Name: Google Transliteration
	Plugin URI: http://www.moallemi.ir/en/blog/2009/10/10/google-transliteration-for-wordpress/
	Description: Google Transliteration support for wordpress.
	Version: 0.8
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
								'comment_form_id' => 'comment');
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
			?>
			<div style="padding-top:7px;" id='translControl'>
			<input type="checkbox" id="chbxGtransliterate" onclick="g_transliteration();" />
			<small><?php _e("Enable Google Transliteration.(To type in English, press Ctrl+g)", "google-transliteration"); ?></small>
			</div>
			<div id="errorDiv"></div>
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

			update_option('g_trans_options', $g_trans_options);
			?>
			<div class="updated">
				<p><strong><?php _e("Settings Saved.","google-transliteration");?></strong></p>
			</div>
			<?php
		} ?>
		<div class=wrap>
			<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
				<h2><?php _e('Google Transliteration Settings', 'google-transliteration'); ?></h2>
				<h3><?php _e('General Settings:', 'google-transliteration'); ?></h3>
				<p><?php _e('Default Language:', 'google-transliteration'); ?> <select name="default_language">
								  
								  <option value="fa" <?php if ($g_trans_options['default_language'] == 'fa' ) echo ' selected="selected" '; ?> >فارسی</option>
								  <option value="ar" <?php if ($g_trans_options['default_language'] == 'ar' ) echo ' selected="selected" '; ?> >العربیه</option>
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
				<p><?php _e('Comment text field id: ', 'google-transliteration'); ?> 
					<input name="comment_form_id" style="direction:ltr;" type="text" value="<?php echo $g_trans_options['comment_form_id']; ?>" /> 
					<small><?php _e('Default for Wordpress Themes is <b>comment</b>', 'google-transliteration'); ?></small>
				</p>
				<div class="submit">
					<input type="submit" name="update_g_trans_settings" value="<?php _e('Save Changes', 'google-transliteration') ?>" />
				</div>
				<hr />
				<div>
					<h4><?php _e('My other plugins for wordpress:', 'google-transliteration'); ?></h4>
					<ul>
						<li>- <b><?php _e('Advanced User Agent Displayer ', 'google-transliteration'); ?></b>
							(<a href="http://wordpress.org/extend/plugins/advanced-user-agent-displayer/"><?php _e('Download', 'google-transliteration'); ?></a> | 
							<a href="<?php _e('http://www.moallemi.ir/en/blog/2009/09/20/advanced-user-agent-displayer/', 'google-transliteration'); ?>"><?php _e('More Information', 'google-transliteration'); ?></a>)
						</li>
						<li>- <b><?php _e('Behnevis Transliteration ', 'google-transliteration'); ?></b> 
							(<a href="http://wordpress.org/extend/plugins/behnevis-transliteration/"><?php _e('Download', 'google-transliteration'); ?></a> | 
							<a href="http://www.moallemi.ir/blog/1388/07/25/%D8%A7%D9%81%D8%B2%D9%88%D9%86%D9%87-%D9%86%D9%88%DB%8C%D8%B3%D9%87-%DA%AF%D8%B1%D8%AF%D8%A7%D9%86-%D8%A8%D9%87%D9%86%D9%88%DB%8C%D8%B3-%D8%A8%D8%B1%D8%A7%DB%8C-%D9%88%D8%B1%D8%AF%D9%BE%D8%B1%D8%B3/"><?php _e('More Information', 'google-transliteration'); ?></a> )
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
				destinationLanguage: ['<?php echo $g_trans_options['default_language']; ?>'],
				transliterationEnabled: <?php echo $g_trans_options['enable_default_comment_form']; ?>,
				shortcutKey: 'ctrl+g'
			};
			transliterationControl = new google.elements.transliteration.TransliterationControl(options);	
			var ids = ['<?php echo $g_trans_options['comment_form_id']; ?>'];
			transliterationControl.makeTransliteratable(ids);		
			transliterationControl.enableTransliteration();
			transliterationControl.addEventListener(
				google.elements.transliteration.TransliterationControl.EventType.STATE_CHANGED,
				transliterateStateChangeHandler);
			document.getElementById('chbxGtransliterate').checked = transliterationControl.isTransliterationEnabled();
			}
		function transliterateStateChangeHandler(e) {
			document.getElementById('chbxGtransliterate').checked = e.transliterationEnabled;
		 }
		function g_transliteration() {
			transliterationControl.toggleTransliteration();
		  }
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
