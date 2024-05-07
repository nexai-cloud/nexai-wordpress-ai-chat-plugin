<?php
/**
 * @package Nexai_Chat
 * @version 1.0.0
 */
/*
Plugin Name: Nexai Chat
Plugin URI: https://nexai.site/docs/wordpress-nexai-chat
Description: Displays Nexai AI Live Support Chat Bubble in every Wordpress page
Author: nexai.site
Version: 1.0.0
Author URI: https://nexai.site
*/


// Chat bubble JS
function nexai_chat_js() {
  $jsBuffer = '';
  $jsBuffer .= "<script src=\"https://nexai.site/ai/embed.umd.js\"></script>\n";
  $jsBuffer .= "<script>\n";
  $jsBuffer .= "// edit the configurations for your website\n";
  $jsBuffer .= "NexaiChatBubble.render({\n";
  $jsBuffer .= "  bottom: " . (int)get_option('nexai_chat_bottom', 30) . ",\n";
  $jsBuffer .= "  right: " . (int)get_option('nexai_chat_right', 30) . ",\n";
  $jsBuffer .= "  width: " . (int)get_option('nexai_chat_width', 400) . ",\n";
  $jsBuffer .= "  nexaiApiKey: '" . trim(get_option('nexai_chat_api_key', '')) . "',\n";
  $jsBuffer .= "  aiName: '" . trim(get_option('nexai_chat_ai_name', 'AI Assistant')) . "',\n";
  $jsBuffer .= "  aiAvatarUrl: '" . trim(get_option('nexai_chat_ai_avatar_url', '')) . "',\n";
  $jsBuffer .= "  projectName: '" . trim(get_option('nexai_chat_project_name', 'this Website')) . "',\n";
  $jsBuffer .= "  inputPlaceholder: '" . trim(get_option('nexai_chat_input_placeholder', '')) . "',\n";
  $jsBuffer .= "  chatSuggests: '" . get_option('nexai_chat_chat_suggests', '') . "',\n"; 
  $jsBuffer .= "  integration: 'wordpress',\n"; 
  // Get current user info
  $current_user = wp_get_current_user();
  $userName = $current_user->display_name;
  $userEmail = $current_user->user_email;
  $userAvatarUrl = get_avatar_url($current_user->ID);
  $jsBuffer .= "  userName: '" . esc_js($userName) . "',\n"; // Escape JavaScript output
  $jsBuffer .= "  userEmail: '" . esc_js($userEmail) . "',\n"; // Escape JavaScript output
  $jsBuffer .= "  userAvatarUrl: '" . esc_js($userAvatarUrl) . "'\n"; // Escape JavaScript output
  $jsBuffer .= "});\n";
  $jsBuffer .= "</script>\n";

  echo $jsBuffer;
}


add_action( 'wp_footer', 'nexai_chat_js' );


// ----- ADMIN ------ 

// Add a menu item for the settings page
function nexai_settings_page() {
  add_menu_page(
    'Nexai - AI Live Chat Support',
    'Nexai Settings',
    'manage_options',
    'nexai-chat-settings',
    'nexai_render_settings_page',
    'https://nexai.site/logo/round-24.png'
  );
  add_action( 'admin_init', 'nexai_plugin_register_settings' );
}


add_action('admin_menu', 'nexai_settings_page');

// Render the settings page
function nexai_render_settings_page() {
  // check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// add error/update messages

	// check if the user have submitted the settings
	// WordPress will add the "settings-updated" $_GET parameter to the url
	if ( isset( $_GET['settings-updated'] ) ) {
		// add settings saved message with the class of "updated"
		add_settings_error( 'nexai_messages', 'nexai_message', __( 'Settings Saved', 'nexai' ), 'updated' );
	}

	// show error/update messages
	settings_errors( 'nexai_messages' );
  ?>
  <div class="wrap">
      <h2>Nexai - AI Live Chat Support</h2>
      <form method="post" action="options.php">
          <?php
          settings_fields('nexai_chat_settings');
          do_settings_sections('nexai_chat_settings');
          submit_button('Save Settings');
          ?>
      </form>
  </div>
  <?php
}

