<?php

	/*
	Plugin Name: Google Transliteration
	Plugin URI: http://www.moallemi.ir/en/blog/2009/10/10/google-transliteration-for-wordpress/
	Description: Google Transliteration support for wordpress.
	Version: 0.6
	Author: Reza Moallemi
	Author URI: http://www.moallemi.ir/blog
	*/

	load_plugin_textdomain('google-transliteration', NULL, dirname(plugin_basename(__FILE__)) . "/languages");
	
	function GoogleTransliteration() 
	{
		add_action('wp_head', 'wp_head_scripts');
	}
	
	add_action('admin_menu', 'g_trans_menu');

	function g_trans_menu() 
	{
		add_options_page('Google Transliteration Options', __('Google Transliteration', 'google-transliteration'), 8, 'google-transliteration', 'g_trans_options');
	}

	function get_g_trans_options()
	{
		$g_trans_options = array('default_language' => 'fa',
								'enable_comment_form' => 'true');
		$g_trans_save_options = get_option('g_trans_options');
		if (!empty($g_trans_save_options))
		{
			foreach ($g_trans_save_options as $key => $option)
			$g_trans_options[$key] = $option;
		}
		update_option('g_trans_options', $g_trans_options);
		return $g_trans_options;
	}

	function g_trans_options()
	{
		$g_trans_options = get_g_trans_options();
		if (isset($_POST['update_auad_settings']))
		{
			$g_trans_options['default_language'] = isset($_POST['default_language']) ? $_POST['default_language'] : 'fa';
			$g_trans_options['enable_comment_form'] = isset($_POST['enable_comment_form']) ? $_POST['enable_comment_form'] : 'false';
			$g_trans_options['enable_new_post_form'] = isset($_POST['enable_new_post_form']) ? $_POST['enable_new_post_form'] : 'false';

			update_option('g_trans_options', $g_trans_options);
			?>
			<div class="updated">
				<p><strong><?php _e("Settings Saved.","google");?></strong></p>
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
				<p><input name="enable_comment_form" value="true" type="checkbox" <?php if ($g_trans_options['enable_comment_form'] == 'true' ) echo ' checked="checked" '; ?> /> <?php _e('enable for comment form.', 'google-transliteration'); ?></p>
				<div class="submit">
					<input type="submit" name="update_auad_settings" value="<?php _e('Save Changes', 'google-transliteration') ?>" />
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
			var ids = [ "comment"];
			transliterationControl.makeTransliteratable(ids);		
			transliterationControl.enableTransliteration();
		  } 
		</script>
		<?php
		}
	}
	
	GoogleTransliteration();
		
?>
