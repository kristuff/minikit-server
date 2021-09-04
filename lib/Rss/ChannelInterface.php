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
 * @version    0.9.11
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Rss;

/**
 * Interface ChannelInterface
 */
interface ChannelInterface
{
    /**
     * Set channel title
     * 
     * @access public
     * @param string    $title
     * @return $this
     */
    public function title($title);

    /**
     * Set channel URL
     * 
     * @param string    $url
     * @return $this
     */
    public function link($url);

    /**
     * Set channel description
     * 
     * @param string    $description
     * @return $this
     */
    public function description($description);

    /**
     * Set ISO639 language code
     *
     * @param string    $language
     * @return $this
     */
    public function language($language);

    /**
     * Set channel copyright
     * 
     * The Copyright notice for content in the channel.
     * 
     * 
     * @param string    $copyright
     * @return $this
     */
    public function copyright($copyright);

    /**
     * Set channel published date
     * 
     * @param int       $pubDate        Unix timestamp
     * @return $this
     */
    public function pubDate($pubDate);

    /**
     * Set channel last build date
     * 
     * @param int       $lastBuildDate  Unix timestamp
     * @return $this
     */
    public function lastBuildDate($lastBuildDate);

    /**
     * Set channel ttl (minutes)
     * 
     * @param int       $ttl
     * @return $this
     */
    public function ttl($ttl);

    /**
     * Add item object
     * 
     * @param ItemInterface $item
     * @return $this
     */
    public function addItem(ItemInterface $item);

    /**
     * Return XML object
     * 
     * @return SimpleXMLElement
     */
    public function asXML();
}
