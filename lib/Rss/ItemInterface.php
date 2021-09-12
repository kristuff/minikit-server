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
 * Interface ItemInterface
 */
interface ItemInterface
{
    /**
     * Set item title
     * 
     * @param string    $title
     * @return $this
     */
    public function title($title);

    /**
     * Set item URL
     * 
     * @param string    $url
     * @return $this
     */
    public function url($url);

    /**
     * Set item description
     * 
     * @param string    $description
     * @return $this
     */
    public function description($description);

    /**
     * Set content:encoded
     * 
     * @param string    $content
     * @return $this
     */
    public function contentEncoded($content);

    /**
     * Set item category
     * 
     * @param string    $name       Category name
     * @param string    $domain     Category URL
     * @return $this
     */
    public function category($name, $domain = null);

    /**
     * Set GUID
     * 
     * @param string    $guid
     * @param bool      $isPermalink
     * @return $this
     */
    public function guid($guid, $isPermalink = false);

    /**
     * Set published date
     * 
     * @param int       $pubDate    Unix timestamp
     * @return $this
     */
    public function pubDate($pubDate);

    /**
     * Set enclosure
     * 
     * @param string    $url        Url to media file
     * @param int       $length     Length in bytes of the media file
     * @param string    $type       Media type, default is audio/mpeg
     * @return $this
     */
    public function enclosure($url, $length = 0, $type = 'audio/mpeg');

    /**
     * Set the author
     * 
     * @param string    $author     Email of item author
     * @return $this
     */
    public function author($author);

    /**
     * Append item to the channel
     * 
     * @param ChannelInterface  $channel
     * @return $this
     */
    public function appendTo(ChannelInterface $channel);

    /**
     * Return XML object
     * 
     * @return SimpleXMLElement
     */
    public function asXML();
}
