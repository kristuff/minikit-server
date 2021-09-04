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
 * @version    0.9.12
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Rss;

use DOMDocument;

/**
 * Class FeedCreator
 * 
 * Use to generate a rss Feed following RSS 2.0 specs. 
 * 
 * The rss element is the top-level element of an RSS feed. A feed that conforms to the RSS specification must contain 
 * a version attribute with the value "2.0". 
 *      <rss version="2.0">
 * 
 * This element is required and must contain a channel element. The rss element must not contain more than one channel.
 * 
 * The Atom syndication format, which serves a similar purpose to RSS, offers some elements closely comparable to RSS 
 * elements and others that provide new capabilities. Any of these elements can be used in RSS by employing Atom as a 
 * namespace. This namespace requires the "http://www.w3.org/2005/Atom" declaration in the rss element. 
 *      <rss xmlns:atom="http://www.w3.org/2005/Atom">
 */
class FeedCreator
{
    /** @var ChannelInterface */
    protected $channel;

    /**
     * Get the channel object attached to current feed
     * 
     * @return Channel
     */
    public function channel()
    {
        if (!isset($this->channel)){
            $this->channel = new Channel();
        }
        return $this->channel;
    }

    /**
     * Render XML
     * 
     * @return string
     */
    public function render()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom" />', LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_ERR_FATAL);

        $toDom = dom_import_simplexml($xml);
        $fromDom = dom_import_simplexml($this->channel->asXML());
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($dom->importNode(dom_import_simplexml($xml), true));
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    /**
     * Render XML
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
