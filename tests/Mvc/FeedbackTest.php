<?php

namespace Kristuff\Miniweb\Tests\Mvc;
require_once __DIR__.'/../_data/mvc_app/model/DummyFeedbackModel.php';

use Kristuff\Miniweb\Mvc\Feedback;
use Kristuff\Miniweb\Http\Session;

class FeedbackTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testFeedback()
    {
        session_start();

        $session = new Session();
        $feedback = new Feedback($session);

        $this->assertInternalType('array', $feedback->getPositives());
        $this->assertInternalType('array', $feedback->getNegatives());
        $this->assertEquals(0, count($feedback->getPositives()));
        $this->assertEquals(0, count($feedback->getNegatives()));

        $feedback->addPositive('hello');
        $feedback->addPositive('hello again');

        $feedback->addNegative('something is wrong');
        $feedback->addNegative('something is wrong again');

        $this->assertEquals(2, count($feedback->getPositives()));
        $this->assertEquals(2, count($feedback->getNegatives()));

        $this->assertEquals('hello',                    $feedback->getPositives()[0]);
        $this->assertEquals('hello again',              $feedback->getPositives()[1]);
        $this->assertEquals('something is wrong',       $feedback->getNegatives()[0]);
        $this->assertEquals('something is wrong again', $feedback->getNegatives()[1]);

        $feedback->clear();
        $this->assertInternalType('array', $feedback->getPositives());
        $this->assertInternalType('array', $feedback->getNegatives());
        $this->assertEquals(0, count($feedback->getPositives()));
        $this->assertEquals(0, count($feedback->getNegatives()));
    }

    /**
     * @runInSeparateProcess
     */
    public function testFeedbackMessagesInModel()
    {
        session_start();

        // dup object for tesing
        $session = new Session();
        $feedback = new Feedback($session);

        \DummyFeedbackModel::registerSomeMessages();

        $this->assertEquals(2,       count($feedback->getPositives()));
        $this->assertEquals('hello',       $feedback->getPositives()[0]);
        $this->assertEquals('hello again', $feedback->getPositives()[1]);
    }

     /**
     * @runInSeparateProcess
     */
    public function testFeedbackMessagesInModel_direct()
    {
        session_start();

        // dup object for tesing
        $session = new Session();
        $feedback = new Feedback($session);

        \DummyFeedbackModel::registerSomeMessages_direct();

        $this->assertEquals(2,       count($feedback->getPositives()));
        $this->assertEquals('hello direct',       $feedback->getPositives()[0]);
        $this->assertEquals('hello direct again', $feedback->getPositives()[1]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testFeedbackErrorsInModel()
    {
        session_start();

        // dup object for tesing
        $session = new Session();
        $feedback = new Feedback($session);

        \DummyFeedbackModel::registerSomeErrors();

        $this->assertEquals(2,                    count($feedback->getNegatives()));
        $this->assertEquals('something is wrong',       $feedback->getNegatives()[0]);
        $this->assertEquals('something is wrong again', $feedback->getNegatives()[1]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testFeedbackErrorsInModel_direct()
    {
        session_start();

        // dup object for tesing
        $session = new Session();
        $feedback = new Feedback($session);

        \DummyFeedbackModel::registerSomeErrors_direct();

        $this->assertEquals(2,                    count($feedback->getNegatives()));
        $this->assertEquals('something is wrong direct',       $feedback->getNegatives()[0]);
        $this->assertEquals('something is wrong direct again', $feedback->getNegatives()[1]);
    }

}