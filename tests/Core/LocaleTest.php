<?php

require_once __DIR__.'/../../vendor/autoload.php';

use Kristuff\Miniweb\Core\Locale;
use Kristuff\Miniweb\Core\Config;
use Kristuff\Miniweb\Mvc\Application;

class LocaleTest extends \PHPUnit\Framework\TestCase
{

    public function testLocaleLoad()
    {
        $texts = [
            'HELLO'                 =>  'Hello',
            'PAGE_WELCOME_TILE'     =>  'Welcome!',
            'PAGE_WELCOME_TEXT'     =>  'This is a paragraph.'
        ];
        $locale = new Locale();
        $locale->load('en-US', $texts);

        $this->assertEquals('Hello', $locale->get('HELLO'));
        $this->assertEquals('Welcome!', $locale->get('PAGE_WELCOME_TILE'));
        $this->assertEquals('This is a paragraph.', $locale->get('PAGE_WELCOME_TEXT'));
        $this->assertNull($locale->get('NOT_EXISTING_KEY'));

    }
    
    public function testAutoloadLocales()
    {
        $locale = new Locale();

        $this->assertTrue($locale->registerAutoloader(__DIR__ . '/../_data/locale', ['en-US', 'fr-FR']));

        $this->assertEquals('Hello', $locale->get('HELLO', 'en-US'));
        $this->assertEquals('Welcome!', $locale->get('PAGE_WELCOME_TILE', 'en-US'));
        $this->assertEquals('This is a paragraph.', $locale->get('PAGE_WELCOME_TEXT', 'en-US'));
        $this->assertNull($locale->get('NOT_EXISTING_KEY', 'en-US'));

        $this->assertEquals('Bonjour', $locale->get('HELLO', 'fr-FR'));
        $this->assertEquals('Bienvenue!', $locale->get('PAGE_WELCOME_TILE', 'fr-FR'));
        $this->assertEquals("Ceci est un paragraphe.", $locale->get('PAGE_WELCOME_TEXT', 'fr-FR'));
        $this->assertNull($locale->get('NOT_EXISTING_KEY', 'fr-FR'));
    }

    public function testAutoloadLocalesWithoutDefault()
    {
        $locale = new Locale();
        $this->assertTrue($locale->registerAutoloader(__DIR__ . '/../_data/locale', ['en-US', 'fr-FR']));

        $this->assertEquals('Hello', $locale->get('HELLO'));
        $this->assertEquals('Welcome!', $locale->get('PAGE_WELCOME_TILE'));
        $this->assertEquals('This is a paragraph.', $locale->get('PAGE_WELCOME_TEXT'));
        $this->assertNull($locale->get('NOT_EXISTING_KEY'));
    }

    public function testAutoloadLocalesWithoutDefaultRevertedOrder()
    {
        $locale = new Locale();
        $this->assertTrue($locale->registerAutoloader(__DIR__ . '/../_data/locale', ['fr-FR', 'en-US']));

        $this->assertEquals('Bonjour', $locale->get('HELLO'));
        $this->assertEquals('Bienvenue!', $locale->get('PAGE_WELCOME_TILE'));
        $this->assertEquals("Ceci est un paragraphe.", $locale->get('PAGE_WELCOME_TEXT'));
    }

    public function testAutoloadLocalesWithDefault()
    {
        $locale = new Locale();
        
        $this->assertTrue($locale->registerAutoloader(__DIR__ . '/../_data/locale', ['en-US', 'fr-FR']));
        $this->assertTrue($locale->setDefault('fr-FR'));

        $this->assertEquals('Bonjour', $locale->get('HELLO'));
        $this->assertEquals('Bienvenue!', $locale->get('PAGE_WELCOME_TILE'));
        $this->assertEquals("Ceci est un paragraphe.", $locale->get('PAGE_WELCOME_TEXT'));
    }

    public function testAutoloadLocalesWithCustomName()
    {
        $locale = new Locale();

        $this->assertTrue($locale->registerAutoloader(__DIR__ . '/../_data/locale', ['en-US', 'fr-FR'], 'app.locale.php'));
        $this->assertTrue($locale->setDefault('fr-FR'));

        $this->assertEquals('Hello', $locale->get('HELLO', 'en-US'));
        $this->assertEquals('Welcome!', $locale->get('PAGE_WELCOME_TILE', 'en-US'));
        $this->assertEquals('This is a paragraph from app.locale.php.', $locale->get('PAGE_WELCOME_TEXT', 'en-US'));
        $this->assertNull($locale->get('NOT_EXISTING_KEY', 'en-US'));

        $this->assertEquals('Bonjour', $locale->get('HELLO', 'fr-FR'));
        $this->assertEquals('Bienvenue!', $locale->get('PAGE_WELCOME_TILE', 'fr-FR'));
        $this->assertEquals('Ceci est un paragraphe extrait de app.locale.php.', $locale->get('PAGE_WELCOME_TEXT', 'fr-FR'));
        $this->assertNull($locale->get('NOT_EXISTING_KEY', 'fr-FR'));

        // test defaults
        $this->assertEquals('Bonjour', $locale->get('HELLO'));
        $this->assertEquals('Bienvenue!', $locale->get('PAGE_WELCOME_TILE'));
        $this->assertEquals("Ceci est un paragraphe extrait de app.locale.php.", $locale->get('PAGE_WELCOME_TEXT'));
        $this->assertNull($locale->get('NOT_EXISTING_KEY'));

    }
 

