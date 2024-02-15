<?php

namespace Whisky\Executor;

use Whisky\Executor;
use Whisky\Functions\FunctionRepository;
use Whisky\InputError;
use Whisky\RunError;
use Whisky\Scope;
use Whisky\Script;

class BasicExecutor implements Executor
{
    public function __construct(
        private readonly FunctionRepository $functionRepository
    ) {
    }

    public function execute(Script $script, Scope $variables): mixed
    {
        $notPassedVariables = [];
        foreach ($script->getParseResult()->getInputVariables() as $inputVariable) {
            if (!$variables->has($inputVariable)) {
                $notPassedVariables[] = $inputVariable;
            }
        }
        if (!empty($notPassedVariables)) {
            throw new InputError(sprintf('Script missing input variables: %1$s', implode(', ', $notPassedVariables)));
        }

        $executable = $this->compile($script);

        try {
            return $executable($variables, $this->functionRepository);
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            if (str_contains($message, 'Undefined array key')) {
                $message = str_replace('Undefined array key', 'Undefined variable', $message);
            }

            throw new RunError($message, $e->getCode(), $e);
        }
    }

    protected function compile(Script $script): \Closure
    {
        $executable = eval(sprintf(
            $this->getCodeRunnerTemplate(),
            $script->getResultCode(),
        ));

        if (!($executable instanceof \Closure)) {
            throw new RunError('Compiled code is not executable function');
        }

        return $executable;
    }

    protected function getCodeRunnerTemplate(): string
    {
        return <<<'EOD'
return function(\Whisky\Scope $variables, \Whisky\Functions\FunctionRepository $functions) {
%s
};
EOD;
    }
}
