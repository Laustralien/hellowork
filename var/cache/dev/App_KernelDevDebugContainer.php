<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerUY2cjdi\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerUY2cjdi/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerUY2cjdi.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerUY2cjdi\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerUY2cjdi\App_KernelDevDebugContainer([
    'container.build_hash' => 'UY2cjdi',
    'container.build_id' => '41d1b078',
    'container.build_time' => 1694620239,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerUY2cjdi');