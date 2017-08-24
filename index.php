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
    
    $attr = array_replace([
      'format' => 'H:i:s'
    ], $attr);
    
    return date($attr['format']);
    
  });
  
  $content = 'Es ist genau [b tag="b"][time format="h:i:s A"] Uhr[/b] Uhr. Außerdem ist heute [b][day][/b].';
  
  echo Shortcode::process($content);