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

interface SensitiveDataEventListenerInterface
{
    public function handle(DomainMessage $domainMessage, SensitiveData $data = null);
}
