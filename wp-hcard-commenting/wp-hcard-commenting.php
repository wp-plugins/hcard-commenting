<?php
/*
Plugin Name: WP-hCard-Commenting
Plugin URI: http://notizblog.org/projects/wp-hcard-commenting/
Description: This Plugin allows your users to easily fill out 
your comment forms using a hCard, it shoul work for the most themes,
if not, you simply have to add &lt;?php ?&gt; to your theme where
you want the link to be displayed.
Author: Matthias Pfefferle
Author URI: http://notizblog.org
Version: 0.5
*/

require_once('lib/hkit.class.php');

function hcard_enabled_link() {
	echo '<a id="hcard_enabled_link" href="http://microformats.org/wiki/hCard">(hCard Enabled)</a>' .
       '<span id="ajax-loader" style="display: none;">Loading hCard</span>';
}

if  ( !class_exists('hCardId') ) {
  class hCardId {
	  var $path;

	  function hCardId() { }

    function parse_hcard($url) {
      if (phpversion() > 5) {
        $hkit = new hKit();
        $hcard = $hkit->getByURL('hcard', $url);
      } else {
        $hcard = file_get_contents('http://tools.microformatic.com/query/php/hkit/' . urldecode($url));
        $hcard = unserialize ($hcard);
      }
      return $this->create_json($hcard);
    }

    function create_json($hcard) {
      $hcard = $hcard[0];
      if ($hcard) {
        $jcard =  '{"vcard": {';
        $jcard .= '"fn": "'.$hcard["fn"].'", ';
        $jcard .= '"email": "'.$hcard["email"].'", ';
        $jcard .= '"url": "'.$hcard["url"].'"}}';
      } else {
        $jcard = null;
      }
      return $jcard;
    }

    function register_js() {
      wp_enqueue_script( 'jquery' );
      wp_enqueue_script( 'openid' );
      wp_enqueue_script( 'hcard-commenting', self::get_path() . '/js/hcard-commenting.js.php', array('jquery') );
    }

    function start() {
      add_action( 'init', array($this, 'register_js') );
      add_action( 'wp_head', array( $this, 'style'));
    }

    /**
     * Set the path for the plugin.
     **/
    static function get_path() {
      $plugin = 'wp-hcard-commenting';

      $base = plugin_basename(__FILE__);
      if ($base != __FILE__) {
        $plugin = dirname($base);
      }

      $path = '/wp-content/plugins/'.$plugin;

      return get_option('siteurl').$path;
    }

    /**
     * Include internal stylesheet.
     *
     * @action: wp_head, login_head
     **/
    function style() {
      $css_path = self::get_path() . '/css/hcard-commenting.css';
      echo '
        <link rel="stylesheet" type="text/css" href="'.$css_path.'" />';
    }
  }
}

$hid = new hCardId();

if ($_GET['url']) {
  $hcard = $hid->parse_hcard($_GET['url']);

  //header('Content-Type: application/json; charset=utf-8');
  header('Content-Type: application/x-javascript; charset=utf-8');
  echo $hcard;
}

if (isset($wp_version)) {
  $hid->start();
}
?>
