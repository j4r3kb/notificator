<?php

namespace App;

use App\Application\Handler\CommandHandlerInterface;
use App\Domain\Service\NotificationChannelInterface;
use App\Infrastructure\Symfony\CompilerPass\NotificationChannelCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->registerForAutoconfiguration(CommandHandlerInterface::class)
            ->addTag(
                'messenger.message_handler',
                [
                    'bus' => 'command.bus',
                ]
            );

        $container->registerForAutoconfiguration(NotificationChannelInterface::class)
            ->addTag('tag.notification-channel');
        $container->addCompilerPass(new NotificationChannelCompilerPass());
    }
}
