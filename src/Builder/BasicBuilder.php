<?php

declare(strict_types=1);

namespace Whisky\Builder;

use Whisky\Builder;
use Whisky\Extension;
use Whisky\Parser;
use Whisky\Script;
use Whisky\Script\BasicScript;

class BasicBuilder implements Builder
{
    private Parser $parser;
    /**
     * @var Extension[]
     */
    private array $extensions = [];

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function build(string $code): Script
    {
        foreach ($this->extensions as $extension) {
            $code = $extension->transformCode($code);
        }
        $parseResult = $this->parser->parse($code);
        foreach ($this->extensions as $extension) {
            $extension->secure($parseResult);
        }

        return $this->createScript(
            $code,
            eval(sprintf(
                $this->getCodeRunnerTemplate(),
                $this->assignVariables(
                    $parseResult->getParsedCode(),
                    $parseResult->getInputVariables(),
                    $parseResult->getOutputVariables()
                )
            ))
        );
    }

    public function addExtension(Extension $extension): void
    {
        $this->extensions[] = $extension;
    }

    protected function createScript(string $code, \Closure $codeRunner): Script
    {
        return new BasicScript($code, $codeRunner);
    }

    protected function getCodeRunnerTemplate(): string
    {
        return <<<'EOD'
return function(\Whisky\Scope $scope): void {
%s
};
EOD;
    }

    /**
     * @param array<string, mixed> $inputVariables
     * @param array<string, mixed> $outputVariables
     */
    protected function assignVariables(string $code, array $inputVariables, array $outputVariables): string
    {
        $resultCode = $code;
        $preCode = '';
        if (!empty($inputVariables)) {
            foreach ($inputVariables as $variable) {
                $preCode .= '$'.$variable.'=$scope->get(\''.$variable.'\');';
            }
            $resultCode = $preCode."\n".$resultCode;
        }
        $postCode = '';
        if (!empty($outputVariables)) {
            foreach ($outputVariables as $variable) {
                $postCode .= '$scope->set(\''.$variable.'\', $'.$variable.');';
            }
            $resultCode .= "\n".$postCode;
        }

        return $resultCode;
    }
}
