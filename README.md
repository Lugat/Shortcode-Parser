# Shortcode-Parser
Simple wordpress-like shortcode parser for PHP. Also parses nested shortcodes logically.

### Empty shortcodes
Empty shortcodes can be written in both ways: [tag] or [tag /]

```PHP
  Shortcode::register('week', function($attr, $content) {

    return intval(date('W'));

  });
  
  $input = 'Week in this year: [week]';
    
  echo Shortcode::process($input);
```

#### Output

Week in this year: 42

### Simple shortcodes

```PHP
  Shortcode::register('b', function($attr, $content) {

    return '<strong>'.$content.'</strong>';

  });
  
  $input = 'This text is [b]fat[/b]!';
    
  echo Shortcode::process($input);
```

#### Output

This text is __fat!__

### Shortcodes with attributes

```PHP
  
  Shortcode::register('time', function($attr, $content) {
    
    $attr = array_replace([
      'format' => 'H:i:s'
    ], $attr);
    
    return date($attr['format']);
    
  });
  
  $input = 'It\'s [time format="g" /] o\'clock!';
    
  echo Shortcode::process($input);
```

#### Output

It's 12 o'clock!

### Nested shortcodes

```PHP
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
```