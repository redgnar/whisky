<?php

namespace Whisky\Test\Scope;

use PHPUnit\Framework\TestCase;
use Whisky\Builder\BasicBuilder;
use Whisky\Extension;
use Whisky\Parser;
use Whisky\Parser\ParseResult;
use Whisky\Provider;
use Whisky\Scope;
use Whisky\Script\BasicScript;

class ExtendedScopeTest extends TestCase
{
    public function testExtended(): void
    {
        $provider = $this->createMock(Provider::class);
        $provider->method('has')->willReturn(true);
        $provider->method('get')->willReturn('foo');
        $scope = new Scope\ExtendedScope();
        $scope->addProvider($provider);

        $this->assertTrue($scope->has('bar'));
        $this->assertEquals('foo', $scope->get('bar'));
    }

}
