<?php
/*
Plugin Name: RankingBadge
Plugin URI: http://www.grobekelle.de/wordpress-plugins/
Description: RankingBadge displays ranking information from major sources such as Google (PageRank), Alexa (Alexa traffic Rank) and Technorati in the sidebar of your blog. Check out more <a href="http://www.grobekelle.de/wordpress-plugins/">Wordpress Plugins</a> by Grobekelle.
Version: 0.5
Author: grobekelle
Author URI: http://www.grobekelle.de
*/

/**
 * v0.5 18.07.2010 small xhtml changes
 * v0.4 21.07.2009 small image position fix for PR > 0
 * v0.3 17.07.2009 fixed title was not save issue
 * v0.2 15.07.2009 small url fix
 * v0.1 13.07.2009 initial release
 */

if(!class_exists('RankingBadge')):
class RankingBadge {
  var $id;
  var $version;
  var $options;
  var $path;
  var $http_cache;
  var $cache_file;
  var $locale;
  var $url;
  var $layouts;
  
  function RankingBadge() {
    $this->id         = 'rankingbadge';
    $this->version    = '0.5';
    $this->http_cache = array();
    $this->path       = dirname( __FILE__ );
    $this->cache_file = $this->path . '/cache/cache.gif';
    $this->url        = get_bloginfo('wpurl') . '/wp-content/plugins/' . $this->id; 
	  $this->locale     = get_locale();
    
    $this->layouts = array(
      array(
        'icon'    => '0.gif',
        'color' => 'e13abc'
      ),
      array(
        'icon'    => '1.gif',
        'color' => 'fd01c5'
      )
    );

	  if(empty($this->locale)) {
		  $this->locale = 'en_US';
    }

    load_textdomain($this->id, sprintf('%s/%s.mo', $this->path, $this->locale));

    $this->LoadOptions();

    if(!@isset($_GET['image'])) {
      if(is_admin()) {
        add_action('admin_menu', array( &$this, 'optionMenu')); 
      }
      else {
        add_action('wp_head', array(&$this, 'BlogHead'));
      }

      add_action('widgets_init', array(&$this, 'InitWidgets'));
    }
  }
  
  function optionMenu() {
    add_options_page('RankingBadge', 'RankingBadge', 8, __FILE__, array(&$this, 'optionMenuPage'));
  }

  function optionMenuPage() {

  $fields = array(
    // key => type, title, extra
    'title'           => array( 'text', __( 'Title', $this->id ) ),
    'technorati_api_key'   => array( 'text', __( 'Technorati <a href="http://technorati.com/developers/api/" target="_blank">API Key</a>', $this->id ), __( 'Needed to geht Technorati-Ranking!' ) ),
);

if(isset($_REQUEST[$this->id])) {
  @unlink($this->cache_file);
  
  $this->UpdateOptions( $_REQUEST[$this->id] );
  
  echo '<div id="message" class="updated fade"><p><strong>' . __( 'Settings saved!', $this->id ) . '</strong></p></div>'; 
}

?>
<div class="wrap">

<h2><?php _e( 'Settings', $this->id ); ?></h2>
<form method="post" action="">
<input type="hidden" name="cmd" value="save" />
<table class="form-table">
<?php if(!file_exists($this->path.'/cache/') || !is_writeable($this->path.'/cache/')): ?>
<tr valign="top"><th scope="row" colspan="3"><span style="color:red;"><?php _e('Warning! The cachedirectory is missing or not writeable!', $this->id); ?></span><br /><em><?php echo $this->path; ?>/cache</em></th></tr>
<?php endif; ?>
<tr valign="top">
  <th scope="row"><?php _e('Title', $this->id); ?></th>
  <td colspan="3"><input name="rankingbadge[title]" type="text" id="" class="code" value="<?=$this->options['title']?>" /> <?php _e('Title is shown above the Widget. If left empty can break your layout in widget mode!', $this->id); ?></td>
</tr>
<tr><th scope="row"><?php _e('Technorati API-Key', $this->id); ?></th>
  <td><input name="<?=$this->id?>[technorati_api_key]" type="text" class="code" value="<?=$this->options['technorati_api_key']?>" /><br /><?php _e('Get your Technorati API-Key <a href="http://technorati.com/account/signup/" target="_blank">here</a>! It\'s free!', $this->id); ?></td></tr>

<tr>
  <th scope="row"><?php _e('Layout', $this->id); ?></th>
  <td colspan="3">
  <input name="rankingbadge[layout]" type="radio" class="code" value="0"<?php echo intval($this->options['layout']) == 0 ? ' checked="checked"' : ''; ?> />
  <img src="<?=$this->url?>/img/0.gif" style="vertical-align:middle;" /><br /><br />
  <input name="rankingbadge[layout]" type="radio" class="code" value="1"<?php echo intval($this->options['layout']) == 1 ? ' checked="checked"' : ''; ?> />
  <img src="<?=$this->url?>/img/1.gif" style="vertical-align:middle;" />
  </td>
</tr>
</tr>
</table>

<p class="submit">
  <input type="submit" value="<?php _e( 'save', $this->id ); ?>" name="submit" />
</p>

</form>

</div>
<?php
}

