<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\Container6yyYx3U\srcApp_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/Container6yyYx3U/srcApp_KernelDevDebugContainer.php') {
    touch(__DIR__.'/Container6yyYx3U.legacy');

    return;
}

if (!\class_exists(srcApp_KernelDevDebugContainer::class, false)) {
    \class_alias(\Container6yyYx3U\srcApp_KernelDevDebugContainer::class, srcApp_KernelDevDebugContainer::class, false);
}

return new \Container6yyYx3U\srcApp_KernelDevDebugContainer([
    'container.build_hash' => '6yyYx3U',
    'container.build_id' => '7baa3206',
    'container.build_time' => 1565042728,
], __DIR__.\DIRECTORY_SEPARATOR.'Container6yyYx3U');
