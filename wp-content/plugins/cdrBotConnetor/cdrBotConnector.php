<?php
  /**
  * Plugin Name: cdrBotConnector
  * Version: 1.0
  */

  defined('ABSPATH') or die();

  class cdrBotConnector {
    private $base;

    public function __construct () {
      // add_action('wp_enqueue_scripts', [$this, 'loadPublicScripts']);
      // add_action('admin_enqueue_scripts', [$this, 'loadAdminScripts']);
      // add_action('admin_menu', [$this, 'addMenu']);
      // add_action('wp_ajax_saveCard', [$this, 'saveCard']);
      // add_shortcode( 'mathCalc', [$this, 'loadShortCodeTemplate']);
      
      $this -> base = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
      add_action('rest_api_init', [$this, 'initSaveCardAPI']);
    }

    public function loadPublicScripts () {
      wp_register_style('botConnCSS', plugins_url('/templates/styles.css', __FILE__));
      wp_register_script('botConnJS', plugins_url('/templates/scripts.js', __FILE__));
      
      wp_enqueue_style('botConnCSS');
      wp_enqueue_script('botConnJS');
    }

    public function loadAdminScripts () {
      wp_register_script('botConnAdminJS', plugins_url('./admin/scripts.js', __FILE__));
      wp_register_style('botConnAdminCSS', plugins_url('./admin/styles.css', __FILE__));

      wp_enqueue_script('botConnAdminJS');
      wp_enqueue_style('botConnAdminCSS');
    }

    public function addMenu () {
      add_menu_page('botConnector', 'botConnector', 'read', 'botConnector', [$this, 'loadAdminPage']);
    }


    public function loadShortCodeTemplate () {
      $shortCode = file_get_contents( plugins_url('./templates/calc.php', __FILE__) );
      return $shortCode;
    }

    public function getRequestHistory() {
      $query = "SELECT * FROM `wp_cdr_temp`";
      $res = $this -> base -> get_results($query);
      return $res;
    }


    public function saveRequest($req)
    {
      $reqParams = $req -> get_params();
      $reqJSON = json_encode($reqParams);
      $query = "INSERT INTO `wp_cdr_temp` VALUES ( '', '$reqJSON')";
      $res = $this -> base -> get_results($query);
      return $res;
    }


    public function initSaveCardAPI () {
      $namespace = 'cdr-connector/v1';
      $route = '/save-card/';

      $paramsArr = [
        'methods' => 'POST',
        'callback' => [$this, 'saveCard'],
        'args' => [],
        'permission_callback' => function () {return true;},
      ];

      register_rest_route($namespace, $route, $paramsArr);
    }


    public function saveCard ($request) {
      $tableURL = 'https://docs.google.com/spreadsheets/d';
      $id = '1Q08p_WncgTX4Ep8ik1Lga59PsD3uOt4-XYf7eGwxvhM';
      $gid = '539937704';
      $urlParams = "export?format=csv&gid=$gid";

      $csv = file_get_contents("$tableURL/$id/$urlParams");
      $csv = explode("\n", $csv);

      $cardsArr = [];
      foreach ($csv as $line) {
        $cardsArr[] = $this -> parseCard($line);
      }

      // $post_id = wp_insert_post($cardsArr[0]);
      $saveRes = $this -> saveRequest($request);
      $reqHistory = $this -> getRequestHistory();

      print('<pre>');
      print_r($reqHistory);
      print_r('</pre>');
    }

    public function loadAdminPage () {
      $adminTemplate = file_get_contents( plugins_url('./admin/index.php', __FILE__) );
      print($adminTemplate);
    }

    public function parseCard($cardString) {
      $card = explode(',', $cardString);

      $resultArr = [];
      $resultArr['public_date']      = $card[0];
      $resultArr['id']               = $card[1];
      $resultArr['type']             = $card[2];
      $resultArr['role']             = $card[3];
      $resultArr['newType']          = $card[5];
      $resultArr['otdelka']          = $card[6];
      $resultArr['adress']           = $card[19];
      $resultArr['desc']             = str_replace('"', '', $card[22]);
      $resultArr['interrior_status'] = str_replace('"', '', $card[23]);
      $resultArr['youtube']          = str_replace('"', '', $card[25]);
      $resultArr['anent']            = $card[26];
      $resultArr['kontakts']         = $card[27];

      $resultArr['coords'] = [
        'x' => str_replace('"', '', $card[20]),
        'y' => str_replace('"', '', $card[21]),
      ];

      $resultArr['post_title'] = $resultArr['adress'] ." " .$resultArr['newType'];
      $resultArr['post_content'] = '';
      $resultArr['post_aouthor'] = 1;
      $resultArr['post_status'] = 'publish';

      return $resultArr;
    }
  }
?>
<? $botConnector = new cdrBotConnector; ?>