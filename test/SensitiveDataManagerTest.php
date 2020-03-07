<?php

declare(strict_types=1);

/*
 * This file is part of the broadway/sensitive-data package.
 *
 * (c) 2020 Broadway project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Broadway\BroadwaySensitiveData\EventHandling;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;

class SensitiveDataManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function it_publishes_events_and_sensitive_date_to_subscribed_processors()
    {
        $domainMessage = DomainMessage::recordNow(1, 1, new Metadata([]), new \stdClass());
        $sensitiveData = new SensitiveData(['foo' => 'bar']);

        $processor1 = $this->prophesize(SensitiveDataEventListenerInterface::class);
        $processor1
            ->handle($domainMessage, $sensitiveData)
            ->shouldBeCalled();

        $processor2 = $this->prophesize(SensitiveDataEventListenerInterface::class);
        $processor2
            ->handle($domainMessage, $sensitiveData)
            ->shouldBeCalled();

        $manager = new SensitiveDataManager([$processor1->reveal(), $processor2->reveal()]);
        $manager->setSensitiveData($sensitiveData);
        $manager->handle($domainMessage);
    }
}
