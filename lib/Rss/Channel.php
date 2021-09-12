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
 * @version    0.9.13
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Rss;

/**
 * Class Channel
 * 
 * The channel element describes the RSS feed, providing such information as its title and description, 
 * and contains items that represent discrete updates to the web content represented by the feed.
 * 
 * This element is REQUIRED and must contain three child elements: 
 *      description, link and title.
 * 
 * The channel may contain each of the following optional elements: 
 *      category, cloud, copyright, docs, generator, image, language, lastBuildDate, managingEditor, 
 *      pubDate, rating, skipDays, skipHours, textInput, ttl and webMaster.
 * 
 * The preceding elements must not be present more than once in a channel, with the exception of category.
 * 
 * The channel also may contain zero or more item elements. The order of elements within the channel must 
 * not be treated as significant.
 * 
 * @see https://www.rssboard.org/rss-profile#element-channel
 */
class Channel implements ChannelInterface
{
    /** @var string */
    protected $title;

    /** @var string */
    protected $link;

    /** @var string */
    protected $description;

    /** @var feedUrl */
    protected $feedUrl;

    /** @var string */
    protected $language;

    /** @var string */
    protected $copyright;

    /** @var int */
    protected $pubDate;

    /** @var int */
    protected $lastBuildDate;

    /** @var int */
    protected $ttl;

    /** @var string[] */
    protected $pubsubhubbub;

    /** @var ItemInterface[] */
    protected $items = [];


    /**
     * Set channel title
     * 
     * The title element holds character data that provides the name of the feed (REQUIRED).
     * It's how people refer to your service. If you have an HTML website that contains the 
     * same information as your RSS file, the title of your channel should be the same as 
     * the title of your website.
     * Example:     Example.com News Headlines
     * 
     * @access public
     * @param string    $title
     * 
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set channel link
     * 
     * The link element identifies the URL of the web site associated with the feed (REQUIRED).
     * Example:     https://www.example.com/
     * 
     * @access public
     * @param string    $url
     * 
     * @return $this
     */
    public function link($url)
    {
        $this->link = $url;
        return $this;
    }

