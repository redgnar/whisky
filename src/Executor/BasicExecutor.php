<?php

namespace Whisky\Executor;

use Whisky\Executor;
use Whisky\InputError;
use Whisky\RunError;
use Whisky\Scope;
use Whisky\Script;

class BasicExecutor implements Executor
{
    public function execute(Script $script, Scope $variables): mixed
    {
        $notPassedVariables = [];
        foreach ($script->getParseResult()->getInputVariables() as $inputVariable) {
            if (!$variables->has($inputVariable)) {
                $notPassedVariables[] = $inputVariable;
            }
        }
        if (!empty($notPassedVariables)) {
            throw new InputError(sprintf('Script missing input variables: $1%s', implode(', ', $notPassedVariables)));
        }
        try {
            return $script->getCodeRunner()($variables);
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            if (str_contains($message, 'Undefined array key')) {
                $message = str_replace('Undefined array key', 'Undefined variable', $message);
            }

            throw new RunError($message, $e->getCode(), $e);
        }
    }
}
