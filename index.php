<?

  require_once('Shortcode.php');
  
  use Shortcode\Shortcode;

  Shortcode::register('b', function($attr, $content) {
    
    return '<strong>'.$content.'</strong>';
    
  });
  
  Shortcode::register('day', function($attr, $content) {
    
    return date('l');
    
  });
  
  Shortcode::register('time', function($attr, $content) {
    
    return date($attr['format']);
    
  });

  
  $content = 'Es ist genau [b][time format="H:i:s" prefix="Uhr"][/b] Uhr. Außerdem ist heute [b][day][/b].';
  
  echo Shortcode::parse($content);