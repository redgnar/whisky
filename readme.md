# redgnar/whisky

[![Quality](https://github.com/redgnar/whisky/actions/workflows/quality.yml/badge.svg?branch=1.0)](https://github.com/redgnar/whisky/actions/workflows/quality.yml)

The Whisky The library is designed for the safe execution of PHP scripts, with the ability to set input variables and read output variables. The library allows for defining custom functions as well as security rules.

## Installation

Use composer to install the whisky library.

`composer require redgnar/whisky`

## Usage

After installation, you can include the whisky library in your PHP scripts using composer auto-load.

`require_once 'vendor/autoload.php';`

Now, you can use the functionalities provided by the whisky library.

```php
use PhpParser\ParserFactory;
...

$functionProvider = new FunctionProvider();
$builder = new BasicBuilder(
    new PhpParser((new ParserFactory())->create(ParserFactory::ONLY_PHP7))
);
$builder->addExtension(new BasicSecurity());
$builder->addExtension(new VariableHandler());
$builder->addExtension($functionProvider);
$executor = new BasicExecutor();
$variables = new BasicScope(['collection' => ['a', 'b']]);
$functionProvider->addFunction('testIt', function (string $text) {return $text; });
$script = $builder->build(
            <<<'EOD'
    $result = [];
    foreach ($collection as $item) {
        if ("b" === $item) {
            continue;
        }
        $result[] = testIt($item."aaa4bbb");
    }
EOD
);
$this->executor->execute($script, $variables);
var_dump($variables->get('result'));
```

## Contributing

We welcome contributions from the community. Please read our [CONTRIBUTING.md](./CONTRIBUTING.md) (link to your contribution guidelines) for details on how to contribute to this project.

## License

This library is released under an open-source license. For more information, please see the [LICENSE](./LICENSE) file in the repository.

## Support

For any questions or issues, you may [open an issue](Link to issue page) on our GitHub repository.

## Acknowledgements

Thank you to all the contributors who have helped in developing this library.