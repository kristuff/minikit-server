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
 * Class Item
 * 
 * A channel may contain any number of <item>s. An item may represent a "story" -- much like a story in a newspaper 
 * or magazine; if so its description is a synopsis of the story, and the link points to the full story. An item may 
 * also be complete in itself, if so, the description contains the text (entity-encoded HTML is allowed; see examples), 
 * and the link and title may be omitted. All elements of an item are optional, however at least one of title or 
 * description must be present.
 */
class Item implements ItemInterface
{
    /** @var string */
    protected $title;

    /** @var string */
    protected $url;

    /** @var string */
    protected $description;

    /** @var string */
    protected $contentEncoded;

    /** @var array */
    protected $categories = [];

    /** @var string */
    protected $guid;

    /** @var bool */
    protected $isPermalink;

    /** @var int */
    protected $pubDate;

    /** @var array */
    protected $enclosure;

    /** @var string */
    protected $author;

    /** @var string */
    protected $creator;

    /** @var bool */
    protected $preferCdata = false;

    /**
     * Set item title
     * 
     * @param string    $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set item URL
     * 
     * @param string    $url
     * @return $this
     */
    public function url($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Set item description
     * 
     * @param string    $description
     * @return $this
     */
    public function description($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Set content:encoded
     * 
     * @param string    $content
     * @return $this
     */
    public function contentEncoded($content)
    {
        $this->contentEncoded = $content;
        return $this;
    }

    /**
     * Add item category
     * 
     * @param string    $name       Category name
     * @param string    $domain     Category URL
     * @return $this
     */
    public function category($name, $domain = null)
    {
        $this->categories[] = [$name, $domain];
        return $this;
    }

    /**
     * Set GUID
     * 
     * <guid> is an optional sub-element of <item>. guid stands for globally unique identifier. It's a string 
     * that uniquely identifies the item. When present, an aggregator may choose to use this string to determine 
     * if an item is new.
     *  <guid>http://some.server.com/weblogItem3207</guid>
     * 
     * There are no rules for the syntax of a guid. Aggregators must view them as a string. It's up to the source 
     * of the feed to establish the uniqueness of the string.
     * If the guid element has an attribute named isPermaLink with a value of true, the reader may assume that it 
     * is a permalink to the item, that is, a url that can be opened in a Web browser, that points to the full item 
     * described by the <item> element. An example:
     *  <guid isPermaLink="true">http://inessential.com/2002/09/01.php#a2</guid>
     * 
     * isPermaLink is optional, its default value is true. If its value is false, the guid may not be assumed to be 
     * a url, or a url to anything in particular.
     * 
     * @param string    $guid
     * @param bool      $isPermalink
     * @return $this
     */
    public function guid($guid, $isPermalink = false)
    {
        $this->guid = $guid;
        $this->isPermalink = $isPermalink;
        return $this;
    }

    /**
     * Set published date
     * 
     * <pubDate> is an optional sub-element of <item>.
     * Its value is a date, indicating when the item was published. If it's a date in the future, 
     * aggregators may choose to not display the item until that date.
     *  <pubDate>Sun, 19 May 2002 15:21:36 GMT</pubDate>
     * 
     * @param int       $pubDate    Unix timestamp (will be converted later)
     * @return $this
     */
    public function pubDate($pubDate)
    {
        $this->pubDate = $pubDate;
        return $this;
    }

    /**
     * Set enclosure
     * 
     * @param string    $url        Url to media file
     * @param int       $length     Length in bytes of the media file
     * @param string    $type       Media type, default is audio/mpeg
     * @return $this
     */
    public function enclosure($url, $length = 0, $type = 'audio/mpeg')
    {
        $this->enclosure = ['url' => $url, 'length' => $length, 'type' => $type];
        return $this;
    }
    
    /**
     * Set the author (email addresse)
     * 
     * According to the RSS Advisory Board's Best Practices Profile, the recommended format for e-mail 
     * addresses in RSS elements is username@hostname.tld (Real Name), as in the following example:
     *  <managingEditor>luksa@dallas.example.com (Frank Luksa)</managingEditor>
     * 
     * Publishers should use author when they want to reveal an author's e-mail address and dc:creator 
     * when they don't. The same item should not include both elements. So we reset the creator field.
     * 
     * @param string    $author     Email of item author
     * @return $this
     */
    public function author($author)
    {
        $this->creator = null;
        $this->author = $author;
        return $this;
    }

    /**
     * Set dc:creator
     * 
     * The dc:creator element identifies the person or entity who wrote an item (optional). An item may 
     * contain more than one dc:creator element to credit multiple authors.
     * 
     * The creator can be identified using a real name, username or some other means of identification 
     * at the publisher's discretion.
     * <dc:creator>Joe Bob Briggs</dc:creator>
     * 
     * The value of the dc:creator element is less restrictive than the author element, which must contain 
     * an e-mail address. Publishers often rely on dc:creator to credit authorship without revealing e-mail 
     * addresses in a form that can be exploited by spammers.
     * 
     * All of the tested aggregators that display item authors support both the author and dc:creator 
     * elements. (BottomFeeder, Mozilla Firefox 2.0 and My Yahoo do not identify authors.). 
     * 
     * When an item contains both elements, aggregators handle it in different ways. Some take the first 
     * element that appears within the item, others take the last and one aggregator combines their values.
     * 
     * Publishers should use author when they want to reveal an author's e-mail address and dc:creator when 
     * they don't. The same item should not include both elements. So reset the author field.
     * 
     * @param string    $creator     
     * @return $this
     */
    public function creator($creator)
    {
        $this->author = null;
        $this->creator = $creator;
        return $this;
    }

    /**
     * If true, title and description become CDATA wrapped HTML.
     * 
     * @param ChannelInterface  $channel
     * @return $this
     */
    public function preferCdata($preferCdata)
    {
        $this->preferCdata = (bool)$preferCdata;
        return $this;
    }

    /**
     * Append item to the channel
     * 
     * @param ChannelInterface  $channel
     * @return $this
     */
    public function appendTo(ChannelInterface $channel)
    {
        $channel->addItem($this);
        return $this;
    }

    /**
     * 
     * @return SimpleXMLElement
     */
    public function asXML()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><item></item>', LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_ERR_FATAL);

        if ($this->preferCdata) {
            $xml->addCdataChild('title', $this->title);
        } else {
            $xml->addChild('title', $this->title);
        }

        $xml->addChild('link', $this->url);

        if ($this->preferCdata) {
            $xml->addCdataChild('description', $this->description);
        } else {
            $xml->addChild('description', $this->description);
        }

        if ($this->contentEncoded) {
            $xml->addCdataChild('xmlns:content:encoded', $this->contentEncoded);
        }

        foreach ($this->categories as $category) {
            $element = $xml->addChild('category', $category[0]);

            if (isset($category[1])) {
                $element->addAttribute('domain', $category[1]);
            }
        }

        if ($this->guid) {
            $guid = $xml->addChild('guid', $this->guid);

            if ($this->isPermalink === false) {
                $guid->addAttribute('isPermaLink', 'false');
            }
        }

        if ($this->pubDate !== null) {
            $xml->addChild('pubDate', date(DATE_RSS, $this->pubDate));
        }

        if (is_array($this->enclosure) && (count($this->enclosure) == 3)) {
            $element = $xml->addChild('enclosure');
            $element->addAttribute('url', $this->enclosure['url']);
            $element->addAttribute('type', $this->enclosure['type']);

            if ($this->enclosure['length']) {
                $element->addAttribute('length', $this->enclosure['length']);
            }
        }

        if (!empty($this->author)) {
            $xml->addChild('author', $this->author);
        }
        
        if (!empty($this->creator)) {
            $xml->addChild('dc:creator', $this->creator, "http://purl.org/dc/elements/1.1/");
        }

        return $xml;
    }
}
