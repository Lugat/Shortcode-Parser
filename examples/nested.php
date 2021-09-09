<?php

  require_once('../Shortcode.php');
  
  use Shortcode\Shortcode;
  
  Shortcode::register('row', function($attr, $content) {
    
    return '<div class="row">'.$content.'</div>';
    
  });
  
  Shortcode::register('col', function($attr, $content) {
    
    $attr = array_replace([
      'width' => 12
    ], $attr);
    
    return '<div class="col-sm-'.$attr['width'].'">'.$content.'</div>';
    
  });
  
  $input = ''
  . '[row]'
    . '[col width="9"]Col 1'
      . '[row]'
        . '[col width="6"]Col 1.1[/col]'
        . '[col width="6"]Col 1.2[/col]'
      . '[/row]'
    . '[/col]'
   . '[col width="3"]Col 2[/col]'
  . '[/row]';
  
  echo Shortcode::process($input);