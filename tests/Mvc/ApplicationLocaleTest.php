<?php

require_once __DIR__.'/../../vendor/autoload.php';

use Kristuff\Miniweb\Core\Locale;
use Kristuff\Miniweb\Mvc\Application;

class ApplicationLocaleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testAppLocaleBeforeInit()
    {
        $this->assertNull(Application::text('HELLO', 'en-US'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testAppLocaleAfterInit()
    {
        $app = new Application();
        $this->assertTrue($app->locales()->registerAutoloader(__DIR__ . '/../_data/locale', ['en-US', 'fr-FR'], 'app.locale.php'));
        $this->assertTrue($app->locales()->setDefault('fr-FR'));

        $this->assertEquals('Hello', Application::text('HELLO', 'en-US'));
        $this->assertEquals('Welcome!', Application::text('PAGE_WELCOME_TILE', 'en-US'));
        $this->assertEquals('This is a paragraph from app.locale.php.', Application::text('PAGE_WELCOME_TEXT', 'en-US'));
        $this->assertNull(Application::text('NOT_EXISTING_KEY', 'en-US'));

        $this->assertEquals('Bonjour', Application::text('HELLO', 'fr-FR'));
        $this->assertEquals('Bienvenue!', Application::text('PAGE_WELCOME_TILE', 'fr-FR'));
        $this->assertEquals('Ceci est un paragraphe extrait de app.locale.php.', Application::text('PAGE_WELCOME_TEXT', 'fr-FR'));
        $this->assertNull(Application::text('NOT_EXISTING_KEY', 'fr-FR'));

        // test defaults
        $this->assertEquals('Bonjour', Application::text('HELLO'));
        $this->assertEquals('Bienvenue!', Application::text('PAGE_WELCOME_TILE'));
        $this->assertEquals('Ceci est un paragraphe extrait de app.locale.php.', Application::text('PAGE_WELCOME_TEXT'));
        $this->assertNull(Application::text('NOT_EXISTING_KEY'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testAppLocaleSectionsAfterInit()
    {
        $app = new Application();
        $this->assertTrue($app->locales()->registerAutoloader(__DIR__ . '/../_data/locale_with_section', ['en-US', 'fr-FR'], 'locale.php'));
        $this->assertTrue($app->locales()->setDefault('fr-FR'));
 
        $this->assertFalse($app->locales()->isLoaded('en-US','section1'));
        $this->assertEquals('Section1 title',Application::textSection('PAGE_TITLE', 'section1', 'en-US'));
        $this->assertEquals('Section2 title',Application::textSection('PAGE_TITLE', 'section2', 'en-US'));
        $this->assertEquals('This is a paragraph from section1.',Application::textSection('PAGE_TEXT', 'section1', 'en-US'));
        $this->assertEquals('This is a paragraph from section2.',Application::textSection('PAGE_TEXT', 'section2', 'en-US'));
        $this->assertTrue($app->locales()->isLoaded('en-US','section1'));

        $this->assertEquals('Titre de la section 1',Application::textSection('PAGE_TITLE', 'section1', 'fr-FR'));
        $this->assertEquals('Titre de la section 2',Application::textSection('PAGE_TITLE', 'section2', 'fr-FR'));
        $this->assertEquals('Ceci est un paragraphe de la section 1.',Application::textSection('PAGE_TEXT', 'section1', 'fr-FR'));
        $this->assertEquals('Ceci est un paragraphe de la section 2.',Application::textSection('PAGE_TEXT', 'section2', 'fr-FR'));

        // test defaults
        $this->assertEquals('Titre de la section 1',Application::textSection('PAGE_TITLE', 'section1'));
        $this->assertEquals('Titre de la section 2',Application::textSection('PAGE_TITLE', 'section2'));
        $this->assertEquals('Ceci est un paragraphe de la section 1.',Application::textSection('PAGE_TEXT', 'section1'));
        $this->assertEquals('Ceci est un paragraphe de la section 2.',Application::textSection('PAGE_TEXT', 'section2'));

    }
    
}