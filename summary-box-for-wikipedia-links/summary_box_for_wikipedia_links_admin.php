<?php
if (!defined('ABSPATH')) { exit; } // exit if accessed directly

// summary_box_for_wikipedia_links_admin.php
class summary_box_for_wikipedia_links_admin {
    public static function sbfwl_init() {
        add_action('admin_menu', array('summary_box_for_wikipedia_links_admin', 'sbfwl_add_admin_menu'));
        add_action('admin_init', array('summary_box_for_wikipedia_links_admin', 'sbfwl_settings_init'));
    }

    public static function sbfwl_settings_page() {
        ?>
        <div class="wrap">
            <h1>Summary box for Wikipedia links</h1>
            <form method="post" action="options.php">
                <?php settings_fields('sbfwl_settings_group'); ?>
                <?php do_settings_sections('sbfwl_settings_page'); ?>
                <?php submit_button(); ?>
            </form>
            
            <h2>Settings that affect single summary boxes (advanced)</h2>
            <p>
                <strong>There are some parameters you can add to the end of a specific Wikipedia link.</strong>
            </p>                
            <table class="form-table" role="presentation">
                <tbody>
                <tr>
                    <th scope="row">#nopreview</th>
                    <td><p class="description">Does not display the summary box for a specific link,<br>
                    but resets it to a common link to a Wikipedia article.<br>
                    Example: <code>https://en.wikipedia.org/wiki/Lanyu#nopreview</code>
                </p></td>
                </tr>
                <tr>
                    <th scope="row">#dontshowimage</th>
                    <td>
                        <p class="description">Does not show the image of a specific Wikipedia summary box for a given link.<br>
                        Example: <code>https://en.wikipedia.org/wiki/Wind#Measurement#dontshowimage</code>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">#showimage</th>
                    <td><p class="description"><b>After checking "Disable images" above</b>, no images are displayed.<br>
                    With this parameter you can still display an image in a specific overview field.<br>
                    Example: <code>https://en.wikipedia.org/wiki/Wind#showimage</code>                    
                </p></td>
                </tr>
                <tr>
                    <th scope="row">Link to a subsection</th>
                    <td><p class="description">Link points to a subsection (#measurement) of a Wikipedia article, put the parameter at the end.<br>
                    Example: <code>https://en.wikipedia.org/wiki/Wind#Measurement#dontshowimage</code>                   
                </p></td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php
    }

    //openlinkinsamewindow, numberofchars, fontsize, noimages, width, nowikilinknote
    public static function sbfwl_settings_init() {
        register_setting('sbfwl_settings_group', 'sbfwl_settings', 'supa_sbfwl_sanitize_callback');

        add_settings_section('sbfwl_general_section', 'General settings that affect all summary boxes', array('summary_box_for_wikipedia_links_admin', 'sbfwl_general_section_callback'), 'sbfwl_settings_page');
        add_settings_field('sbfwl_width', 'Summary box Width', array('summary_box_for_wikipedia_links_admin', 'sbfwl_width_callback'), 'sbfwl_settings_page', 'sbfwl_general_section');
        add_settings_field('sbfwl_numberofchars', 'Number of characters', array('summary_box_for_wikipedia_links_admin', 'sbfwl_numberofchars_callback'), 'sbfwl_settings_page', 'sbfwl_general_section');
        add_settings_field('sbfwl_fontsize', 'Font size', array('summary_box_for_wikipedia_links_admin', 'sbfwl_fontsize_callback'), 'sbfwl_settings_page', 'sbfwl_general_section');
        add_settings_field('sbfwl_openlinkinsamewindow', 'Open link in same window', array('summary_box_for_wikipedia_links_admin', 'sbfwl_openlinkinsamewindow_callback'), 'sbfwl_settings_page', 'sbfwl_general_section'); //checkbox
        add_settings_field('sbfwl_nowikilinknote', 'Do not display the text<br>"(wikipedia.org)"', array('summary_box_for_wikipedia_links_admin', 'sbfwl_nowikilinknote_callback'), 'sbfwl_settings_page', 'sbfwl_general_section'); //checkbox
        add_settings_field('sbfwl_noimages', 'Disable images', array('summary_box_for_wikipedia_links_admin', 'sbfwl_noimages_callback'), 'sbfwl_settings_page', 'sbfwl_general_section'); //checkbox
        
        // Add an action to clear cache
        add_action('admin_init', array('summary_box_for_wikipedia_links_admin', 'sbfwl_clear_plugin_cache'));
    }

    //sanitize option values for saving
    function supa_sbfwl_sanitize_callback($input) {
        $new_input = array();
        if (isset($input['width']))
            $new_input['width'] = absint($input['width']);
        if (isset($input['numberofchars']))
            $new_input['numberofchars'] = absint($input['numberofchars']);
        if (isset($input['fontsize']))
            $new_input['fontsize'] = floatval($input['fontsize']);
        if (isset($input['openlinkinsamewindow']))
            $new_input['openlinkinsamewindow'] = absint($input['openlinkinsamewindow']);
        if (isset($input['nowikilinknote']))
            $new_input['nowikilinknote'] = absint($input['nowikilinknote']);
        if (isset($input['noimages']))
            $new_input['noimages'] = absint($input['noimages']);    
        return $new_input;
    }

    // Clear plugin cache
    public static function sbfwl_clear_plugin_cache() {        
        wp_clean_plugins_cache(true); // Clear both plugin and update caches
    }

    public static function sbfwl_general_section_callback() {
        // Section description
        ?>
            <p>
                Often there is no need to change any values here.<br>
                <strong>Note: If you don't see an effect of your change, consider clearing your website (and browser) cache.</strong><br>                
                <br>Dominik, <a href="mailto:wikinick@su-pa.net">wikinick@su-pa.net</a>, 
                <a href="https://su-pa.net/wikiPrevBox/">project page</a> (su-pa.net)
                <br>Hope you enjoy the plugin!
            </p>
        <?php
    }

    // noimages
    public static function sbfwl_noimages_callback() {
        $options = get_option('sbfwl_settings');
        $noimages = isset($options['noimages']) ? $options['noimages'] : '';
        ?>
        <input type="checkbox" name="sbfwl_settings[noimages]" value="1" <?php checked(1, $noimages); ?> />
        <p class="description">
            Disable all images in the Summary boxes for Wikipedia.<br>
            Individual images can be re-displayed by including "#showimage" in the URL.<br>
            Default is <b>unchecked</b>
        </p>
        <?php
    }

    // nowikilinknote ((don't) show "(wikipedia.org)")
    public static function sbfwl_nowikilinknote_callback() {
        $options = get_option('sbfwl_settings');
        $nowikilinknote = isset($options['nowikilinknote']) ? $options['nowikilinknote'] : '';
        ?>
        <input type="checkbox" name="sbfwl_settings[nowikilinknote]" value="1" <?php checked(1, $nowikilinknote); ?> />
        <p class="description">
        Do not display the "(wikipedia.org)" notice next to the Wikipedia link; an arrow appears instead.<br>
            Default is <b>unchecked</b>
        </p>
        <?php
    }

    // width
    public static function sbfwl_width_callback() {
        $options = get_option('sbfwl_settings');
        $width = isset($options['width']) ? $options['width'] : '300';
        ?>
        <input type="number" name="sbfwl_settings[width]" min="200" max="400" value="<?php echo esc_attr($width); ?>" />
        <p class="description">Width of the Summary box for Wikipedia links.<br>
            200 to 400, default is <b>300</b></p>
        <?php
    }

    //numberofchars
    public static function sbfwl_numberofchars_callback() {
        $options = get_option('sbfwl_settings');
        $numberofchars = isset($options['numberofchars']) ? $options['numberofchars'] : '280';
        ?>
        <input type="number" name="sbfwl_settings[numberofchars]" min="100" max="800" value="<?php echo esc_attr($numberofchars); ?>" />
        <p class="description">Maximum number of letters that can appear in the Summary box for Wikipedia.<br>
            The maximum length of the text also depends on the linked Wikipedia article.<br>
            100 to 800, default is <b>280</b>
        </p>
        <?php
    }

    //fontsize
    public static function sbfwl_fontsize_callback() {
        $options = get_option('sbfwl_settings');
        $fontsize = isset($options['fontsize']) ? $options['fontsize'] : '0.9';
        ?>
        <input type="number" step="0.1" name="sbfwl_settings[fontsize]" min="0.3" max="2" value="<?php echo esc_attr($fontsize); ?>" />
        <p class="description">Font size in relation to the font size on the page.<br>
            0.3 to 2, default is <b>0.9</b>
        </p>
        <?php
    }

    //openlinkinsamewindow
    public static function sbfwl_openlinkinsamewindow_callback() {
        $options = get_option('sbfwl_settings');
        $openlinkinsamewindow = isset($options['openlinkinsamewindow']) ? $options['openlinkinsamewindow'] : '';
        ?>
        <input type="checkbox" name="sbfwl_settings[openlinkinsamewindow]" value="1" <?php checked(1, $openlinkinsamewindow); ?> />
        <p class="description">Open links to the Wikipedia articles in the already open window.<br>
            Default is <b>unchecked</b>
        </p>
        <?php
    }

    public static function sbfwl_add_admin_menu() {
        add_options_page(
            'Summary box for Wikipedia settings',
            'Summary for Wikipedia',
            'manage_options',
            'sbfwl_settings',
            array('summary_box_for_wikipedia_links_admin', 'sbfwl_settings_page')
        );
    }
}
summary_box_for_wikipedia_links_admin::sbfwl_init();