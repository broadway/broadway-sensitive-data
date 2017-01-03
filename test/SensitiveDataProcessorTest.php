<?php

/*
 * This file is part of the broadway/sensitive-data package.
 *
 * (c) 2016 Broadway project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Broadway\BroadwaySensitiveData\EventHandling;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit_Framework_TestCase;

class SensitiveDataProcessorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_passes_the_event_and_domain_message_and_sensitive_data()
    {
        $testProcessor = new TestProcessor();
        $testEvent     = new TestEvent();

        $this->assertFalse($testProcessor->isCalled());

        $testProcessor->handle($this->createDomainMessage($testEvent), new SensitiveData(['foo' => 'bar']));

        $this->assertTrue($testProcessor->isCalled());
    }

    private function createDomainMessage($event)
    {
        return DomainMessage::recordNow(1, 1, new Metadata([]), $event);
    }
}

class TestProcessor extends SensitiveDataProcessor
{
    private $isCalled = false;

    public function applyTestEvent($event, DomainMessage $domainMessage, SensitiveData $sensitiveData)
    {
        $this->isCalled = true;
    }

    public function isCalled()
    {
        return $this->isCalled;
    }
}

class TestEvent
{
}
