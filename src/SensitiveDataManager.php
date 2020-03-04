<?php

declare(strict_types=1);

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
use Broadway\EventHandling\EventListener;

class SensitiveDataManager implements EventListener
{
    /**
     * @var SensitiveData
     */
    private $sensitiveData;

    /**
     * @var SensitiveDataEventListenerInterface[]
     */
    private $sensitiveDataProcessors = [];

    /**
     * @param SensitiveDataEventListenerInterface[] $sensitiveDataProcessors
     */
    public function __construct(array $sensitiveDataProcessors = [])
    {
        foreach ($sensitiveDataProcessors as $sensitiveDataProcessor) {
            $this->subscribe($sensitiveDataProcessor);
        }
    }

    private function subscribe(SensitiveDataEventListenerInterface $sensitiveDataProcessor): void
    {
        $this->sensitiveDataProcessors[] = $sensitiveDataProcessor;
    }

    public function handle(DomainMessage $domainMessage): void
    {
        foreach ($this->sensitiveDataProcessors as $processor) {
            $processor->handle($domainMessage, $this->sensitiveData);
        }
    }

    public function setSensitiveData(SensitiveData $data): void
    {
        $this->sensitiveData = $data;
    }
}
