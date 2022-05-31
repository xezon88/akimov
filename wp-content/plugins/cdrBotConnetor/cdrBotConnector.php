<?
 /**
  * Plugin Name: cdr bot connector
  * Version: 1.0
  */

  defined('ABSPATH') or die();

  class cdrBotConnector {
    public function __construct () {
      add_action('rest_api_init', [$this, 'initSaveCardAPI']);
    }

    public function initSaveCardAPI () {
      $namespace = 'cdr-connector/v1';
      $route = '/save-card/';

      $params = [
        'methods' => 'POST',
        'callback' => [$this, 'saveCard'],
        'args' => [],
        'permission_callback' => function () {
          return true;
        },
      ];

      register_rest_route($namespace, $route, $params);
    }

    public function saveCard ($request) {
      $reqArr = $request -> get_params();

      $id = '1Q08p_WncgTX4Ep8ik1Lga59PsD3uOt4-XYf7eGwxvhM';
      $gid = '539937704';
      $csv = file_get_contents('https://docs.google.com/spreadsheets/d/' . $id . '/export?format=csv&gid=' . $gid);
      // $csv = explode('\r\n', $csv);
      // $csv = explode(',', $csv[0]);

      print('<pre>');
      print_r($csv);
      print('</pre>');
    }
  }

  $botConnector = new cdrBotConnector;
?>