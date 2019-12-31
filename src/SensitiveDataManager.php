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
use Broadway\EventHandling\EventListener;

class SensitiveDataManager implements EventListener
{
    private $sensitiveData;
    private $sensitiveDataProcessors = [];

    /**
     * @param SensitiveDataEventListenerInterface[] $sensitiveDataProcessors
     */
    public function __construct(array $sensitiveDataProcessors = array())
    {
        foreach ($sensitiveDataProcessors as $sensitiveDataProcessor) {
            $this->subscribe($sensitiveDataProcessor);
        }
    }

    private function subscribe(SensitiveDataEventListenerInterface $sensitiveDataProcessor)
    {
        $this->sensitiveDataProcessors[] = $sensitiveDataProcessor;
    }

    public function handle(DomainMessage $domainMessage): void
    {
        foreach ($this->sensitiveDataProcessors as $processor) {
            $processor->handle($domainMessage, $this->sensitiveData);
        }
    }

    public function setSensitiveData(SensitiveData $data)
    {
        $this->sensitiveData = $data;
    }
}
