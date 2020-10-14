<?php
/*
* Plugin Name: San Cricket
* Plugin URI: https://www.allwebtuts.com/stylish-internal-links-wordpress-plugin/
* Description: Display Live Cricket Score on your Wordpress site.
* Version: 1.0
* Author: Santhosh veer
* Author URI: https://santhoshveer.com
* License: GPLv3
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

add_action('wp_head','ssmsklnk_css');
function ssmsklnk_css() {

$output="<style>
.table-score {
  display: inline-block;
  overflow-x: auto;
  margin: 0.5em 0 2.5em;
  max-width: 100%;
  width: auto;
  border-spacing: 0;
  border-collapse: collapse;
  font-size: 14px;
  white-space: nowrap;
  vertical-align: top;
}
.table-score th {
  color:  #15171A;
  font-size: 14px;
  font-weight: 500;
  letter-spacing: 0.2px;
  text-align: left;
  text-transform: uppercase;
  background-color: #e5eff5;
}
.table-score th,
.table-score td {
  padding: 8px 18px;
  border: #e5eff5 1px solid;
}
</style>";

echo $output;

}

function admin_init_mmsancricket() {
  register_setting('sancric_mskc_topt', 'sancric_userapi_key');
}

function admin_menu_mmsancricket() {
  add_options_page('San Cricket', 'San Cricket', 'manage_options', 'sancric_mskc_topt', 'options_page_mmsancricket');
}

function options_page_mmsancricket() {
  include( plugin_dir_path( __FILE__ ) .'options.php');
}

function remove_http($url) {
  $disallowed = array('http://', 'https://');
  foreach($disallowed as $d) {
     if(strpos($url, $d) === 0) {
        return str_replace($d, '', $url);
     }
  }
  return $url;
}

function display_apii_response() {
  $base_url = "https://cri.sanweb.info/cri/";
  $api_path = get_option('sancric_userapi_key').'&domain=';
  $site_url = remove_http(get_home_url());
  $url = $base_url.$api_path.$site_url;
  $response = wp_remote_get($url);
  global $body;
  $body = $response['body'];
  $body = stripslashes($body);
}
add_action( 'init', 'display_apii_response' );

function wpb_demo_shortcode(){
    global $body;
    $message = json_decode($body,true);
    $score_title = isset($message["livescore"]["title"]) ? $message["livescore"]["title"] : 'Match data Will Updated Soon';
    $score_Update = isset($message["livescore"]["update"]) ? $message["livescore"]["update"] : '';
    $score_data = isset($message["livescore"]["current"]) ? $message["livescore"]["current"] : '';
    $Run_rate = isset($message["livescore"]["runrate"]) ? $message["livescore"]["runrate"] : '';
    $Batsman = isset($message["livescore"]["batsman"]) ? $message["livescore"]["batsman"] : '';
    $Bowler = isset($message["livescore"]["bowler"]) ? $message["livescore"]["bowler"] : '';
    $scorecard ='
    <div class="table-score">
    <table>
    <tbody>
    <tr>
    <th>Match</th>
    <td>'.$score_title.' </td>
    </tr>
    <tr>
    <th>Status</th>
    <td>'.$score_Update.'</td>
    </tr>
    <tr>
    <th>Live</th>
    <td>'.$score_data.'</td>
    </tr>
    <tr>
    <th>Run rate</th>
    <td>'.$Run_rate.'</td>
    </tr>
    <tr>
    <th>Current Batsman</th>
    <td>'.$Batsman.'</td>
    </tr>
    <tr>
    <th>Current Bowler</th>
    <td>'.$Bowler.'</td>
    </tr>
    </tbody>
    </table>
    </div>';
    return $scorecard;
  }
  add_shortcode('sancri', 'wpb_demo_shortcode');

if (is_admin()) {
  add_action('admin_init', 'admin_init_mmsancricket');
  add_action('admin_menu', 'admin_menu_mmsancricket');

}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'scrikt_optge_links' );

function scrikt_optge_links ( $links ) {
 $mylinks = array(
 '<a href="' . admin_url( 'options-general.php?page=sancric_mskc_topt' ) . '">Plugin Settings</a>',
 );
return array_merge( $links, $mylinks );
}
