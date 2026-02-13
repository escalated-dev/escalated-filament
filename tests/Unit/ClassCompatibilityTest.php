<?php

it('can load all source classes without fatal errors', function () {
    $srcDir = dirname(__DIR__, 2) . '/src';
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($srcDir)
    );

    $classes = [];
    foreach ($files as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }

        $content = file_get_contents($file->getPathname());

        if (preg_match('/namespace\s+(.+?);/', $content, $ns) &&
            preg_match('/class\s+(\w+)/', $content, $cl)) {
            $classes[] = $ns[1] . '\\' . $cl[1];
        }
    }

    expect($classes)->not->toBeEmpty();

    foreach ($classes as $class) {
        // This will trigger a fatal error if the class can't be loaded
        $reflection = new ReflectionClass($class);
        expect($reflection->getName())->toBe($class);
    }
});
