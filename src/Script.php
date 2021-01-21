<?php

declare(strict_types=1);

namespace Whisky;

interface Script
{
    public function getCode(): string;

    public function getRunTimeName(): string;

    public function getRuntimeCode(): string;

    public function getRunTime(): Runtime;
}
