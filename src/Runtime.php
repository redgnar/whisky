<?php

declare(strict_types=1);

namespace Whisky;

/**
 * @extends \ArrayAccess<string, mixed>
 */
interface Runtime extends \ArrayAccess
{
    public function setScope(Scope $scope): void;

    public function getScope(): Scope;

    public function run(): void;
}
