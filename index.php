<?

  require_once('Shortcode.php');
  
  use Shortcode\Shortcode;
  
  Shortcode::register('row', function($attr, $content) {
    
    return '<div class="row">'.$content.'</div>';
    
  });
  
  Shortcode::register('col', function($attr, $content) {
    
    return '<div class="col-sm-6">'.$content.'</div>';
    
  });
  
  $content = '[row]'
              . '[col]Content'
                . '[row]'
                  . '[col]Col 1[/col]'
                  . '[col]Col 2[/col]'
                . '[/row]'
              . '[/col]'
              . '[col]Sidebar[/col]'
            . '[/row]';
    
  echo Shortcode::process($content).'<br />';
  exit();

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
  
  $content = 'Es ist genau [b tag="b"][time format="h:i:s A"/] Uhr[/b] Uhr. Außerdem ist heute [b][day/][/b].';
    
  echo Shortcode::process($content);