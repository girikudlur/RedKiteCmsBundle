<?php
/**
 * This file is part of the RedKiteCmsBunde Application and it is distributed
 * under the GPL LICENSE Version 2.0. To use this application you must leave
 * intact this copyright notice.
 *
 * Copyright (c) RedKite Labs <webmaster@redkite-labs.com>
 *
 * For the full copyright and license infpageRepositoryation, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.redkite-labs.com
 *
 * @license    GPL LICENSE Version 2.0
 *
 */

namespace RedKiteLabs\RedKiteCmsBundle\Tests\Functional\Controller;

use RedKiteLabs\RedKiteCmsBundle\Tests\WebTestCaseFunctional;
use RedKiteLabs\RedKiteCmsBundle\Core\Repository\Propel\AlPageRepositoryPropel;
use RedKiteLabs\RedKiteCmsBundle\Core\Repository\Propel\AlSeoRepositoryPropel;
use RedKiteLabs\RedKiteCmsBundle\Core\Repository\Propel\AlBlockRepositoryPropel;

/**
 * CmsControllerTest
 *
 * @author RedKite Labs <webmaster@redkite-labs.com>
 */
class CmsControllerTest extends WebTestCaseFunctional
{
    private $pageRepository;
    private $seoRepository;
    private $blockRepository;

    public static function setUpBeforeClass()
    {
        self::$languages = array(array('LanguageName'      => 'en',));

        self::$pages = array(array('PageName'      => 'index',
                                    'TemplateName'  => 'home',
                                    'IsHome'        => '1',
                                    'Permalink'     => 'this is a website fake page',
                                    'MetaTitle'         => 'page title',
                                    'MetaDescription'   => 'page description',
                                    'MetaKeywords'      => 'key'),
                            array('PageName'      => 'page1',
                                    'TemplateName'  => 'empty',
                                    'Permalink'     => 'page-1',
                                    'MetaTitle'         => 'page 1 title',
                                    'MetaDescription'   => 'page 1 description',
                                    'MetaKeywords'      => ''));
        self::populateDb();
    }

    protected function setUp()
    {
        parent::setUp();

        $this->pageRepository = new AlPageRepositoryPropel();
        $this->seoRepository = new AlSeoRepositoryPropel();
        $this->blockRepository = new AlBlockRepositoryPropel();
    }

    public function testOpeningAPageThatDoesNotExistShowsTheDefaultWelcomePage()
    {
        $crawler = $this->client->request('GET', 'backend/en/fake');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertRegExp(
            '/welcome_title|Welcome to RedKite CMS/si',
            $response->getContent()
        );
        $this->assertRegExp(
            '/welcome_background|This is the RedKite CMS background and usually it should be hide/si',
            $response->getContent()
        );
    }

    public function testExistingPageIsOpened()
    {
        $crawler = $this->client->request('GET', 'backend/en/index');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("This is the RedKiteCms background and usually it should be hide")')->count() == 0);

        $this->checkCms($crawler);
        $this->assertCount(1, $crawler->filter('#block_2'));
        $this->assertCount(1, $crawler->filter('#block_2')->filter('[data-name="block_2"]'));
        $this->assertCount(1, $crawler->filter('#block_2')->filter('[data-editor="enabled"]'));
        $this->assertCount(1, $crawler->filter('#block_2')->filter('[data-hide-when-edit="false"]'));
        $this->assertCount(1, $crawler->filter('#block_2')->filter('[data-included=""]'));
        $this->assertCount(1, $crawler->filter('#block_2')->filter('[data-type="Text"]'));
        $this->assertCount(1, $crawler->filter('#block_2')->filter('[data-slot-name="content_title_1"]'));
        $this->assertCount(1, $crawler->filter('#block_2')->filter('[data-block-id="2"]'));
        $this->assertCount(1, $crawler->filter('#block_2')->filter('[data-content-editable="true"]'));
        $this->checkIncludedBlock($crawler);
        $this->assertCount(43, $crawler->filter('[data-editor="enabled"]'));
    }

