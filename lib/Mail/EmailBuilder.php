<?php

/** 
 *        _      _            _
 *  _ __ (_)_ _ (_)_ __ _____| |__
 * | '  \| | ' \| \ V  V / -_) '_ \
 * |_|_|_|_|_||_|_|\_/\_/\___|_.__/
 *
 * This file is part of Kristuff\MiniWeb.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @version    0.9.15
 * @copyright  2017-2021 Kristuff
 */
    
namespace Kristuff\Miniweb\Mail;

use Kristuff\Phtemail\HtmlEmailBuilder;
use Kristuff\Phtemail\HtmlElements;

/** 
 *
 */
class EmailBuilder
{

    /**
     *
     * @access public
     * @static
	 *
	 * @return HtmlEmailBuilder
	 */
    public static function getEmailBuilder()
    {
        $builder = new HtmlEmailBuilder();
        $builder->setEmailBodyWidth(560); 
        
        // -----------
        // header part
        // -----------
        $builder->header()->setFontSize('14px'); 
        $builder->header()->setPadding(15); 
        $builder->header()->add(new HtmlElements\BreakLine);                                                     

        // -----------
        // footer part
        // -----------
        $builder->footer()->setPadding(20); 
        $builder->footer()->setFontSize('13px'); 
        $divFooter = new HtmlElements\Div();
        $divFooter->add(new HtmlElements\Span('Miniweb | Copyright © 2017-2021 '));
        $divFooter->add(new HtmlElements\Link('Kristuff', 'https://kristuff.fr/', 'Kristuff'));
        $builder->footer()->add($divFooter);

        return $builder;
    }
    
    /**
     *
     * @access public
     * @static
	 *
	 * @return void
	 */
    public static function createHeaderWithLogo(HtmlEmailBuilder $builder, string $title, string $subtitle, string $logoUrl)
    {
        // colored header
        $emailHeader = new HtmlElements\RowTwoColumns();
        $emailHeader->setBackground(HtmlEmailBuilder::COLOR_GRAY_800); 
        $emailHeader->leftColumn()->setColumnWidth(90); 
        $emailHeader->leftColumn()->add(new HtmlElements\Image($logoUrl, 70,'logo', 'logo'));
        $emailHeader->rightColumn()->setColumnWidth(410);
        $emailHeader->rightColumn()->add(new HtmlElements\Heading1($title, ['color' => "#FFFFFF"]));
        $emailHeader->rightColumn()->add(new HtmlElements\Heading5($subtitle, ['color' => "#FFFFFF"]));
        $builder->body()->add($emailHeader);
    }

    /**
     *
     * @access public
     * @static
	 *
	 * @return void
	 */
    public static function createHeader(HtmlEmailBuilder $builder, string $title, string $subtitle)
    {
        // colored header
        $emailHeader = new HtmlElements\Row();
        $emailHeader->setBackground(HtmlEmailBuilder::COLOR_GRAY_800); 
        $emailHeader->add(new HtmlElements\Heading1($title, ['color' => "#FFFFFF"]));
        $emailHeader->add(new HtmlElements\Heading5($subtitle, ['color' => "#FFFFFF"]));
        $builder->body()->add($emailHeader);
    }

    /**
     *
     * @access public
     * @static
	 *
	 * @return void
	 */
    public static function createContent(HtmlEmailBuilder $builder, array $lines)
    {
        $content = new HtmlElements\Row();
        foreach ($lines as $paragraph){
            $content->add(new HtmlElements\Paragraph($paragraph));
            $content->add(new HtmlElements\BreakLine());
        }
        $builder->body()->add($content);
    }

    /**
     *
     * @access public
     * @static
	 *
	 * @return void
	 */
    public static function createButton(HtmlEmailBuilder $builder, string $text, string $url)
    {
        $rowButton = new HtmlElements\RowButton($text, $url);
        $rowButton->removePaddingTop(); // no need extra padding before previous element
        $rowButton->setButtonBackground(HtmlEmailBuilder::COLOR_BLUE); 
        $rowButton->setButtonColor(HtmlEmailBuilder::COLOR_WHITE); 
        $builder->body()->add($rowButton);
    }

    /**
     *
     * @access public
     * @static
	 *
	 * @return void
	 */
    public static function createFooter(HtmlEmailBuilder $builder, string $text1, string $text2)
    {
        // our footer
        $footer = new HtmlElements\Row();
        $footer->setBackground(HtmlEmailBuilder::COLOR_GRAY_900);
        $footer->setAlign(HtmlEmailBuilder::H_ALIGN_CENTER);
        $footer->setColor(HtmlEmailBuilder::COLOR_GRAY_500);
        $footer->add(new HtmlElements\Paragraph($text1, ['font-size' => '20px', 'font-weight' => '200!important']));
        $footer->add(new HtmlElements\Paragraph($text2, ['font-size' => '13px']));
        $builder->body()->add($footer);
    }
}