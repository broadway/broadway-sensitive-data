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

abstract class SensitiveDataProcessor implements SensitiveDataEventListenerInterface
{
    public function handle(DomainMessage $domainMessage, SensitiveData $data = null)
    {
        $event = $domainMessage->getPayload();
        $method = $this->getApplyMethod($event);

        if (!method_exists($this, $method)) {
            return;
        }

        $this->$method($event, $domainMessage, $data);
    }

    private function getApplyMethod($event)
    {
        $classParts = explode('\\', get_class($event));

        return 'apply'.end($classParts);
    }
}