    public function testMovingThroughPages()
    {
        $menu = '<ul class="business-menu">
            <li><a href="this-is-a-website-fake-page">home</a></li>
            <li><a href="page1">Another page</a></li>
        </ul>';

        $block = $this->blockRepository->fromPK(2);
        $block->setType('Text');
        $block->setContent($menu);
        $block->save();
        
        $crawler = $this->client->request('GET', '/backend/en/index');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $link = $crawler->selectLink('Another page')->link();
        $crawler = $this->client->click($link);
        
        $this->assertTrue($crawler->filter('html:contains("This is the RedKiteCms background and usually it should be hide")')->count() == 0);
        $this->assertCount(1, $crawler->filter('#block_25'));
        $this->assertCount(1, $crawler->filter('#block_25')->filter('[data-name="block_25"]'));
        $this->assertCount(1, $crawler->filter('#block_25')->filter('[data-editor="enabled"]'));
        $this->assertCount(1, $crawler->filter('#block_25')->filter('[data-hide-when-edit="false"]'));
        $this->assertCount(1, $crawler->filter('#block_25')->filter('[data-included=""]'));
        $this->assertCount(1, $crawler->filter('#block_25')->filter('[data-type="Text"]'));
        $this->assertCount(1, $crawler->filter('#block_25')->filter('[data-slot-name="page_title"]'));
        $this->assertCount(1, $crawler->filter('#block_25')->filter('[data-block-id="25"]'));
        $this->assertCount(1, $crawler->filter('#block_25')->filter('[data-content-editable="true"]'));
        $this->checkIncludedBlock($crawler);
        $this->assertCount(24, $crawler->filter('[data-editor="enabled"]'));
    }

    public function testOpenPageFromPermalink()
    {
        $crawler = $this->client->request('GET', 'backend/en/this-is-a-website-fake-page');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

    private function checkIncludedBlock($crawler)
    {
        $this->assertCount(0, $crawler->filter('#block_28'));
        $this->assertCount(1, $crawler->filter('[data-name="block_28"]'));
        $this->assertCount(1, $crawler->filter('[data-name="block_28"]')->filter('[data-included="1"]'));
        $this->assertCount(1, $crawler->filter('[data-name="block_28"]')->filter('[data-type="Link"]'));
        $this->assertCount(1, $crawler->filter('[data-name="block_28"]')->filter('[data-slot-name="6-0"]'));
    }
    
    private function checkCms($crawler)
    {
        $this->checkToolbar($crawler);
        $this->checkStylesheets($crawler);
        $this->checkJavascripts($crawler);
    }

    private function checkToolbar($crawler)
    {
        $this->assertEquals(1, $crawler->filter('#al_control_panel')->count());
        $this->check($crawler, '#al_start_editor', "/cms_controller_label_edit|Edit/si");
        $this->check($crawler, '#al_stop_editor', "/cms_controller_label_stop|Stop/si");
        $this->check($crawler, '#al_open_pages_panel', "/cms_controller_label_pages|Pages/si");
        $this->check($crawler, '#al_open_languages_panel', "/cms_controller_label_languages|Languages/si");
        $this->check($crawler, '#al_open_themes_panel', "/cms_controller_label_themes|Themes/si");
        $this->check($crawler, '#al_open_media_library', "/cms_controller_label_media_library|Media Library/si");        
        $this->check($crawler, '#al_languages_navigator', "/en/si");        
        $el = $crawler->filter('.al_deployer');
        $this->assertEquals(2, $el->count());
        $this->check($crawler, '#al_pages_navigator', "/index/si");
        $this->check($crawler, '#al_available_languages', "/EnglishItalian/si");
    }

    private function checkStylesheets($crawler)
    {
        $assets = $crawler->filter('link')->extract(array('href'));
        $this->assertGreaterThanOrEqual(5, $assets);
        $assets = array_filter($assets, 'self::ignoreAssetic');
        //TODO $this->assertCount(3, $assets);
    }

    private function checkJavascripts($crawler)
    {
        $assets = array_filter($crawler->filter('script')->extract(array('src')));
        $this->assertGreaterThanOrEqual(15, $assets);
        $assets = array_filter($assets, 'self::ignoreAssetic');
        //TODO $this->assertCount(14, $assets);
    }

    private function check($crawler, $element, $value)
    {
        $el = $crawler->filter($element);
        $this->assertEquals(1, $el->count());
        $this->assertRegExp(
            $value,
            trim($el->text())
        );
    }

    private static function ignoreAssetic($key)
    {
        return false !== strpos($key, 'bundles');
    }
}
