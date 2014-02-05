<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


class Mygento_HamlSass_Model_Design_Package extends Mage_Core_Model_Design_Package {
  
    /**
     * Check whether requested file exists in specified theme params
     *
     * Possible params:
     * - _type: layout|template|skin|locale
     * - _package: design package, if not set = default
     * - _theme: if not set = default
     * - _file: path relative to theme root
     *
     * @see Mage_Core_Model_Config::getBaseDir
     * @param string $file
     * @param array $params
     * @return string|false
     */
  
    public function validateFile($file, array $params)
    {
      if(isset($params['_type']) && ($params['_type'] === 'template')) {
        $pos = strrpos($file, '.');
        $file2 = substr($file, 0, $pos);
        $ext = substr($file, $pos);
        $file2 .= ($ext === '.phtml') ?  '.haml' : '.phtml';
        return ($file = parent::validateFile($file, $params)) ? 
                $file : parent::validateFile($file2, $params);
      } else {
        return parent::validateFile($file, $params);
      }
    }
  
    /**
     * Check for files existence by specified scheme
     *
     * If fallback enabled, the first found file will be returned. Otherwise the base package / default theme file,
     *   regardless of found or not.
     * If disabled, the lookup won't be performed to spare filesystem calls.
     *
     * @param string $file
     * @param array &$params
     * @param array $fallbackScheme
     * @return string
     */
    protected function _fallback($file, array &$params, array $fallbackScheme = array(array()))
    {
        if ($this->_shouldFallback) {
            foreach ($fallbackScheme as $try) {
                $params = array_merge($params, $try);
                $filename = $this->validateFile($file, $params);
                if ($filename) {
                    return $filename;
                }
            }
            $params['_package'] = self::BASE_PACKAGE;
            $params['_theme']   = self::DEFAULT_THEME;
        }
        return $this->_renderFilename($file, $params);
    }

    
}