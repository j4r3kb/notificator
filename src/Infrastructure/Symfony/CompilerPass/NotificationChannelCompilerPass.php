<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\CompilerPass;

use App\Domain\Service\MultiChannelNotificationSender;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class NotificationChannelCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(MultiChannelNotificationSender::class)) {
            return;
        }

        $multiChannelSender = $container->findDefinition(MultiChannelNotificationSender::class);

        foreach ($container->findTaggedServiceIds('tag.notification-channel') as $id => $tags) {
            $multiChannelSender->addMethodCall('addNotificationChannel', [new Reference($id)]);
        }
    }
}
