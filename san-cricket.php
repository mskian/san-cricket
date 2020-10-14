<?php
/*
* Plugin Name: San Cricket
* Plugin URI: https://santhoshveer.com/
* Description: Display Live Cricket Score on your Wordpress site.
* Version: 1.0
* Author: Santhosh veer
* Author URI: https://santhoshveer.com/
* License: GPLv3
* License URI: http://www.gnu.org/licenses/gpl.html
*/

## CSS for Scorecard
add_action('wp_head','sanckt_css');
function sanckt_css() {
$output="<style>
.table-score {
  border-collapse: collapse;
  margin: 0.5em 0 2.5em;
  width: 100%;
  table-layout: fixed;
}
.table-score tr {
  border: 1px solid #ddd;
  padding: 6px 12px;
  color: #2d3436;
}
.table-score th,
.table-score td {
  font-size: 13px;
}
.table-score th {
  font-size: 14px;
  padding: 6px 12px;
}
@media screen and (max-width: 600px) {
  .table-score {
    border: 0;
  }
  .table-score caption {
    font-size: 14px;
  }
  .table-score thead {
    border: none;
    clip: rect(0 0 0 0);
    height: 1px;
    margin: -1px;
    overflow: hidden;
    padding: 0;
    position: absolute;
    width: 1px;
  }
  .table-score tr {
    border-bottom: 3px solid #ddd;
    display: block;
    margin-bottom: .625em;
  }
  .table-score td {
    border-bottom: 1px solid #ddd;
    display: block;
    font-size: 14px;
    text-align: right;
  }
  .table-score td::before {
    /*
    * aria-label has no advantage, it won't be read inside a table
    content: attr(aria-label);
    */
    content: attr(data-label);
    float: left;
    font-weight: bold;
    text-transform: uppercase;
  }
  .table-score td:last-child {
    border-bottom: 0;
}
</style>";
echo $output;
}

## Regsister user input
function admin_init_mmsancricket() {
  register_setting('sancric_mskc_topt', 'sancric_userapi_key');
}

## Setup Admin Page
function admin_menu_mmsancricket() {
  add_options_page('San Cricket', 'San Cricket', 'manage_options', 'sancric_mskc_topt', 'options_page_mmsancricket');
}

## Options Page
function options_page_mmsancricket() {
  include( plugin_dir_path( __FILE__ ) .'options.php');
}


## Remove HTTP and HTTPS from URL
function remove_http($url) {
  $disallowed = array('http://', 'https://');
  foreach($disallowed as $d) {
     if(strpos($url, $d) === 0) {
        return str_replace($d, '', $url);
     }
  }
  return $url;
}

## API Auth and Get data
function display_apii_response() {
  $base_url = "https://cri.sanweb.info/cri/";
  $api_path = get_option('sancric_userapi_key').'&domain=';
  $site_url = remove_http(get_bloginfo('url'));
  $url = $base_url.$api_path.$site_url;
  $response = wp_remote_get($url);
  global $body;
  $body = $response['body'];
  $body = stripslashes($body);
}
add_action( 'init', 'display_apii_response' );

## Shortcode to Display Score
function wpb_sancric_shortcode(){
    global $body;
    $message = json_decode($body,true);
    $score_title = isset($message["livescore"]["title"]) ? $message["livescore"]["title"] : 'Match data Will Updated Soon';
    $score_Update = isset($message["livescore"]["update"]) ? $message["livescore"]["update"] : '';
    $score_data = isset($message["livescore"]["current"]) ? $message["livescore"]["current"] : '';
    $Run_rate = isset($message["livescore"]["runrate"]) ? $message["livescore"]["runrate"] : '';
    $Batsman = isset($message["livescore"]["batsman"]) ? $message["livescore"]["batsman"] : '';
    $Batsman_run = isset($message["livescore"]["batsmanrun"]) ? $message["livescore"]["batsmanrun"] : '';
    $Bowler = isset($message["livescore"]["bowler"]) ? $message["livescore"]["bowler"] : '';
    $Batsman_over = isset($message["livescore"]["bowlerover"]) ? $message["livescore"]["bowlerover"] : '';
    $scorecard ='
    <div class="table-score">
    <table>
    <tbody>
    <tr>
    <th>Match 🏏</th>
    <td>'.$score_title.' </td>
    </tr>
    <tr>
    <th>Status 📊</th>
    <td>'.$score_Update.'</td>
    </tr>
    <tr>
    <th>Live 🔵</th>
    <td>'.$score_data.'</td>
    </tr>
    <tr>
    <th>Run rate 📉</th>
    <td>'.$Run_rate.'</td>
    </tr>
    <tr>
    <th>Current ✊ Batsman</th>
    <td>'.$Batsman.' ('.$Batsman_run.')</td>
    </tr>
    <tr>
    <th>Current ✊ Bowler</th>
    <td>'.$Bowler.' - Over '.$Batsman_over.'</td>
    </tr>
    </tbody>
    </table>
    </div>';
    return $scorecard;
  }
  add_shortcode('sancri', 'wpb_sancric_shortcode');


## init Admin
if (is_admin()) {
  add_action('admin_init', 'admin_init_mmsancricket');
  add_action('admin_menu', 'admin_menu_mmsancricket');

}

## Settings Page link
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'scrikt_optge_links' );

function scrikt_optge_links ( $sanclinks ) {
 $mysanclinks = array(
 '<a href="' . admin_url( 'options-general.php?page=sancric_mskc_topt' ) . '">Plugin Settings</a>',
 );
return array_merge( $sanclinks, $mysanclinks );
}
