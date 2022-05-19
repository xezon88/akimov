<?php 
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       @xezon
 * @since      1.0.0
 *
 * @package    Akimov
 * @subpackage Akimov/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Akimov
 * @subpackage Akimov/includes
 * @author     xezon <@xezon>
 */
defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

class Akimov_Shortcode {

     /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Akimov_RealEstate    $real_estate    Maintains and registers all hooks for the plugin.
     */
    protected $real_estate;

  

    public function __construct()
    {

        add_action('init', array($this,'register_real_estate_shortcodes'));
        add_action('init', array($this,'load_dependencies'));
        
    }
    public function load_dependencies() {
       
    }


    public function register_real_estate_shortcodes(){
        add_shortcode('real_estate_short', array($this,'real_estate_shortcode_output'));
    }
    public function real_estate_shortcode_output($atts, $content = '', $tag){

       // require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-akimov-real-estate.php';
        $real_estate = new Akimov_RealEstate;
       // var_dump($real_estate);
        //get the global wp_simple_locations class
        
    
        //build default arguments
        $arguments = shortcode_atts(array(
            'real_estate_id' => '',
            'number_of_real_estates' => -1)
        ,$atts,$tag);
    
        //uses the main output function of the location class
        $html = $real_estate->get_real_estates_output($arguments);
       
       return $html;

       //$real_estate_short = new Akimov_Shortcode;
    }
  
}