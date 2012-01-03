<?php
namespace ultimate\system;
use ultimate\page\IndexPage;
use ultimate\system\config\ConfigEntry;
use ultimate\system\config\storage\ConfigStorage;
use ultimate\system\UltimateCore;

use wcf\system\SingletonFactory;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\RequestHandler;
use wcf\util\FileUtil;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Handles the incoming links.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
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
     * Contains the domain path of this application.
     * @var string
     */
    protected $domainPath = '';
    
    /**
     * Contains an array of the configurations.
     * @var array
     */
    protected $viewConfigurations = array();
    
    /**
     * Contains an array of the links
     * @var array
     */
    protected $linkList = array();
    
    
    /**
     * Handles a http request.
     */
    public function handle() {
        $this->readParameters();
        $this->readCache();
        $this->validateRequest();
        
        $config = $this->viewConfigurations[$this->requestURI];
        $callData = array(
        	'templateName' => $config['templateName'],
            'content' => array()
        );
        $configStorage = unserialize($config['storage']);
        $entries = $configStorage->getEntries();
        foreach ($entries['left'] as $id => $entry) {
           $callData['content']['left'.$id] = $entry->getContent();
        }
        foreach ($entries['center'] as $id => $entry) {
            $callData['content']['center'.$id] = $entry->getContent();
        }
        foreach ($entries['right'] as $id => $entry) {
            $callData['content']['right'.$id] = $entry->getContent();
        }
        
        $controllerObj = '\ultimate\page\GenericCMSPage';
        new $controllerObj($callData);
    }
    
    /**
     * Returns the request uri.
     */
    public function getRequestURI() {
        return $this->requestURI;
    }
    
    /**
     * Reads the given parameters.
     */
    protected function readParameters() {
        if (isset($_GET['request'])) $this->requestURI = FileUtil::removeTrailingSlash(StringUtil::trim($_GET['request']));
    }
    
    /**
     * Reads the cache for this class.
     */
    protected function readCache() {
        //loading domain path from cache
        $cache = 'domain-paths';
        $file = WCF_DIR.'cache/cache.'.$cache.'.php';
        $className = '\ultimate\system\cache\builder\ApplicationDomainPathCacheBuilder';
        CacheHandler::getInstance()->addResource($cache, $file, $className);
        
        $domainPaths = CacheHandler::getInstance()->get($cache, 'paths');
        $this->domainPath = $domainPaths[PACKAGE_ID];
        
        //loading links from cache
        $cache = 'ultimate-links-'.PACKAGE_ID;
        $file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
        $className = '\ultimate\system\cache\builder\UltimateLinksCacheBuilder';
        CacheHandler::getInstance()->addResource($cache, $file, $className);
        $this->linkList = CacheHandler::getInstance()->get($cache, 'links');
        $this->viewConfigurations = CacheHandler::getInstance()->get($cache, 'configs');
    }
    
    /**
     * Validates the given request uri.
     * @throws IllegalLinkException
     */
    protected function validateRequest() {
        $this->requestURI = str_replace($this->domainPath, '', $this->requestURI);
        if ($pos = strpos($this->requestURI, 'index.php') !== false) {
            HeaderUtil::redirect($this->requestURI.'/');
            exit;
        } elseif ($this->requestURI == 'index') {
            new IndexPage();
            exit;
        }
        //@todo remove for production later
        if (false) { //does nothing
            echo $this->getRequestURI().'<br /><pre>'.print_r($this->linkList, true).'</pre>';
            //exit;
        }
        
        if (!in_array($this->requestURI, $this->linkList)) {
            throw new IllegalLinkException();
        }
    }
    
}
