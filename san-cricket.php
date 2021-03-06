<?php
/*
* Plugin Name: San Cricket
* Plugin URI: https://github.com/mskian/san-cricket
* Description: Display Live Cricket Score on your Wordpress site.
* Version: 1.2
* Author: Santhosh Veer
* Author URI: https://sanweb.info/
* License: GPLv3
* License URI: http://www.gnu.org/licenses/gpl.html
*/

## CSS for Scorecard
add_action('wp_head','sanckt_css');
function sanckt_css() {
$output="<style>
.table-score{border-collapse:collapse;margin:.5em 0 2.5em;width:100%;table-layout:fixed}.table-score tr{border:1px solid #ddd;padding:6px 12px;color:#2d3436}.table-score th,.table-score td{font-size:13px}.table-score th{font-size:14px;padding:6px 12px}@media screen and (max-width: 600px){.table-score{border:0}.table-score caption{font-size:14px}.table-score thead{border:none;clip:rect(0 0 0 0);height:1px;margin:-1px;overflow:hidden;padding:0;position:absolute;width:1px}.table-score tr{border-bottom:3px solid #ddd;display:block;margin-bottom:.625em}.table-score td{border-bottom:1px solid #ddd;display:block;font-size:14px;text-align:right}.table-score td::before{content:attr(data-label);float:left;font-weight:700;text-transform:uppercase}.table-score td:last-child{border-bottom:0}}
</style>";
echo $output;
}

## Regsister user input
function admin_init_mmsancricket() {
  register_setting('sancric_mskc_topt', 'sancric_livecric_url');
}

## Setup Admin Page
function admin_menu_mmsancricket() {
  add_options_page('San Cricket', 'San Cricket', 'manage_options', 'sancric_mskc_topt', 'options_page_mmsancricket');
}

## Options Page
function options_page_mmsancricket() {
  include( plugin_dir_path( __FILE__ ) .'options.php');
}

## Get data
function display_apii_response() {
  $base_url = "https://cricket-api.vercel.app/cri.php?url=";
  $api_path = get_option('sancric_livecric_url');
  $url = $base_url.$api_path;
  $response = wp_remote_get($url);
  if ( is_wp_error( $response ) ) {
    return 'bad connection';
  }
  $json = $response['body'];
  global $body;
  $body = json_decode( $json, true );
}
add_action( 'init', 'display_apii_response' );

## Shortcode to Display Score
function wpb_sancric_shortcode(){

    ## Check Empty Input
    if (empty(get_option('sancric_livecric_url'))) {
    return '<p>Match URL Not Found</p>';
    }

    global $body;
    $message = $body;
    $score_title = isset($message["livescore"]["title"]) ? $message["livescore"]["title"] : 'Match Data Will be Updated Soon';
    $score_Update = isset($message["livescore"]["update"]) ? $message["livescore"]["update"] : 'Data Will be Updated Soon';
    $score_data = isset($message["livescore"]["current"]) ? $message["livescore"]["current"] : 'Data Will be Updated Soon';
    $Run_rate = isset($message["livescore"]["runrate"]) ? $message["livescore"]["runrate"] : 'Data Will be Updated Soon';
    $Batsman = isset($message["livescore"]["batsman"]) ? $message["livescore"]["batsman"] : 'Data Will be Updated Soon';
    $Batsman_run = isset($message["livescore"]["batsmanrun"]) ? $message["livescore"]["batsmanrun"] : 'Data Will be Updated Soon';
    $Bowler = isset($message["livescore"]["bowler"]) ? $message["livescore"]["bowler"] : 'Data Will be Updated Soon';
    $Batsman_over = isset($message["livescore"]["bowlerover"]) ? $message["livescore"]["bowlerover"] : 'Data Will be Updated Soon';
    $scorecard ='
    <div class="table-score">
    <table>
    <tbody>
    <tr>
    <th>🏏 Match</th>
    <td>'.$score_title.' </td>
    </tr>
    <tr>
    <th>📊 Status</th>
    <td>'.$score_Update.'</td>
    </tr>
    <tr>
    <th>🔴 Live</th>
    <td>'.$score_data.'</td>
    </tr>
    <tr>
    <th>📉 Run rate</th>
    <td>'.$Run_rate.'</td>
    </tr>
    <tr>
    <th>Current ✊ Batsman</th>
    <td>'.$Batsman.' ('.$Batsman_run.' Runs)</td>
    </tr>
    <tr>
    <th>Current ✊ Bowler</th>
    <td>'.$Bowler.' - Overs '.$Batsman_over.'</td>
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
