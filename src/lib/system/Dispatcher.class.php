<?php
namespace ultimate\system;
use wcf\system\exception\IllegalLinkException;
use wcf\util\FileUtil;
use wcf\util\StringUtil;
use wcf\system\cache\CacheHandler;
use wcf\system\SingletonFactory;
use ultimate\system\UltimateCore;

/**
 * Handles the incoming links.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system
 * @category Ultimate CMS
 */
class Dispatcher extends SingletonFactory {
    
    /**
     * Contains the request data.
     * @var string
     */
    protected $requestURI = '';
    
    /**
     * Handles a http request.
     */
    public function handle() {
        if (isset($_GET['request'])) $this->requestURI = FileUtil::removeTrailingSlash(StringUtil::trim($_GET['request']));
        
        //loading domain path from cache
        $cache = 'domain-paths';
        $file = WCF_DIR.'cache/cache.'.$cache.'.php';
        $className = 'ultimate\system\cache\builder\ApplicationDomainPathCacheBuilder';
        CacheHandler::getInstance()->addResource($cache, $file, $className);
        
        $domainPaths = CacheHandler::getInstance()->get($cache, 'paths');
        $domainPath = $domainPaths[PACKAGE_ID];
        //cut domain path away from request uri
        $this->requestURI = substr($this->requestURI, strlen($domainPath));
        
        //loading links from cache
        $cache = 'ultimate-links-'.PACKAGE_ID;
        $file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
        $className = 'ultimate\system\cache\builder\UltimateLinksCacheBuilder';
        CacheHandler::getInstance()->addResource($cache, $file, $className);
        $linkList = array();
        $linkList = CacheHandler::getInstance()->get($cache, 'links');
        
        if (DEBUG) {
            echo $this->getRequestURI().'<br /><pre>'.print_r($linkList, true).'</pre>';
            //exit;
        }
        
        if (!in_array($this->requestURI, $linkList)) {
            throw new IllegalLinkException();
        }
        //loading configurations from cache
        $viewConfigurations = array();
        $viewConfigurations = CacheHandler::getInstance()->get($cache, 'configs');
        
        //$config['templateName'] = template name of the overall generated template
        //$config['content'] = array ('content id' => '*ComponentPage')
        $config = $viewConfigurations[$this->requestURI];
        $callData = array(
        	'templateName' => $config['templateName'],
            'content' => array()
        );
        foreach ($config['content'] as $id => $component) {
            if (DEBUG) echo $component.'<br /><pre>'.print_r(UltimateCore::getTPL()->templatePaths, true).'</pre>';
            require_once(ULTIMATE_DIR.'lib/page/'.$component.'.class.php');
            $component = 'ultimate\page\\'.$component;
            $obj = new $component($id);
            $result = $obj->getOutput(); //returns output
            $callData['content'][$id] = $result;
        }
        
        
        $controllerObj = 'ultimate\page\GenericCMSPage';
        new $controllerObj($callData);
    }
    
    /**
     * Returns the request uri.
     */
    public function getRequestURI() {
        return $this->requestURI;
    }
    
}
