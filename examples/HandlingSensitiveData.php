<?php

/*
 * This file is part of the broadway/sensitive-data package.
 *
 * (c) 2016 Broadway project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Invitation aggregate root.
 */
class Invitation extends Broadway\EventSourcing\EventSourcedAggregateRoot
{
    private $invitationId;

    /**
     * Factory method to create an invitation.
     */
    public static function invite(string $invitationId, string $name)
    {
        $invitation = new Invitation();

        // After instantiation of the object we apply the "InvitedEvent".
        $invitation->apply(new InvitedEvent($invitationId, $name));

        return $invitation;
    }

    /**
     * Every aggregate root will expose its id.
     *
     * {@inheritDoc}
     */
    public function getAggregateRootId(): string
    {
        return $this->invitationId;
    }

    /**
     * The "apply" method of the "InvitedEvent"
     */
    protected function applyInvitedEvent(InvitedEvent $event)
    {
        $this->invitationId = $event->invitationId;
    }
}

/**
 * A repository that will only store and retrieve Invitation aggregate roots.
 *
 * This repository uses the base class provided by the EventSourcing component.
 */
class InvitationRepository extends Broadway\EventSourcing\EventSourcingRepository
{
    public function __construct(Broadway\EventStore\EventStore $eventStore, Broadway\EventHandling\EventBus $eventBus)
    {
        parent::__construct($eventStore, $eventBus, 'Invitation', new Broadway\EventSourcing\AggregateFactory\PublicConstructorAggregateFactory());
    }
}

/*
 * When using CQRS with commands, a lot of times you will find that you have a
 * command object and a "dual" event. Mind though that this is not always the
 * case. The following classes show the commands and events for our small
 * domain model.
 */

/* All commands and events below will cary the id of the aggregate root. For
 * our convenience and readability later on we provide base classes that hold
 * this data.
 */

class InviteCommand
{
    public $invitationId;
    public $name;
    public $password;

    public function __construct($invitationId, $name, $password)
    {
        $this->invitationId = $invitationId;
        $this->name         = $name;
        $this->password     = $password;
    }
}

class InvitedEvent
{
    public $invitationId;
    public $name;

    public function __construct($invitationId, $name)
    {
        $this->invitationId = $invitationId;
        $this->name         = $name;
    }
}

/*
 * A command handler will be registered with the command bus and handle the
 * commands that are dispatched. The command handler can be seen as a small
 * layer between your application code and the actual domain code.
 *
 * In the end a command handler listens for commands and translates commands to
 * method calls on the actual aggregate roots.
 */
class InvitationCommandHandler extends Broadway\CommandHandling\SimpleCommandHandler
{
    private $repository;
    private $sensitiveDataManager;

    public function __construct(
        Broadway\EventSourcing\EventSourcingRepository $repository,
        \Broadway\BroadwaySensitiveData\EventHandling\SensitiveDataManager $sensitiveDataManager
    ) {
        $this->repository           = $repository;
        $this->sensitiveDataManager = $sensitiveDataManager;
    }

    /**
     * A new invite aggregate root is created and added to the repository.
     */
    protected function handleInviteCommand(InviteCommand $command)
    {
        $invitation = Invitation::invite($command->invitationId, $command->name);

        $this->sensitiveDataManager->setSensitiveData(
            new \Broadway\BroadwaySensitiveData\EventHandling\SensitiveData(['password' => $command->password])
        );

        $this->repository->save($invitation);
    }
}
