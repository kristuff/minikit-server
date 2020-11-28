<?php

declare(strict_types=1);

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
 * @version    0.9.2
 * @copyright  2017-2020 Kristuff
 */

namespace Kristuff\Miniweb\Mvc;

use Kristuff\Miniweb\Http\Session;

/**
 * Class Feedback
 *
 */
class Feedback
{
    /** 
     * @access protected
     * @var Http\Session        $session        The Session instance
     */
    protected $session = null;

    /** 
     * @access private
     * @var string 
     */
    private static $FEEDBACK_POSITIVE = '__FEEDBACK_POSITIVE';

    /** 
     * @access private
     * @var  string 
     */
    private static $FEEDBACK_NEGATIVE = '__FEEDBACK_NAGATIVE';

    /**
     * Constructor
     *
     * @access public
	 * @param  Http\Session     $session        The session instance
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Adds a postive feedback message
     *
     * @access public
     * @param  string    $message      The message
     *
     * @return void
     */
    public function addPositive(string $message): void
    {
        $this->session->add(self::$FEEDBACK_POSITIVE, $message);
    }

    /**
     * Adds a postive nagative message
     *
     * @access public
     * @param  string    $message      The message
     *
     * @return void
     */
    public function addNegative(string $message): void
    {
        $this->session->add(self::$FEEDBACK_NEGATIVE, $message);
    }

    /**
     * Gets/returns an array of postive messages
     *
     * @access public
     * @return array
     */
    public function getNegatives(): array
    {
        $messages = $this->session->get(self::$FEEDBACK_NEGATIVE);
        return !empty($messages) ? $messages : [];
    }

    /**
     * Gets/returns an array of nagative messages
     *
     * @access public
     * @return array
     */
    public function getPositives(): array
    {
        $messages = $this->session->get(self::$FEEDBACK_POSITIVE);
        return !empty($messages) ? $messages : [];
    }

    /**
     * Clear all messages
     *
     * @access public
     * @return void
     */
    public function clear(): void
    {
        $this->session->set(self::$FEEDBACK_NEGATIVE, []);
        $this->session->set(self::$FEEDBACK_POSITIVE, []);
    }
}