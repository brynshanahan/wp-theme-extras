<?php
/* 
Adding some of the helpful functions node's path module to php
*/
if (!class_exists('paths')) {
  class Paths {
    /* Given two paths it will return the second path relative to the first */
    public static function relative ($from, $to) {
      // some compatibility fixes for Windows paths
      $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
      $to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
      $from = str_replace('\\', '/', $from);
      $to   = str_replace('\\', '/', $to);
  
      $from     = explode('/', $from);
      $to       = explode('/', $to);
      $relPath  = $to;
  
      foreach($from as $depth => $dir) {
          // find first non-matching dir
          if($dir === $to[$depth]) {
              // ignore this directory
              array_shift($relPath);
          } else {
              // get number of remaining dirs to $from
              $remaining = count($from) - $depth;
              if($remaining > 1) {
                  // add traversals up to first matching dir
                  $padLength = (count($relPath) + $remaining - 1) * -1;
                  $relPath = array_pad($relPath, $padLength, '..');
                  break;
              } else {
                  $relPath[0] = './' . $relPath[0];
              }
          }
      }
      return implode('/', $relPath);
    }
  
    function has_extension ($path, $ext) {
      return strpos($path, $ext) !== false;
    }
  
    public static function match_php_file ($path) {
      return self::has_extension($path, '.php');
    }
  
    /* Includes all php files by default */
    public static function include_all ($path, $match = self::MATCH_PHP_FILE, $includeCurrentDir = true) {    
      $paths = glob("$path/*");
    
      $val = array_reduce(
        $paths, 
        function($acc, $item) use ($match, $includeCurrentDir) {
          if (is_dir($item)) {
            /* 
            Always want to include the current directory in a recursive section
            */
            $acc = array_merge($acc, self::include_all($item, $match, true));
          } else if ($includeCurrentDir) {
            if ($match($item)) {
              $acc[] = $item;
            }
          }
          return $acc;
        }, 
        []
      );
    
      return $val;
    }
  }
} else {
  error_log('Tried including helper "paths" but the class already exists');
}