<?php
/**
 * This file is part of the RedKiteCmsBunde Application and it is distributed
 * under the GPL LICENSE Version 2.0. To use this application you must leave
 * intact this copyright notice.
 *
 * Copyright (c) RedKite Labs <webmaster@redkite-labs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.redkite-labs.com
 *
 * @license    GPL LICENSE Version 2.0
 *
 */

namespace RedKiteLabs\RedKiteCmsBundle\Tests\Unit\Core\Deploy;

use RedKiteLabs\RedKiteCmsBundle\Core\Deploy\AlTwigDeployerProduction;
use org\bovigo\vfs\vfsStream;

/**
 * AlTwigDeployerTest
 *
 * @author RedKite Labs <webmaster@redkite-labs.com>
 */
class AlTwigDeployerProductionTest extends AlTwigDeployer
{
    protected function setUp()
    {
        $this->templatesFolder = 'RedKiteCms';
        $this->siteRoutingFile = 'site_routing.yml';
        $this->assetsFolder = 'root\AcmeWebSiteBundle\Resources\public\\';
        
        parent::setUp();
    }
    
    protected function checkSiteMap($seo)
    {
        $sitemapFile = vfsStream::url('root\sitemap.xml');
        $this->assertFileExists($sitemapFile);
        $this->assertEquals($this->buildExpectedSitemap($seo), file_get_contents($sitemapFile));
    }
    
    protected function getDeployer()
    {
        return new AlTwigDeployerProduction($this->container);    
    }
    
    protected function initContainer()
    {
        parent::initContainer();
        
         $this->container->expects($this->at(9))
            ->method('getParameter')
            ->with('red_kite_labs_theme_engine.deploy.templates_folder')
            ->will($this->returnValue($this->templatesFolder));
        
        $this->container->expects($this->at(17))
            ->method('get')
            ->with('red_kite_cms.url_manager')
            ->will($this->returnValue($this->urlManager));

        $this->container->expects($this->at(18))
            ->method('get')
            ->with('red_kite_cms.block_manager_factory')
            ->will($this->returnValue($this->blockManagerFactory));

        $this->container->expects($this->at(19))
            ->method('getParameter')
            ->with('red_kite_cms.deploy_bundle.views_dir')
            ->will($this->returnValue('Resources/views'));
            
        $this->container->expects($this->at(20))
            ->method('get')
            ->with('red_kite_cms.themes_collection_wrapper')
            ->will($this->returnValue($this->themesCollectionWrapper));
        
        $this->containerAtSequenceAfterObjectCreation = 21;
    }
}