    /**
     * Set channel description
     * 
     * The description element holds character data that provides a human-readable characterization or 
     * summary of the feed (REQUIRED). The description is a phrase or sentence describing the channel.
     * 
     * Example: The latest news from Example.com, a Spartanburg Herald-Journal Web site.
     * 
     * @access public
     * @param string    $description    Phrase or sentence describing the channel.
     * 
     * @return $this
     */
    public function description($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Set URL of this feed
     * 
     * The atom:link element defines a relationship between a web resource (such as a page) and an RSS 
     * channel or item (optional). The most common use is to identify an HTML representation of an entry 
     * in an RSS or Atom feed.
     * The element must have an href attribute that contains the URL of the related resource and may contain 
     * the following attributes:
     *  - The hreflang attribute identifies the language used by the related resource using an HTML language code
     *  - The length attribute contains the resource's size, in bytes
     *  - The title attribute provides a human-readable description of the resource
     *  - The type attribute identifies the resource's MIME media type
     * 
     * @access public
     * @param string    $url
     * 
     * @return $this
     */
    public function feedUrl($url)
    {
        $this->feedUrl = $url;
        return $this;
    }

    /**
     * Set ISO639 language code
     *
     * The language the channel is written in. This allows aggregators to group all
     * Italian language sites, for example, on a single page. A list of allowable
     * values for this element, as provided by Netscape, is here: 
     *  https://www.rssboard.org/rss-language-codes
     * You may also use values defined by the W3C: 
     *  https://www.w3.org/TR/REC-html40/struct/dirlang.html#langcodes.
     *
     * @access public
     * @param string    $language
     * 
     * @return $this
     */
    public function language($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Set channel copyright
     * 
     * @access public
     * @param string    $copyright
     * 
     * @return $this
     */
    public function copyright($copyright)
    {
        $this->copyright = $copyright;
        return $this;
    }

    /**
     * Set channel published date
     * 
     * @access public
     * @param int       $pubDate            Unix timestamp
     * 
     * @return $this
     */
    public function pubDate($pubDate)
    {
        $this->pubDate = $pubDate;
        return $this;
    }

    /**
     * Set channel last build date
     * 
     * @access public
     * @param int       $lastBuildDate      Unix timestamp
     * 
     * @return $this
     */
    public function lastBuildDate($lastBuildDate)
    {
        $this->lastBuildDate = $lastBuildDate;
        return $this;
    }

    /**
     * Set channel ttl (minutes)
     * 
     * @access public
     * @param int       $ttl
     * @return $this
     */
    public function ttl($ttl)
    {
        $this->ttl = $ttl;
        return $this;
    }

    /**
     * Enable PubSubHubbub discovery
     * 
     * @access public
     * @param string    $feedUrl
     * @param string    $hubUrl
     * @return $this
     */
    public function pubsubhubbub($feedUrl, $hubUrl)
    {
        $this->pubsubhubbub = [
            'feedUrl' => $feedUrl,
            'hubUrl' => $hubUrl,
        ];
        return $this;
    }

    /**
     * Add item object
     * 
     * @param ItemInterface     $item
     * @return $this
     */
    public function addItem(ItemInterface $item)
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * Return XML object
     * 
     * @access public
     * @return SimpleXMLElement
     */
    public function asXML()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel></channel>', LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_ERR_FATAL);
        $xml->addChild('title', $this->title);
        $xml->addChild('link', $this->link);
        $xml->addChild('description', $this->description);

        /**
         * The atom:link element defines a relationship between a web resource (such as a page) and an RSS 
         * channel or item (optional). The most common use is to identify an HTML representation of an entry 
         * in an RSS or Atom feed.
         * The element must have an href attribute that contains the URL of the related resource and may contain 
         * the following attributes:
         *  - The hreflang attribute identifies the language used by the related resource using an HTML language code
         *  - The length attribute contains the resource's size, in bytes
         *  - The title attribute provides a human-readable description of the resource
         *  - The type attribute identifies the resource's MIME media type
         * 
         * The element also may contain a rel attribute, which contains a keyword that identifies the nature of the 
         * relationship between the linked resouce and the element. Five relationships are possible:
         * - The value "alternate" describes an alternate representation, such as a web page containing the same content 
         *   as a feed entry
         * - The value "enclosure" describes a a media object such as an audio or video file
         * - The value "related" describes a related resource
         * - The value "self" describes the feed itself
         * - The value "via" describes the original source that authored the entry, when it's not the feed publisher
         */
        if($this->feedUrl !== null) {
            $link = $xml->addChild('atom:link', '', "http://www.w3.org/2005/Atom");
            $link->addAttribute('href',$this->feedUrl);
            $link->addAttribute('type','application/rss+xml');
            $link->addAttribute('rel','self');
        }

        if ($this->language !== null) {
            $xml->addChild('language', $this->language);
        }

        if ($this->copyright !== null) {
            $xml->addChild('copyright', $this->copyright);
        }

        if ($this->pubDate !== null) {
            $xml->addChild('pubDate', date(DATE_RSS, $this->pubDate));
        }

        if ($this->lastBuildDate !== null) {
            $xml->addChild('lastBuildDate', date(DATE_RSS, $this->lastBuildDate));
        }

        if ($this->ttl !== null) {
            $xml->addChild('ttl', $this->ttl);
        }

        //TODO
        if ($this->pubsubhubbub !== null) {
            $feedUrl = $xml->addChild('xmlns:atom:link');
            $feedUrl->addAttribute('rel', 'self');
            $feedUrl->addAttribute('href', $this->pubsubhubbub['feedUrl']);
            $feedUrl->addAttribute('type', 'application/rss+xml');
            
            $hubUrl = $xml->addChild('xmlns:atom:link');
            $hubUrl->addAttribute('rel', 'hub');
            $hubUrl->addAttribute('href', $this->pubsubhubbub['hubUrl']);
        }

        foreach ($this->items as $item) {
            $toDom = dom_import_simplexml($xml);
            $fromDom = dom_import_simplexml($item->asXML());
            $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
        }

        return $xml;
    }
}
