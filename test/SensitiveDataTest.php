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

class SensitiveDataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function it_exposes_its_properties()
    {
        $data = ['foo' => 'bar'];
        $sensitiveData = new SensitiveData($data);

        $this->assertEquals($data, $sensitiveData->getData());
    }
}
