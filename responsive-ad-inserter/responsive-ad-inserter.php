<?php
/*
Plugin Name: Responsive Ad Inserter
Plugin URI: http://www.techtuft.com/
Description: The plugin lets you insert ads inside posts, pages nad various other places as you like it.
Version: 0.1
Author: Plato Puthur
Author URI: http://www.techtuft.com
License: GPL2
*/
?>
<?php
/*  Copyright 2015  Plato Puthur  (email : plato.puthur@gmail.com)
    This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
    You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
  defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

  // add the admin options page
    add_action('admin_menu', 'plugin_admin_add_page');
    add_action( 'admin_enqueue_scripts','load_boostrap' );
    function plugin_admin_add_page() {
        add_options_page('Responsive Ad Inserter', 'RAI Menu', 'install_plugins', 'rai_plugin', 'rai_options_page');
    }

    function rai_options_page() {?>
    <div class="row" style="padding-top: 20px;">
      <div class=" well col-md-6 pull-left">
        <h2>Responsive Ad Inserter</h2>
        Set the position of the ad here!
        <form name="rai_form" action="options.php" method="post">
          <?php settings_fields('rai_options_group'); ?>
          <?php do_settings_sections('rai_plugin'); ?>
          <div align=center><br />
            <input class="btn btn-primary" id="rai_main" name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
          </div>
        </form>
      </div>
      <div class=" col-md-4">
        <div class="col-md-6 well pull-right">
          <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="7EP5PT8ZQ4BPG">
            <table>
              <tr><td><input type="hidden" name="on0" value="Buy Me Something If You Like the Plugin">Please Donate to Support the Developer. </td>               </tr><tr><td><select name="os0">
              <option value="I like the plugin">I like the plugin $10.00 USD</option>
              <option value="I love the plugin">I love the plugin $15.00 USD</option>
              <option value="Simply awesome">Simply awesome $20.00 USD</option>
              </select> </td></tr>
            </table>
            <input type="hidden" name="currency_code" value="USD">
            <input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
          </form>
        </div>
      </div>
    </div>
<?php }
    // add the admin settings and such
    add_action('admin_init', 'rai_admin_init');

    function rai_admin_init() {
      register_setting( 'rai_options_group', 'rai_options', 'rai_options_validate' );

      add_settings_section( 'rai_main', '', 'rai_section_text', 'rai_plugin' );
      ?><div class="form-group"><?php
      add_settings_field('rai_page_type', 'Type', 'rai_page_type_input', 'rai_plugin', 'rai_main');
      ?></div>
      <div class="form-group"><?php
      add_settings_field('rai_ad_position', 'Position', 'rai_ad_position_input', 'rai_plugin', 'rai_main');
      ?></div>
      <div class="form-group"><?php
      add_settings_field('rai_ad_code_top', 'Code for Top position', 'rai_top_ad_code_input', 'rai_plugin', 'rai_main');
      ?></div>
      <div class="form-group"><?php
      add_settings_field('rai_ad_code_middle', 'Code for Middle position', 'rai_middle_ad_code_input', 'rai_plugin', 'rai_main');
      ?></div>
      <div class="form-group"><?php
      add_settings_field('rai_ad_code_bottom', 'Code for Bottom position', 'rai_bottom_ad_code_input', 'rai_plugin', 'rai_main');
      ?></div><?php
    }

    //validating user input
    function rai_options_validate() {
      $options = get_option('rai_options');
      return $input;
    }

    //functions for displaying each of the admin settings in the admin page
    function rai_page_type_input() {
      $options = get_option('rai_options'); ?>
      <input type="checkbox" name="rai_page_type[]" value="post" class="form-control" />Post<br />
      <input type="checkbox" name="rai_page_type[]" value="page" class="form-control" />Page<br /> <?php
    }
    function rai_ad_position_input() {
      $options = get_option('rai_options'); ?>
      <input type="checkbox" name="rai_position[]" value="top" class="form-control" />Top<br />
      <input type="checkbox" name="rai_position[]" value="middle" class="form-control" />Middle<br />
      <input type="checkbox" name="rai_position[]" value="bottom" class="form-control" />Bottom<br /> <?php
    }
    function rai_top_ad_code_input() {
      $options = get_option('rai_options'); ?>
      <textarea name="rai_textarea_top" rows="4" cols="50" placeholder="Insert your ad code here..."></textarea> <?php
    }
    function rai_middle_ad_code_input() {
      $options = get_option('rai_options'); ?>
      <textarea name="rai_textarea_middle" rows="4" cols="50" placeholder="Insert your ad code here..."></textarea> <?php
    }
    function rai_bottom_ad_code_input() {
      $options = get_option('rai_options'); ?>
      <textarea name="rai_textarea_bottom" rows="4" cols="50" placeholder="Insert your ad code here..."></textarea> <?php
    }
    function rai_section_text() {
      echo '';
    }

    //inserting adsense code to the posts and pages
    function insert_responsive_ad_code($content) {
      $options = get_option('rai_options');
      if (!get_option('rai_options')) {
        return $content;
      }

      //initializing the positions and page types to be false
      $top = false;
      $middle = false;
      $bottom = false;

      if (in_array("top", $options['rai_position'])) {
        $top = true;
        $top_ad_content = $options['rai_textarea_top'];
      }
      if (in_array("middle", $options['rai_position'])) {
        $middle = true;
        $middle_ad_content = $options['rai_textarea_middle'];
      }
      if (in_array("bottom", $options['rai_position'])) {
        $bottom = true;
        $bottom_ad_content = $options['rai_textarea_bottom'];
      }

      //manipulating the post and inserting the ads!
      if($middle == true) {
        $count_words = strlen($content)/2;
        $insert_ad = strpos($content, '. ', $count_words);
        $ad_content = '<br />'.$middle_ad_content.'<br />';
        $content = substr_replace($content, $ad_content, $insert_ad+2, 0);
      }

      if($top == true) {
        $content = $top_ad_content.'<br />'.$content;
      }

      if($bottom == true) {
        $content = $content.'<br />'.$bottom_ad_content;
      }
      return $content;
    }
    add_filter('the_content', 'insert_responsive_ad_code');

    //adding bootstrap for making the whole thing look good
    function load_boostrap() {
      wp_register_script('rai_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js');
      wp_enqueue_script('rai_bootstrap');
      wp_register_style('rai_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
      wp_enqueue_style('rai_bootstrap');
    }
?>
