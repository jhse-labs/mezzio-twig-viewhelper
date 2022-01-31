# mezzio-twig-viewhelper

Within mezzio applications, this module bridges the classic laminas view helpers to the twig environment enabled by [mezzio-twigrenderer](https://github.com/mezzio/mezzio-twigrenderer).
You can use all laminas view helpers (e.g. for rendering [laminas-form](https://github.com/laminas/laminas-form) components) seamlessly from you twig templates.

This is inspired by kokspflanze/zfc-twig, the module that achieved this functionality for laminas-mvc.  

Installation
------------

Install the library using [composer](https://getcomposer.org):

    composer require jhse-labs/mezzio-twig-viewhelper

Enable the module in config.php:

```php
<?php

$aggregator = new ConfigAggregator([
    \JhseLabs\MezzioTwigViewHelper\ConfigProvider::class,
    ...
```
And register the EnvironmentExtensionFactory in your local ConfigProvider:

```php
<?php

    public function getDependencies(): array
    {
        return [
            'factories' => [
                ...
                \Twig\Environment::class => \JhseLabs\MezzioTwigViewHelper\View\Twig\EnvironmentExtensionFactory::class,
                ...
            ],
        ];
    }
```

Usage
-----

Within your Twig templates you can now call all your laminas view helpers:
```html
<head>
{{ headTitle('Login Page') }}
</head>
<body>
{{ form(loginForm) }}
</body>
```