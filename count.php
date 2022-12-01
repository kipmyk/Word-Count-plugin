<?php
/**
 * Plugin Name: Word Count plugin
 * Description: Creating plugin settings with React JS.
 * Version: 1.0.0
 * Author: mikekipruto
 * Author URI: https://kipmyk.co.ke/
 * Text Domain: wordcount
 * 
 */

class WordCountAndTimePlugin {
   function __construct() {
     add_action('admin_menu', array($this, 'adminPage'));
     add_action('admin_init', array($this, 'settings'));
   }
 
   function settings() {
     add_settings_section('wcp_first_section', null, null, 'word-count-settings-page');
     add_settings_field('wcp_location', 'Display Location', array($this, 'locationHTML'), 'word-count-settings-page', 'wcp_first_section');
     register_setting('wordcountplugin', 'wcp_location', array('sanitize_callback' => 'sanitize_text_field', 'default' => '0'));
   }
 
   function locationHTML() { ?>
     <select name="wcp_location">
     <option value="0" <?php selected(get_option('wcp_location'), '0') ?>>Beginning of post</option>
      <option value="1" <?php selected(get_option('wcp_location'), '1') ?>>End of post</option>
    </select>
   <?php }
 
   function adminPage() {
     add_options_page('Word Count Settings', 'Word Count', 'manage_options', 'word-count-settings-page', array($this, 'ourHTML'));
   }
 
   function ourHTML() { ?>
     <div class="wrap">
       <h1>Word Count Settings</h1>
       <form action="options.php" method="POST">
       <?php
         settings_fields('wordcountplugin');
         do_settings_sections('word-count-settings-page');
         submit_button();
       ?>
       </form>
     </div>
   <?php }
 }
 
 $wordCountAndTimePlugin = new WordCountAndTimePlugin(); 