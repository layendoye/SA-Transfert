<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerDuZqPNv\srcApp_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerDuZqPNv/srcApp_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerDuZqPNv.legacy');

    return;
}

if (!\class_exists(srcApp_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerDuZqPNv\srcApp_KernelDevDebugContainer::class, srcApp_KernelDevDebugContainer::class, false);
}

return new \ContainerDuZqPNv\srcApp_KernelDevDebugContainer([
    'container.build_hash' => 'DuZqPNv',
    'container.build_id' => 'fc0f4310',
    'container.build_time' => 1565483123,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerDuZqPNv');
