<?php
/*
 * MIT License
 *
 * Copyright (c) 2022 JH Software Engineering, www.jh-se.de
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

declare(strict_types=1);

namespace JhseLabs\MezzioTwigViewHelper\View\Twig;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;
use Laminas\View\HelperPluginManager;
use Twig\Environment;
use Twig\TwigFunction;

class EnvironmentExtensionDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * Returns a Twig\Environment instance that is capable of calling all registered
     * Laminas\View helper classes by bridging twig function calls to Laminas\View\HelperPluginManager
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, ?array $options = null): Environment
    {

        $environment = $callback();
        assert($environment instanceof Environment);

        /** @var HelperPluginManager $helperPluginManager */
        $helperPluginManager = $container->get(HelperPluginManager::class);

        // delegate all undefined twig function calls to getViewHelper method of BridgeExtension
        $environment->registerUndefinedFunctionCallback(
            function ($name) use ($helperPluginManager) {
                if ($helperPluginManager->has($name)) {
                    return new TwigFunction(
                        $name,
                        sprintf('$this->env->getExtension("%s")->getViewHelper("%s")->__invoke', BridgeExtension::class, $name),
                        ['is_safe' => ['all']]
                    );
                }
                return false;
            }
        );

        // register BridgeExtension within twig environment
        $environment->addExtension($container->get(BridgeExtension::class));

        return $environment;
    }
}