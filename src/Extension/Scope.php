<?php

declare(strict_types=1);

namespace Whisky\Extension;

use Whisky\Extension;
use Whisky\Parser\ParseResult;

class Scope implements Extension
{
    public function build(string $code, ParseResult $parseResult, \Whisky\Scope $environment): string
    {
        return $this->assignVariables(
            $code,
            $parseResult->getInputVariables(),
            $parseResult->getOutputVariables()
        );
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
