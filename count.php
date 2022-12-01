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

 defined('ABSPATH') or die('die');

 class WordCountAndTimePlugin
 {
   function __construct(){
      
      // Main hooks
      add_action( 'admin_menu', array($this,'adminPage') , 10, 1 );
   }
   function adminPage(){
      add_options_page( 'Word Count Settings', 'Word Count', 'manage_options', 'word-count-settings-page', array($this,'ourHTML'), 3 );
    }
   
    function ourHTML(){?>
      <div class="wrap">
         <h1>Word Count Settings</h1>
      </div>
   <?php }
 }

$wordCountAndTimePlugin = new WordCountAndTimePlugin();

 