// Register settings
function nexai_plugin_register_settings() {
  register_setting('nexai_chat_settings', 'nexai_chat_api_key');
  register_setting('nexai_chat_settings', 'nexai_chat_ai_name');
  register_setting('nexai_chat_settings', 'nexai_chat_ai_avatar_url');
  register_setting('nexai_chat_settings', 'nexai_chat_chat_suggests');
  register_setting('nexai_chat_settings', 'nexai_chat_width');
  register_setting('nexai_chat_settings', 'nexai_chat_bottom');
  register_setting('nexai_chat_settings', 'nexai_chat_right');
  register_setting('nexai_chat_settings', 'nexai_chat_project_name');
  register_setting('nexai_chat_settings', 'nexai_chat_input_placeholder');
  register_setting('nexai_chat_settings', 'nexai_chat_nexai_io_url');
  register_setting('nexai_chat_settings', 'nexai_chat_assets_url');

  
  add_settings_section('nexai_chat_settings_section', 'Required Settings', 'nexai_section_cb', 'nexai_chat_settings');
  add_settings_section('nexai_chat_ai_settings_section', 'AI Settings', 'nexai_section_cb', 'nexai_chat_settings');
  add_settings_section('nexai_chat_display_settings_section', 'Display Settings', 'nexai_section_cb', 'nexai_chat_settings');
  add_settings_section('nexai_chat_advanced_settings_section', 'Advanced Settings', 'nexai_section_cb', 'nexai_chat_settings');
  
  add_settings_field('nexai_chat_api_key', 'Nexai API Key', 'nexai_field_cb', 'nexai_chat_settings', 'nexai_chat_settings_section',                               array('name' => 'nexai_chat_api_key'));
  add_settings_field('nexai_chat_ai_name', 'AI Name', 'nexai_field_cb', 'nexai_chat_settings', 'nexai_chat_ai_settings_section',                                  array('name' => 'nexai_chat_ai_name', 'default' => 'Nexai'));
  add_settings_field('nexai_chat_ai_avatar_url', 'AI Avatar URL', 'nexai_field_cb', 'nexai_chat_settings', 'nexai_chat_ai_settings_section',                      array('name' => 'nexai_chat_ai_avatar_url'));
  add_settings_field('nexai_chat_chat_suggests', 'Chat Suggests', 'nexai_field_cb', 'nexai_chat_settings', 'nexai_chat_ai_settings_section',                      array('name' => 'nexai_chat_chat_suggests'));
  add_settings_field('nexai_chat_width', 'Width', 'nexai_field_cb', 'nexai_chat_settings', 'nexai_chat_display_settings_section',                                 array('name' => 'nexai_chat_width', 'default' => '400'));
  add_settings_field('nexai_chat_bottom', 'Bottom (pixels from bottom of page)', 'nexai_field_cb', 'nexai_chat_settings', 'nexai_chat_display_settings_section',  array('name' => 'nexai_chat_bottom', 'default' => '60'));
  add_settings_field('nexai_chat_right', 'Right (pixels from right of page)', 'nexai_field_cb', 'nexai_chat_settings', 'nexai_chat_display_settings_section',     array('name' => 'nexai_chat_right', 'default' => '60'));
  add_settings_field('nexai_chat_project_name', 'Project Name', 'nexai_field_cb', 'nexai_chat_settings', 'nexai_chat_display_settings_section',                   array('name' => 'nexai_chat_project_name'));
  add_settings_field('nexai_chat_input_placeholder', 'Input Placeholder', 'nexai_field_cb', 'nexai_chat_settings', 'nexai_chat_display_settings_section',         array('name' => 'nexai_chat_input_placeholder', 'default' => 'Ask a question...'));
  add_settings_field('nexai_chat_nexai_io_url', 'Nexai IO URL', 'nexai_field_cb', 'nexai_chat_settings', 'nexai_chat_advanced_settings_section',                  array('name' => 'nexai_chat_nexai_io_url'));
  add_settings_field('nexai_chat_assets_url', 'Nexai Assets URL', 'nexai_field_cb', 'nexai_chat_settings', 'nexai_chat_advanced_settings_section',                array('name' => 'nexai_chat_assets_url'));

  
}

function nexai_section_cb( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>">
    <?php 
      if ($args['id'] === 'nexai_chat_settings_section') {
        echo 'Get your free API key from <a target="_blank" href="https://nexai.site">Nexai.site</a>';
      }
    ?>
  </p>
	<?php
}

function nexai_field_cb( $args ) {
  $value = get_option($args['name'], $args['default']);
  
	?>
	<p id="<?php echo esc_attr( $args['name'] ); ?>">
    <input name="<?php echo esc_attr( $args['name'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
  </p>
	<?php
}
