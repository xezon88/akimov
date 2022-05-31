<?
 /**
  * Plugin Name: cdr bot connector
  * Version: 1.0
  */

  defined('ABSPATH') or die();

  class cdrBotConnector {
    public function cdrBotConnector () {
      add_action( 'rest_api_init', [$this, 'initSaveCardAPI']);
    }

    public function initSaveCardAPI () {
      $namespace = 'cdr-connector/v1';
      $route = '/save-card/';

      $params = [
        'methods' => 'POST',
        'callback' => [$this, 'saveCard'],
        'args' => [],
        'permission_callback' => true,
      ];

      register_rest_route($namespace, $route, $params);
    }

    public function saveCard ($request) {
      $logPath = plugins_url('./restLog.txt', __FILE__);
      $result = file_put_contents($logPath, $request, FILE_APPEND);

      print($result);
    }
  }

  $botConnector = new cdrBotConnector;
?>