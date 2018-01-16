<?

  namespace Shortcode;
  
  /**
   * Simple Shortcode Parser
   * 
   * @copyright Copyright (c) 2018 SquareFlower Websolutions
   * @license BSD License
   * @author Lukas Rydygel <hallo@squareflower.de>
   * @version 0.3.2
   */
  
  abstract class Shortcode
  {
    
    /**
     * Collection of registered shortcodes
     * 
     * @var array
     */
    protected static $shortcodes = [];
    
    /**
     * Register a callback function for a shortcode
     * Default attributes can be pased
     * 
     * Existing shortcodes can't be overwritten, they must be unregistered first
     * 
     * @param string $tag
     * @param mixed $callback
     * @param array $attr
     * @return boolean
     */
    public static function register($tag, $callback, array $attr = [])
    {
      
      // check if the shortcode exists
      if (!array_key_exists($tag, self::$shortcodes)) {
        
        // register the shortcode
        self::$shortcodes[$tag] = ['callback' => $callback, 'attr' => $attr];
        return true;
        
      }
      
      return false;
      
    }
    
    /**
     * Removes a registered shortcode
     * 
     * @param string $tag
     * @return boolean
     */
    public static function unregister($tag)
    {
      
      // check if the shortcode exists
      if (array_key_exists($tag, self::$shortcodes)) {
        
        // remove the shortcode
        unset(self::$shortcodes[$tag]);
        return true;
        
      }
      
      return false;
      
    }

    /**
     * Process all shortcodes inside a string
     * 
     * You may also allow only certain shortcodes. If no shortcodes were specified, all registered shortcodes will be used.
     * 
     * @param string $content
     * @param array $allow
     * @return type
     */
    public static function process($content, array $allow = [])
    {
                        
      // return if content is empty
      if (empty($content)) {
        return $content;
      }
      
      // return if no shortcodes exist
      if (empty(self::$shortcodes)) {
        return $content; 
      }
      
      // get all allowed shortcodes
      $allowedShortcodes = self::getAllowedShortcodes($allow);
      
      // return if no shortcodes are allowed
      if (empty($allowedShortcodes)) {
        return $content;
      }    
      
      // execute the shortcodes
      return self::execute($allowedShortcodes, $content);
      
    }
    
    /**
     * Gets all allowed shortcodes
     * 
     * @param array $allow
     * @return array
     */
    protected static function getAllowedShortcodes(array $allow)
    {
      
      // return all if no shortcodes were specified
      if (empty($allow)) {
        return self::$shortcodes;
      }
      
      // intersect by the registered tags
      return array_intersect_key(self::$shortcodes, array_flip($allow, $allow));
      
    }
    
    /**
     * Executes the content with a set of given shortcodes
     * 
     * @param array $shortcodes
     * @param string $content
     * @return string
     */
    protected static function execute($shortcodes, $content)
    {
            
      $offset = 0;
      
      foreach ($shortcodes as $tag => $data) {
        
        // Regular expression for opening and closing tags
        $open = "/\[{$tag}(\s(.*?))?\]/i";
        $close = "/\[\/{$tag}\]/i";
        
        $size = strlen($tag)+3;
                
        if (preg_match_all($open, $content, $openMatches, PREG_OFFSET_CAPTURE) && preg_match_all($close, $content, $closeMatches, PREG_OFFSET_CAPTURE)) {
          
          // Find matching tags
          $matches = self::findMatchingTags($openMatches[0], $closeMatches[0]);
          
          // Walk through matching tags
          foreach ($matches as $match) {
            
            // Calculate the start and endpoint of the part
            $start = $match[0];
            $end = $match[1] - $start + $size + $offset;

            // Get the part from the content
            $part = substr($content, $start, $end);
            $length = strlen($part);

            // Replace the part with the parsed results from the shortcode
            $replace = self::parseTagInPart($tag, $part);
            $content = substr_replace($content, $replace, $start, $end);
            
            // Correct the offset
            $offset += strlen($replace) - $length;
             
          }

        }
              
      }
                  
      return self::parseEmptyTags($shortcodes, $content);
      
    }
    
    protected static function findMatchingTags($openMatches, $closeMatches)
    {
      
      $tags = [];
      
      foreach (array_reverse($openMatches) as $i => $openMatch) {
                
        $diff = null;
        
        foreach ($closeMatches as $j => $closeMatch) {
          
          if ($openMatch[1] < $closeMatch[1]) {
            
            $d = $closeMatch[1] - $openMatch[1];
            
            if (!isset($diff) || $d < $diff) {
              
              $diff = $d;
              
              $tags[$i] = [$openMatch[1], $closeMatch[1]];
              
              unset($openMatches[$i]);
              unset($closeMatches[$j]);
              
            }
            
          }
          
        }
        
      }
      
      return $tags;
      
    }
    
    protected static function parseEmptyTags($shortcodes, $content)
    {
            
      // get the regular expression for the given shortcodes
      $regexp = self::createRegExp($shortcodes);
            
      // run a replace callback for the regular expression
      return preg_replace_callback($regexp, function($matches) {
        
        // default values
        $attr = [];
        $content = '';
        
        // check for the number of matches
        switch (count($matches)) {
          
          // shortcode without params
          case 2:
            list(, $tag) = $matches;
          break;
        
          // shortcode with attr
          case 4:
            list(, $tag, , $attr) = $matches;
          break;
          
        }
        
        return self::parseInternal($tag, $attr);
        
      }, $content);
      
    }
    
    /**
     * Parse a single shortcode in a given part of the content
     * 
     * @param string $tag
     * @param string $part
     * @return string
     */
    protected static function parseTagInPart($tag, $part)
    {
      
      $regexp = "/\[{$tag}(\s(.*?))?\](.*)\[\/{$tag}\]/is";
      
      return preg_replace_callback($regexp, function($matches) use($tag) {

        // default values
        $attr = [];
        $content = '';

        // check for the number of matches
        switch (count($matches)) {

          // shortcode with attr, no content
          case 3:
            list(, , $attr) = $matches;
          break;

          // shortcodes with attr and content
          case 4:
            list(, , $attr, $content) = $matches;
          break;

        }

        return self::parseInternal($tag, $attr, $content);

      }, $part);
      
    }
    
    protected static function parseInternal($tag, $attr, $content = '')
    {
      
      $content = trim($content);
      
      // if no params were found, they don't need to be parsed
      if (!is_array($attr)) {
        $attr = self::parseAttributes($attr);
      }

      $attr = array_replace($attr, self::$shortcodes[$tag]['attr']);

      // run the callback with params and content
      return call_user_func_array(self::$shortcodes[$tag]['callback'], [$attr, $content]);
      
    }
    
    /**
     * Parses the attributes by using XML
     * 
     * @param string $attr
     * @return array
     */
    protected static function parseAttributes($attr)
    {
      
      $xml = (array) new \SimpleXMLElement("<element $attr />"); 
      
      return array_key_exists('@attributes', $xml) ? $xml['@attributes'] : [];
      
    }
    
    /**
     * Created one regular expression for all given shortcodes.
     * 
     * Using one regular expression instead looping through the shortcodes increased the speed.
     * 
     * @param array $shortcodes
     * @return string
     */
    protected static function createRegExp($shortcodes)
    {
      
      $tags = implode('|', array_keys($shortcodes));
      
      return "/\[({$tags})(\s(.*?))?\/?\]/is"; 
      
    }

  }