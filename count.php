<?php
/**
 * Plugin Name: Word Count plugin
 * Description: A truly amazing plugin for post word count, and read time approximation.
 * Version: 1.0.0
 * Author: Mike Kipruto
 * Author URI: https://kipmyk.co.ke/
 * Text Domain: wcpdomain
 * Domain Path: /languages
 */

if(!defined('ABSPATH')) exit; //exist if access directly

class WordCountAndTimePlugin {
   function __construct() {
     add_action('admin_menu', array($this, 'adminPage'));
     add_action('admin_init', array($this, 'settings'));
     add_filter( 'the_content', array($this, 'ifWrap'));
     add_action( 'init', array($this, 'languages') );
   }

  //  handle translation
  function languages(){
    load_plugin_textdomain( 'wcpdomain', false, dirname(plugin_basename( __FILE__ )).'/languages');
  }

   // adding to single posts and if checked.

   function ifWrap($content){
      if(is_main_query() AND is_single() AND 
      (
         get_option('wcp_wordcount', 1) OR 
         get_option('wcp_charactercount', 1) OR 
         get_option('wcp_readtime', 1) 
      )){
            return $this->createHTML($content);
      }
      return $content;
   }

   function createHTML($content){ 
      $html = '<h3>'. esc_html( get_option('wcp_headline', 'Post Statitics') ) . '</h3> <p>';

      // grab the word count and save it to the variable.

      if(get_option('wcp_wordcount', 1) OR get_option('wcp_readtime', 1)){
        $wordcount = str_word_count(strip_tags($content));
      }

      if(get_option('wcp_wordcount', 1)){
        $html .= esc_html__('This post has', 'wcpdomain') . ' ' . '<b>'. $wordcount . '</b>'.' ' . esc_html__('words','wcpdomain') . '<br>';
      }

      if(get_option('wcp_charactercount', 1)){
        $html .= esc_html__('This post has', 'wcpdomain') .' ' . '<b>'. strlen(strip_tags($content)) . '</b>'. ' ' . __('characters', 'wcpdomain') . '<br>';
      }

      if(get_option('wcp_readtime', 1)){
        $html .= esc_html__('This will take about', 'wcpdomain') .' ' . '<b>'.round($wordcount/255).'</b>' .' ' . esc_html__('minute(s) to read', 'wcpdomain') .'<br>';
      }

      $html .='</p>';

      // add to the beginning of the blog content
      if(get_option( 'wcp_location', '0') == '0'){
        return  $html . $content;
      }
      // add to the beginning of the blog content
      return  $content . $html;
   }

   function settings() {
      add_settings_section('wcp_first_section', null, null, 'word-count-settings-page');
      
      //Register Display location setttings
      add_settings_field('wcp_location', 'Display Location', array($this, 'locationHTML'), 'word-count-settings-page', 'wcp_first_section');
      register_setting('wordcountplugin', 'wcp_location', array('sanitize_callback' => array($this, 'sanitizeLocation'), 'default' => '0'));
      
      //Register the Headline Text settings
      add_settings_field('wcp_headline', 'Headline Text', array($this, 'headlineHTML'), 'word-count-settings-page', 'wcp_first_section');
      register_setting('wordcountplugin', 'wcp_headline', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics'));
      
      //Register the Word count settings
      add_settings_field('wcp_wordcount', 'Word Count', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_first_section', array('theName' => 'wcp_wordcount'));
      register_setting('wordcountplugin', 'wcp_wordcount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
      
      //Register the Character count settings
      add_settings_field('wcp_charactercount', 'Character Count', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_first_section', array('theName' => 'wcp_charactercount'));
      register_setting('wordcountplugin', 'wcp_charactercount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
      
      //Register the toggle options settings
      add_settings_field('wcp_readtime', 'Read Time', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_first_section', array('theName' => 'wcp_readtime'));
      register_setting('wordcountplugin', 'wcp_readtime', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
    }
  
    function sanitizeLocation($input) {
      if ($input != '0' AND $input != '1') {
        add_settings_error('wcp_location', 'wcp_location_error', 'Display location must be either beginning or end.');
        return get_option('wcp_location');
      }
      return $input;
    }
  
    /*
    function wordcountHTML() { ?>
      <input type="checkbox" name="wcp_wordcount" value="1" <?php checked(get_option('wcp_wordcount'), '1') ?>>
    <?php }
  
    function charactercountHTML() { ?>
      <input type="checkbox" name="wcp_charactercount" value="1" <?php checked(get_option('wcp_charactercount'), '1') ?>>
    <?php }
  
    function readtimeHTML() { ?>
      <input type="checkbox" name="wcp_readtime" value="1" <?php checked(get_option('wcp_readtime'), '1') ?>>
    <?php }
    */
  
    // reusable checkbox function
    function checkboxHTML($args) { ?>
      <input type="checkbox" name="<?php echo $args['theName'] ?>" value="1" <?php checked(get_option($args['theName']), '1') ?>>
    <?php }
  
    function headlineHTML() { ?>
      <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')) ?>">
    <?php }
  
    function locationHTML() { ?>
      <select name="wcp_location">
        <option value="0" <?php selected(get_option('wcp_location'), '0') ?>>Beginning of post</option>
        <option value="1" <?php selected(get_option('wcp_location'), '1') ?>>End of post</option>
      </select>
    <?php }
  
    function adminPage() {
      add_options_page('Word Count Settings', __('Word Count', 'wcpdomain'), 'manage_options', 'word-count-settings-page', array($this, 'ourHTML'));
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