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

use Broadway\BroadwaySensitiveData\EventHandling\SensitiveData;
use Broadway\BroadwaySensitiveData\EventHandling\SensitiveDataManager;
use Broadway\BroadwaySensitiveData\EventHandling\SensitiveDataProcessor;
use Broadway\CommandHandling\SimpleCommandBus;
use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\SimpleEventBus;
use Broadway\EventHandling\TraceableEventBus;
use Broadway\EventStore\InMemoryEventStore;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/HandlingSensitiveData.php';

/**
 * This test demonstrates that
 * - sensitive data is not stored in the event stream
 * - sensitive data is available for one-off processing.
 */
class HandlingSensitiveDataTest extends TestCase
{
    private $commandBus;
    private $eventBus;
    private $sensitiveDataProcessor;

    public function setUp(): void
    {
        $this->commandBus = new SimpleCommandBus();
        $this->eventBus = new TraceableEventBus(new SimpleEventBus());

        $this->sensitiveDataProcessor = new MySensitiveDataProcessor();
        $sensitiveDataManager = new SensitiveDataManager([$this->sensitiveDataProcessor]);

        $commandHandler = new InvitationCommandHandler(
            new InvitationRepository(
                new InMemoryEventStore(),
                $this->eventBus
            ),
            $sensitiveDataManager
        );

        $this->commandBus->subscribe($commandHandler);
        $this->eventBus->subscribe($sensitiveDataManager);
    }

    /**
     * @test
     */
    public function it_handles_sensitive_data()
    {
        $this->eventBus->trace();

        $this->commandBus->dispatch(new InviteCommand('1583c029-de76-40ec-8674-de26767617d2', 'asm89', 'p4ssw0rd'));

        // the event should not contain sensitive data
        $this->assertEquals([new InvitedEvent('1583c029-de76-40ec-8674-de26767617d2', 'asm89')], $this->eventBus->getEvents());

        // the sensitive data should be available for the processor
        $this->assertEquals([new SensitiveData(['password' => 'p4ssw0rd'])], $this->sensitiveDataProcessor->getRecordedSensitiveData());
    }
}

class MySensitiveDataProcessor extends SensitiveDataProcessor
{
    private $recordedSensitiveData = [];

    protected function applyInvitedEvent(InvitedEvent $event, DomainMessage $domainMessage, SensitiveData $data = null)
    {
        $this->recordedSensitiveData[] = $data;
    }

    public function getRecordedSensitiveData()
    {
        return $this->recordedSensitiveData;
    }
}