    public function testAutoloadLocalesWithSections()
    {
        $locale = new Locale();
        $this->assertTrue($locale->registerAutoloader(__DIR__ . '/../_data/locale_with_section', ['en-US', 'fr-FR'], 'locale.php'));
        $this->assertTrue($locale->setDefault('fr-FR'));

        $this->assertFalse($locale->isLoaded('en-US'));
        $this->assertEquals('OK', $locale->getFromSection('BUTTON_OK', 'ui', 'en-US'));
        $this->assertEquals('Cancel', $locale->getFromSection('BUTTON_CANCEL', 'ui', 'en-US'));
        $this->assertNull($locale->getFromSection('NOT_EXISTING_KEY', 'ui', 'en-US'));
        $this->assertTrue($locale->isLoaded('en-US'));
        $this->assertTrue($locale->isLoaded('en-US','ui'));
        $this->assertFalse($locale->isLoaded('en-US','NotExistingSection'));

        $this->assertFalse($locale->isLoaded('fr-FR'));
        $this->assertEquals('OK', $locale->getFromSection('BUTTON_OK', 'ui', 'fr-FR'));
        $this->assertEquals('Annuler', $locale->getFromSection('BUTTON_CANCEL', 'ui', 'fr-FR'));
        $this->assertNull($locale->getFromSection('NOT_EXISTING_KEY', 'ui', 'fr-FR'));
        $this->assertTrue($locale->isLoaded('fr-FR'));
        $this->assertTrue($locale->isLoaded('fr-FR','ui'));

        $this->assertFalse($locale->isLoaded('en-US','section1'));
        $this->assertEquals('Section1 title', $locale->getFromSection('PAGE_TITLE', 'section1', 'en-US'));
        $this->assertEquals('Section2 title', $locale->getFromSection('PAGE_TITLE', 'section2', 'en-US'));
        $this->assertEquals('This is a paragraph from section1.', $locale->getFromSection('PAGE_TEXT', 'section1', 'en-US'));
        $this->assertEquals('This is a paragraph from section2.', $locale->getFromSection('PAGE_TEXT', 'section2', 'en-US'));
        $this->assertTrue($locale->isLoaded('en-US','section1'));

        $this->assertEquals('Titre de la section 1', $locale->getFromSection('PAGE_TITLE', 'section1', 'fr-FR'));
        $this->assertEquals('Titre de la section 2', $locale->getFromSection('PAGE_TITLE', 'section2', 'fr-FR'));
        $this->assertEquals('Ceci est un paragraphe de la section 1.', $locale->getFromSection('PAGE_TEXT', 'section1', 'fr-FR'));
        $this->assertEquals('Ceci est un paragraphe de la section 2.', $locale->getFromSection('PAGE_TEXT', 'section2', 'fr-FR'));

        // test defaults
        $this->assertEquals('Titre de la section 1', $locale->getFromSection('PAGE_TITLE', 'section1'));
        $this->assertEquals('Titre de la section 2', $locale->getFromSection('PAGE_TITLE', 'section2'));
        $this->assertEquals('Ceci est un paragraphe de la section 1.', $locale->getFromSection('PAGE_TEXT', 'section1'));
        $this->assertEquals('Ceci est un paragraphe de la section 2.', $locale->getFromSection('PAGE_TEXT', 'section2'));
    }

    public function testAutoloadIsRegireted()
    {
        $locale = new Locale();
        $locale->registerAutoloader(__DIR__ . '/../_data/locale_with_section', ['en-US', 'fr-FR'], 'locale.php');
        $this->assertTrue($locale->isRegistered('fr-FR'));
        $this->assertFalse($locale->isRegistered('fr-FRRRRRRRRRRR'));
    }

    public function testAutoloadGetWrongSection()
    {
        $locale = new Locale();
        $locale->registerAutoloader(__DIR__ . '/../_data/locale_with_section', ['en-US', 'fr-FR'], 'locale.php');
        $this->assertNull($locale->getFromSection('NOT_EXISTING_KEY', 'NOT_EXISTING_SECTION', 'fr-FR'));
    }

    public function testAutoloadGetWrongLocale()
    {
        $locale = new Locale();
        $locale->registerAutoloader(__DIR__ . '/../_data/locale_with_section', ['en-US', 'fr-FR'], 'locale.php');
        $this->assertNull($locale->getFromSection('PAGE_TEXT', 'section1', 'fr-FRRRRRRRRRRRRRRR'));
    }

    public function testAutoloadaAndRegisterWrongLocale()
    {
        $locale = new Locale();
        $this->assertTrue($locale->registerAutoloader(__DIR__ . '/../_data/locale_with_section', ['en-US', 'fr-FR'], 'locale.php'));
        $this->assertFalse($locale->setDefault('fr-FRRRRRRRRRRRRRRR'));
    }

    public function testAutoloadaWrongPath()
    {
        $locale = new Locale();
        $this->assertFalse($locale->registerAutoloader('/not/extisting/path', ['en-US', 'fr-FR'], 'locale.php'));
    }

    public function testAutoloadaWithoutAnyLocale()
    {
        $locale = new Locale();
        $this->assertFalse($locale->registerAutoloader(__DIR__ . '/../_data/locale_with_section', [], 'locale.php'));
    }

}