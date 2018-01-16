<?

  require_once('../Shortcode.php');
  
  use Shortcode\Shortcode;
  
  Shortcode::register('b', function($attr, $content) {

    return '<strong>'.$content.'</strong>';

  });
  
  $input = 'This text is [b]fat[/b]!';
    
  echo Shortcode::process($input);