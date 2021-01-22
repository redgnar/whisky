<?php

declare(strict_types=1);

namespace Whisky\Builder;

use Whisky\Builder;
use Whisky\Extension;
use Whisky\Normalizer;
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
            $extension->parse($code);
        }
        foreach ($this->extensions as $extension) {
            $code = $extension->normalize($code);
        }
        $doScriptContent = $this->parser->parse($code);
        $className = 'Whisky_'.md5(uniqid('', true));

        return $this->createScript($code, $className, sprintf(
            $this->getClassTemplate(),
            $className,
            $doScriptContent
        ));
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
}
