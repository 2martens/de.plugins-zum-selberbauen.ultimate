<?php
namespace ultimate\page;
use wcf\page\AbstractPage;
use wcf\system\cache\CacheHandler;
use wcf\system\request\RouteHandler;
use wcf\util\StringUtil;

/**
 * Shows a page.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage page
 * @category Ultimate CMS
 */
class PagePage extends AbstractPage {
    /**
     * @see \wcf\page\AbstractPage::$useTemplate
     * @var boolean
     */
    public $useTemplate = false;
    
    /**
     * @see \wcf\page\AbstractPage::$neededModules
     * @var string[]
     */
    public $neededModules = array(
        'module_ultimateFrontend'
    );
    
    /**
     * Contains the Page object.
     * @var \ultimate\data\page\Page
     */
    public $page = null;
    
    /**
     * Contains an array of the given page slugs.
     * @var string[]
     */
    public $pageSlugs = array();
    
    /**
     * @see \wcf\page\AbstractPage::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        /* @var $routeData string[] */
        $routeData = RouteHandler::getInstance()->getRouteData();
        $this->pageSlugs = explode('/', StringUtil::trim($routeData['pageSlug']));
    }
    
    /**
     * @see \wcf\page\AbstractPage::readData()
     */
    public function readData() {
        parent::readData();
        $pagesToSlug = $this->loadCache();
        /* @var $page \ultimate\data\page\Page */
        $page = $pagesToSlug[$this->pageSlugs[0]];
        if (count($this->pageSlugs > 1)) {
            $page = $this->getRealPage($page);
        }
        $this->page = $page;
        
        // TODO implement template
    }
    
    /**
     * Returns the real page for this request.
     *
     * @param \ultimate\data\page\Page $page
     * @param integer                  $i
     * @return \ultimate\data\page\Page
     */
    protected function getRealPage(\ultimate\data\page\Page $page, $i = 1) {
        $childPages = $page->__get('childPages');
        $maxI = count($this->pageSlugs) - 1;
        /* @var $returnPage \ultimate\data\page\Page */
        $returnPage = null;
        foreach ($childPages as $pageID => $page) {
            if ($page->__get('pageSlug') != $this->pageSlugs[$i]) continue;
            if ($i == $maxI) {
                $returnPage = $page;
                break;
            }
            $returnPage = $this->getPage($page, ++$i);
        }
        return $returnPage;
    }
    
    /**
     * Loads the cache.
     */
    protected function loadCache() {
        $cacheName = 'page';
        $cacheBuilderClassName = '\ultimate\system\cache\builder\PageCacheBuilder';
        $file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
        CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
        return CacheHandler::getInstance()->get($cacheName, 'pagesToSlug');
    }
    
}