<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\Container5ks4M38\srcApp_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/Container5ks4M38/srcApp_KernelDevDebugContainer.php') {
    touch(__DIR__.'/Container5ks4M38.legacy');

    return;
}

if (!\class_exists(srcApp_KernelDevDebugContainer::class, false)) {
    \class_alias(\Container5ks4M38\srcApp_KernelDevDebugContainer::class, srcApp_KernelDevDebugContainer::class, false);
}

return new \Container5ks4M38\srcApp_KernelDevDebugContainer([
    'container.build_hash' => '5ks4M38',
    'container.build_id' => '9633d406',
    'container.build_time' => 1568043706,
], __DIR__.\DIRECTORY_SEPARATOR.'Container5ks4M38');
