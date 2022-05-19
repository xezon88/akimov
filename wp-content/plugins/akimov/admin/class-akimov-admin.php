<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       @xezon
 * @since      1.0.0
 *
 * @package    Akimov
 * @subpackage Akimov/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Akimov
 * @subpackage Akimov/admin
 * @author     xezon <@xezon>
 */
class Akimov_Admin
{
    /**
     * The options name to be used in this plugin
     *
     * @since  	1.0.0
     * @access 	private
     * @var  	string 		$option_name 	Option name of this plugin
     */
    private $option_name = 'akimov';
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;



    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Akimov_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Akimov_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/akimov-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Akimov_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Akimov_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/akimov-admin.js', array('jquery'), $this->version, false);
    }

    public function add_options_page()
    {
        $this->plugin_screen_hook_suffix = add_options_page(
            __('Недвижимость 360 настройки', 'akimov'),
            __('Недвижимость 360', 'akimov'),
            'manage_options',
            $this->plugin_name,
            array($this, 'display_options_page')
        );
    }

    public function display_options_page()
    {
        include_once 'partials/akimov-admin-display.php';
    }


    // public function add_plugin_admin_menu()
    // {

    // 	/*
    // 	 * Add a settings page for this plugin to the Settings menu.
    // 	*/
    // 	add_options_page(
    // 		__('Недвижимость 360 настройки', 'akimov'),
    // 		__('Недвижимость 360', 'akimov'),
    // 		'manage_options',
    // 		$this->plugin_name,
    // 		array($this, 'display_plugin_setup_page')
    // 	);
    // }


    // public function add_action_links($links)
    // {

    // 	$settings_link = array(
    // 		'<a href="' . admin_url('options-general.php?page=' . $this->plugin_name) . '">' . __('Settings', $this->plugin_name) . '</a>',
    // 	);
    // 	return array_merge($settings_link, $links);
    // }

    // /**
    //  * Render the settings page for this plugin.
    //  */

    // public function display_plugin_setup_page()
    // {

    // 	include_once('partials/akimov-admin-display.php');
    // }

    public function register_setting()
    {
        add_settings_section(
            $this->option_name . '_general',
            __('Настройки', 'akimov'),
            array($this, $this->option_name . '_general_cb'),
            $this->plugin_name
        );
        // add_settings_field(
        // 	$this->option_name . '_position',
        // 	__('Text position', 'akimov'),
        // 	array($this, $this->option_name . '_position_cb'),
        // 	$this->plugin_name,
        // 	$this->option_name . '_general',
        // 	array('label_for' => $this->option_name . '_position')
        // );
        add_settings_field(
            $this->option_name . '_day',
            __('Идентификатор таблицы гугл', 'akimov'),
            array($this, $this->option_name . '_day_cb'),
            $this->plugin_name,
            $this->option_name . '_general',
            array('label_for' => $this->option_name . '_day')
        );

        add_settings_field(
            $this->option_name . '_api_key',
            __('API карт гугл', 'akimov'),
            array($this, $this->option_name . '_api_key_cb'),
            $this->plugin_name,
            $this->option_name . '_general',
            array('label_for' => $this->option_name . '_api_key')
        );

        //register_setting($this->plugin_name, $this->option_name . '_position', array($this, $this->option_name . '_sanitize_position'));
        register_setting($this->plugin_name, $this->option_name . '_day', 'val');
        register_setting($this->plugin_name, $this->option_name . '_api_key', 'val');
    }


    public function akimov_general_cb()
    {
        echo '<p>' . __('Настройки для работы с плагином.', 'akimov') . '</p>';
    }
    /*
    public function akimov_position_cb() {
         $position = get_option($this->option_name . '_position');

?>
         <fieldset>
            <label>
                <input type="radio" name="<?php echo $this->option_name . '_position' ?>" id="<?php echo $this->option_name . '_position' ?>" value="before" <?php checked($position, 'before'); ?>>
                <?php _e('Before the content', 'akimov'); ?>
            </label>
            <br>
            <label>
                <input type="radio" name="<?php echo $this->option_name . '_position' ?>" value="after" <?php checked($position, 'after'); ?>>
                <?php _e('After the content', 'akimov'); ?>
            </label>
        </fieldset>
<?php

    }
    */
    /**
     * Render the treshold day input for this plugin
     *
     * @since  1.0.0
     */
    public function akimov_day_cb()
    {
        $day = get_option($this->option_name . '_day');
        echo '<input type="text" name="' . $this->option_name . '_day' . '" id="' . $this->option_name . '_day' . '" value="' . $day .  '"> ';
    }

    public function akimov_api_key_cb()
    {
        $api_key = get_option($this->option_name . '_api_key');
        echo '<input type="text" name="' . $this->option_name . '_api_key' . '" id="' . $this->option_name . '_api_key' . '" value="' . $api_key .  '"> ';
    }



    // /**
    //  * Sanitize the text position value before being saved to database
    //  *
    //  * @param  string $position $_POST value
    //  * @since  1.0.0
    //  * @return string           Sanitized value
    //  */
    // public function akimov_sanitize_position($position)
    // {
    // 	if (in_array($position, array('before', 'after'), true)) {
    // 		return $position;
    // 	}
    // }
}
