<?php
/*
Plugin Name: WP-hCard-Commenting
Plugin URI: http://notizblog.org/projects/wp-hcard-commenting/
Description: This Plugin allows your users to easily fill out your comment forms using an hCard, it should work for the most themes without any changes, if not, simply add &lt;?php hcard_commenting_link() ?&gt; to your theme where you want the link to be displayed.
Author: Matthias Pfefferle
Author URI: http://notizblog.org
Version: 0.6
*/

if (!class_exists('hKit')) {
  include_once('lib/hkit.class.php');
}

function hcard_commenting_link() {
	echo '<a id="hcard_enabled_link" href="http://microformats.org/wiki/hCard">(hCard Enabled)</a>' .
       '<span id="ajax-loader" style="display: none;">Loading hCard</span>';
}

if (isset($wp_version)) {
  add_filter('query_vars', array('hCardId', 'query_vars'));
  add_action('parse_query', array('hCardId', 'parse_hcard'));
  add_action('init', array('hCardId', 'init'));
  add_filter('generate_rewrite_rules', array('hCardId', 'rewrite_rules'));
  
  add_action('wp_head', array('hCardId', 'style'), 5);
}

class hCardId {

  function hCardId() { }
  
  function init() {
    global $wp_rewrite;
    $wp_rewrite->flush_rules();

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'hcard-commenting', hCardId::get_path() . '/js/hcard-commenting.js.php', array('jquery') );
  }
  
  /**
   * Define the rewrite rules
   */
  function rewrite_rules($wp_rewrite) {
    $new_rules = array(
      'hcard_url/(.+)' => 'index.php?hcard_url=' . $wp_rewrite->preg_index(1)
    );
    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
  }
  
  function parse_hcard() {
  	global $wp_query, $wp_version;

  	$url = $wp_query->query_vars['hcard_url'];

    $status = '200';
    $ct = 'text/plain';
    
    if( isset( $url )) {
      if (phpversion() > 5) {
        $hkit = new hKit();
        $result = $hkit->getByURL('hcard', $url);
      } else {
        $hcard = file_get_contents('http://tools.microformatic.com/query/php/hkit/' . urldecode($url));
        $result = unserialize ($hcard);
      }

      $repcard = null;

      if (count($result) != 0) {
        if (count($result) == 1) {
          $repcard = $result[0];
        } else {
          foreach ($result as $card) {
            if (array_search($url, $card) == true || @$card['uid'] == $url) {
              $repcard = $card;
            }
          }
        }

        if (!$repcard) {
          $repcard = $result[0];
        }

        $o = hCardId::create_json($repcard);
        $ct = 'application/json';
      } else {
        $o = '404 Not Found';
        $status = '404';
      }
      
      $header = 'Content-type: '.$ct;

      switch($status) {
        case '400':
          $header = "HTTP/1.0 400 Bad Request";
          break;
        case '404':
          $header = "HTTP/1.0 404 Not Found";
          break;
        case '200':
          default:
          $header = "HTTP/1.0 200 OK";
          break;
      }
    
      header($header);
      echo $o;
      exit;
    }
  }

  function create_json($hcard) {
    // if there is more than one url
    $hcard["url"] = hCardId::get_url($hcard["url"]);
    // if there is more than one email address, take the first
    $hcard["email"] = is_array($hcard["email"]) ? $hcard["email"][0] : $hcard["email"];
      
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
    
  function get_url($url) {
    if (is_array($url)) {
      /*foreach ($url as $u) {
        echo $u;
        if (preg_match_all("((http://|https://)[^ ]+)", $u, $match)) {
          return $u;
        }
      }*/
      return $url[0];
    } else {
      return $url;
    }
  }

  /**
   * Set the path for the plugin.
   **/
  function get_path() {
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
    $css_path = hCardId::get_path() . '/css/hcard-commenting.css';
    echo '<link rel="stylesheet" type="text/css" href="'.$css_path.'" />';
  }
    
  /**
   * Add 'hcard_url' as a valid query variables.
   */
  function query_vars($vars) {
    $vars[] = 'hcard_url';
  
    return $vars;
  }
}
?>
