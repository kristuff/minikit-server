<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.22 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Rss;

/**
 * Class SimpleXMLElement
 */
class SimpleXMLElement extends \SimpleXMLElement
{
    /**
     * @param string    $name
     * @param string    $value
     * @param string    $namespace
     * 
     * @return \SimpleXMLElement
     */
    public function addChild($name, $value = null, $namespace = null)
    {
        if ($value !== null and is_string($value) === true) {
            $value = str_replace('&', '&amp;', $value);
        }

        return parent::addChild($name, $value, $namespace);
    }

    /**
     * @param string    $name
     * @param string    $value
     * @param string    $namespace
     * 
     * @return \SimpleXMLElement
     */
    public function addCdataChild($name, $value = null, $namespace = null)
    {
        $element = $this->addChild($name, null, $namespace);
        $dom = dom_import_simplexml($element);
        $elementOwner = $dom->ownerDocument;
        $dom->appendChild($elementOwner->createCDATASection($value));
        return $element;
    }
}
