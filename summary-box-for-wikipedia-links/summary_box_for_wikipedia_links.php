<?php
/**
 * Plugin Name: Summary box for Wikipedia links
 * Plugin URI: https://su-pa.net/wikiPrevBox/
 * Description: Provides a preview for Wikipedia links with nice summary boxes when a reader hovers over or taps a word that is linked to a Wikipedia article.
 * Version: 1.0.4
 * Author: suter & partner, Dominik Fehr, wikinick@su-pa.net
 * Author URI: https://su-pa.net/
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html 
 * Text Domain: summary-box-for-wikipedia-links
 */

if (!defined('ABSPATH')) { exit; } //exit if accessed directly
require_once plugin_dir_path(__FILE__) . 'summary_box_for_wikipedia_links_admin.php';

function supa_enqueue_WikiSummaryPreview() {
    $data = get_plugin_data( __FILE__ ); //header data
    wp_enqueue_style('wiki-preview-box', esc_url(plugins_url('/summary-box-for-wikipedia-links/wikiPreviewBox.min.css')), [], $data['Version']);
    wp_enqueue_script('wiki-preview-box-js', esc_url(plugins_url('/summary-box-for-wikipedia-links/wikiPreviewBox.min.js')), [], $data['Version'], true);
}
add_action('wp_enqueue_scripts', 'supa_enqueue_WikiSummaryPreview');

function supa_add_data_attribute($tag, $handle) {
    if ('wiki-preview-box-js' !== $handle) {return $tag;}

    $options = get_option('sbfwl_settings');

    // validate, sanitize, escape parameter values
    // possible params, affecting all boxes (inside <script ...> tag): 
    // noimages, openlinkinsamewindow, nowikilinknote, numberofchars, fontsize, width 
    $noimages = isset($options['noimages']) && $options['noimages'] == '1' ? 'noimages'.',' : ''; 
    $openlinkinsamewindow = isset($options['openlinkinsamewindow']) && $options['openlinkinsamewindow'] == '1'? 'openlinkinsamewindow'.',' : '';
    $nowikilinknote = isset($options['nowikilinknote']) && $options['nowikilinknote'] == '1' ? 'nowikilinknote,' : '';
    
    $numberofchars_value = isset($options['numberofchars']) && is_numeric($options['numberofchars']) ? absint($options['numberofchars']) : ''; //numberofchars
    $numberofchars = "numberofchars=" . esc_attr($numberofchars_value) . ',';    
    
    $fontsize_value = isset($options['fontsize']) && is_numeric($options['fontsize']) ? floatval($options['fontsize']) : ''; //fontsize
    $fontsize = "fontsize=" . esc_attr($fontsize_value) . ',';
    
    $width_value = isset($options['width']) && is_numeric($options['width']) ? absint($options['width']) : ''; //width
    $width = "width=" . esc_attr($width_value);
    
    $data_value = $noimages . $openlinkinsamewindow . $nowikilinknote . $numberofchars . $fontsize . $width;
    return str_replace(' src', ' data-wikipreview="' . esc_attr($data_value) . '" src', $tag);
}
add_filter('script_loader_tag', 'supa_add_data_attribute', 10, 2);