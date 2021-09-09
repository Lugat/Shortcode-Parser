<?php

  require_once('../Shortcode.php');
  
  use Shortcode\Shortcode;
  
  Shortcode::register('week', function($attr, $content) {

    return intval(date('W'));

  });
  
  $input = 'Week in this year: [week]';
    
  echo Shortcode::process($input);