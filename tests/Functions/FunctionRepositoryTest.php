<?php

namespace Whisky\Test\Functions;

use PHPUnit\Framework\TestCase;
use Whisky\Functions\FunctionRepository;
use Whisky\Functions\Provider;

class FunctionRepositoryTest extends TestCase
{
    public function testExtended(): void
    {
        $provider = $this->createMock(Provider::class);
        $provider->method('has')->willReturn(true);
        $function = function () {return 'foo'; };
        $provider->method('get')->willReturn($function);
        $scope = new FunctionRepository();
        $scope->addProvider($provider);

        $this->assertTrue($scope->has('bar'));
        $this->assertEquals($function, $scope->get('bar'));
    }
}