  function LoadOptions() {

    if(!($this->options = get_option($this->id))) {
      $this->options = array(
        'layout' => 0,
        'title' => 'RankingBadge',
        'technorati_api_key' => ''
			);

      add_option( $this->id, $this->options, $this->name, 'yes' );

    }
  }
  
  function UpdateOption($name, $value) {
    $this->UpdateOptions(array($name => $value));
  }

  function UpdateOptions($options) {
    foreach($this->options as $k => $v) {
      if(array_key_exists($k, $options)) {
        $this->options[ $k ] = $options[ $k ];
      }
    }

		update_option($this->id, $this->options);
	}
  
  function BlogHead() {
    printf( '<meta name="%s" content="%s/%s" />' . "\n", $this->id, $this->id, $this->version ); 

    print( '<style type="text/css">
#rankingbadge, #rankingbadge small {padding: 0;margin: 0;color: #aaa;font-family: Arial, sans-serif;font-size: 10px;font-style: normal;font-weight: normal;letter-spacing: 0px;text-transform: none;}
#rankingbadge small a:hover, #rankingbadge small a:link, #rankingbadge small a:visited, #rankingbadge small a:active {color: #aaa;text-decoration:none;cursor: pointer;text-transform: none;}
</style>' );
  }
  
  function getPageRank() {
    include_once($this->path . '/lib/pagerank.class.php');
#echo $this->path . '/lib/pagerank.class.php';

    $url = get_bloginfo('wpurl');

    $pr = Pagerank::Fetch($url);

    return(is_null($pr) ? '0/10' : "$pr/10");
  }

  function getAlexaRank() {
    $url = get_bloginfo( 'wpurl' );

    $url = sprintf( "http://data.alexa.com/data?cli=10&dat=snbamz&url=%s", urlencode( $url ) );

    if( ( $data = $this->HttpGet( $url ) ) !== false )
    {
      preg_match( '|POPULARITY URL="(.*?)" TEXT="([0-9]+)"|', $data, $matches );

      if( count( $matches ) == 3 && !empty( $matches[ 2 ] ) )
      {
        return( intval( $matches[ 2 ] ) );
      }
    }
    
    return( '-' );
  }
  
  function HttpGet($url) {

    $id = md5( $url );
    
    if( array_key_exists( $id, $this->http_cache ) ) {
      return $this->http_cache[ $id ];
    }

    if(!class_exists('Snoopy')) {
      include_once(ABSPATH. WPINC. '/class-snoopy.php');
    }

	  $Snoopy = new Snoopy();

    if( @$Snoopy->fetch( $url ) ) {

      if( !empty( $Snoopy->results ) ) {
        $this->http_cache[ $id ] = $Snoopy->results;

        return $Snoopy->results;
      }
    }
    
    return false;
  }
  
  function GetTechnorati( $what /* links, rank */ ) { 
    if(empty($this->options['technorati_api_key'])) {
      return false;
    }
    
    $url = get_bloginfo('wpurl');

    $url = sprintf("http://api.technorati.com/cosmos?key=%s&url=%s", $this->options[ 'technorati_api_key'  ], urlencode($url));

    if( ( $data = $this->HttpGet( $url ) ) !== false )
    {
		  $pattern = array(
        'links' => '<inboundlinks>([0-9]+)</inboundlinks>',
  		  'rank'  => '<rank>([0-9]+)</rank>'
      );

      preg_match( '|' . $pattern[ $what ] . '|', $data, $matches );

      if( count( $matches ) == 2 && !empty( $matches[ 1 ] ) )
      {
        return( intval( $matches[ 1 ] ) );
      }
    }
    return( '-' );
  }
  
  function GetTechnoratiLinks() {
    return( $this->GetTechnorati( 'links' ) );
  }
  
  function getTechnoratiRank() {
    return $this->GetTechnorati('rank');
  }
  
  function getTitle() {
    $host = trim(strtolower($_SERVER['HTTP_HOST']));
  
    if(substr($host, 0, 4) == 'www.') {
      $host = substr($host, 4);
    }

    $titles = array('Grobekelle', 'www.grobekelle.de', 'Grobekelle.de', 'GrobeKelle', 'grobekelle.de', 'www.Grobekelle.de');
  
    return $titles[strlen($host) % count($titles)];

  }
  
  function GetBadgetTag() {
    return sprintf( '<div align="center" id="rankingbadge"><img src="%s/wp-content/plugins/rankingbadge/rankingbadge.php?draw=1" border="0" alt="RankingBadge" title="RankingBadge" /><br /><small>Plugin by <a href="http://www.grobekelle.de" class="snap_noshots" target="_blank">%s</a></small></div>', get_bloginfo('wpurl'), $this->getTitle());
  }
  
  function rgbColor(&$img, $rgb) {
    if( $rgb[ 0 ] == '#' ) {
      $rgb = substr( $rgb, 1 );
    }
    
    $a = substr($rgb, 0, 2);
    $b = substr($rgb, 2, 2);
    $c = substr($rgb, 4, 2);

    return imagecolorallocate($img, hexdec($a), hexdec($b), hexdec($c));
  }

  function draw() {
    clearstatcache();

    $create = false;
    
    if(!file_exists($this->cache_file)) {
      $create = true;
    }
    elseif(time() - filemtime($this->cache_file) > (3600 * 3)) {
      $create = true;
    }
    
    $create = true;

    if($create) {
      
      $layout = $this->layouts[intval($this->options['layout'])];
      
      $dummy = @imagecreatefromgif($this->path. '/img/'. $this->options['layout']. '.gif');
      /*
      if($layout['transparent'] !== false ) {        
        $background_color = imagecolorallocate($dummy, 0, 0, 0);
        imagecolortransparent($dummy, $background_color);
      }
      */       
      $w = imagesx($dummy);
      $h = imagesy($dummy);
        
      $img = imagecreatetruecolor($w, $h);

      imagecopyresampled($img, $dummy, 0, 0, 0, 0, $w, $h, $w, $h);
      
      imagedestroy($dummy);

      $color = $this->rgbColor($img, $layout['color']);

      $x = 97;
      $font = 2;

      $google = $this->getPageRank();
 #     $google = '1/10';
      imagestring($img, $font, $x, 17, $google, $color);
      
      $alexa = $this->getAlexaRank();
#      $alexa = 4124867;
      imagestring($img, $font, $x, 50, number_format($alexa, 0, '.', '.'), $color);
      
      $technorati = $this->getTechnoratiRank();
#     $technorati = 0;
      imagestring($img, $font, $x, 82, number_format($technorati, 0, '', '.'), $color);

      if(is_writeable($this->path. '/cache')) {
        @imagegif($img, $this->cache_file);
      }
      
    }
    else {
      $img = @imagecreatefromgif($this->cache_file);
    }
    
    header( 'Content-Type: image/gif' );

    imagegif($img);
    imagedestroy($img);
  }

  function Encode( $s ) {
    if( function_exists( 'utf8_decode' ) ) {
      $s = utf8_decode( $s );
    }
    
    return $s;
  }
  
  function InitWidgets() {
    if(function_exists('register_sidebar_widget')) {
      register_sidebar_widget('RankingBadge Widget', array(&$this, 'Widget'), null, 'widget_rankingbadge');
    }
  }
  
  function Widget($args) {
    extract($args);

    printf('%s%s%s%s%s%s', $before_widget, $before_title, $this->options['title'], $after_title, $this->GetBadgetTag(), $after_widget);
  }
}

function rankingbadge_display() {
  global $RankingBadge;

  if(!isset($RankingBadge)) {
    $RankingBadge = new RankingBadge();
  }

  if($RankingBadge) {
    print($RankingBadge->GetBadgetTag());
  }
}
endif;

if(@isset($_GET['draw'])) {
  include_once(dirname(__FILE__). '/../../../wp-config.php');

  if(!isset($RankingBadge)) {
    $RankingBadge = new RankingBadge();
  }

  $RankingBadge->draw();
}
else {
  add_action('plugins_loaded', create_function('$RankingBadge_sdla13Xa', 'global $RankingBadge; $RankingBadge = new RankingBadge();')); 
}

?>
