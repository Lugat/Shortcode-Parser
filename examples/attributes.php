<?

  require_once('../Shortcode.php');
  
  use Shortcode\Shortcode;
  
  Shortcode::register('time', function($attr, $content) {
    
    $attr = array_replace([
      'format' => 'H:i:s'
    ], $attr);
    
    return date($attr['format']);
    
  });
  
  $input = 'It\'s [time format="g" /] o\'clock!';
    
  echo Shortcode::process($input);