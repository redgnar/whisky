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

        $className = 'Whisky_'.md5(uniqid('', true));

        return $this->createScript(
            $code,
            $className,
            sprintf(
                $this->getClassTemplate(),
                $className,
                $this->assignVariables(
                    $parseResult->getParsedCode(),
                    $parseResult->getInputVariables(),
                    $parseResult->getOutputVariables()
                )
            )
        );
    }

    public function addExtension(Extension $extension): void
    {
        $this->extensions[] = $extension;
    }

    protected function createScript(string $code, string $className, string $classContent): Script
    {
        return new BasicScript($code, $className, $classContent);
    }

    protected function getClassTemplate(): string
    {
        return <<<'EOD'
class %s extends \Whisky\Runtime\BasicRuntime {
    public function run(): void {
    %s
    }
}
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
                $preCode .= '$'.$variable.'=$this[\''.$variable.'\'] ?? null;';
            }
            $resultCode = $preCode."\n".$resultCode;
        }
        $postCode = '';
        if (!empty($outputVariables)) {
            foreach ($outputVariables as $variable) {
                $postCode .= '$this[\''.$variable.'\']=$'.$variable.';';
            }
            $resultCode .= "\n".$postCode;
        }

        return $resultCode;
    }
}
