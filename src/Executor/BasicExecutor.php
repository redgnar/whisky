<?php

namespace Whisky\Executor;

use Whisky\Executor;
use Whisky\RunError;
use Whisky\Scope;
use Whisky\Script;

class BasicExecutor implements Executor
{
    public function execute(Script $script, Scope $scope): mixed
    {
        try {
            return $script->getCodeRunner()($scope);
        } catch (\Throwable $e) {
            throw new RunError($e->getMessage());
        }
    }
}
