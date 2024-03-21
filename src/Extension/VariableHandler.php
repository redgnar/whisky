<?php

declare(strict_types=1);

namespace Whisky\Extension;

use Whisky\Extension;
use Whisky\Parser\ParseResult;

class VariableHandler implements Extension
{
    use NotAllowedWord;

    private const NOT_ALLOWED_WORDS = [
        '$variables',
    ];

    public function parse(string $code): string
    {
        $codeWithoutStrings = $this->clearCodeFromStringsAndComments($code);
        foreach (self::NOT_ALLOWED_WORDS as $notAllowedWord) {
            $this->isWordAllowed($notAllowedWord, $codeWithoutStrings);
        }

        return $code;
    }

    public function build(string $code, ParseResult $parseResult): string
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
        $preCode = $this->buildPreCode($inputVariables, $outputVariables);
        $postCode = $this->buildPostCode($outputVariables);
        $newCode = $postCode ? preg_replace('/(\s*)return([\s\;])/', '${1}'.$postCode.'return${2}', $code) : $code;

        return $preCode."\n".$newCode."\n".$postCode;
    }

    /**
     * @param array<string, mixed> $inputVariables
     * @param array<string, mixed> $outputVariables
     */
    private function buildPreCode(array $inputVariables, array $outputVariables): string
    {
        $preCode = '';
        if (!empty($inputVariables)) {
            foreach ($inputVariables as $variable) {
                $preCode .= '$'.$variable.'=$variables->get(\''.$variable.'\');';
            }
        }
        if (!empty($outputVariables)) {
            foreach ($outputVariables as $variable) {
                $preCode .= 'if($variables->has(\''.$variable.'\'))$'.$variable.'=$variables->get(\''.$variable.'\');';
            }
        }

        return $preCode;
    }

    /**
     * @param array<string, mixed> $outputVariables
     */
    private function buildPostCode(array $outputVariables): string
    {
        $postCode = '';
        if (!empty($outputVariables)) {
            foreach ($outputVariables as $variable) {
                $postCode .= 'if(isset($'.$variable.'))$variables->set(\''.$variable.'\', $'.$variable.');';
            }
        }

        return $postCode;
    }
}
