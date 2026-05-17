<?php

declare(strict_types=1);

/**
 * Livewire 4.x + Orchestra Testbench: the component error bag can be unset/null during
 * Filament Livewire tests. Patch vendor files after install/update (composer.json scripts).
 */
function patchFile(string $path, array $replacements): int
{
    if (! is_file($path) || ! is_readable($path)) {
        return 0;
    }

    $original = file_get_contents($path);
    if ($original === false) {
        fwrite(STDERR, "Could not read {$path}\n");

        exit(1);
    }

    $patched = $original;
    $total = 0;

    foreach ($replacements as [$needle, $replacement]) {
        if (! str_contains($patched, $needle)) {
            continue;
        }

        $patched = str_replace($needle, $replacement, $patched, $count);
        $total += $count;
    }

    if ($total === 0 || $patched === $original) {
        return 0;
    }

    if (file_put_contents($path, $patched) === false) {
        fwrite(STDERR, "Could not write {$path}\n");

        exit(1);
    }

    return $total;
}

$root = dirname(__DIR__);

$livewireSupportValidation = $root.'/vendor/livewire/livewire/src/Features/SupportValidation/SupportValidation.php';
$livewireHandlesValidation = $root.'/vendor/livewire/livewire/src/Features/SupportValidation/HandlesValidation.php';
$livewireSupportTesting = $root.'/vendor/livewire/livewire/src/Features/SupportTesting/SupportTesting.php';

$n = 0;

$n += patchFile($livewireSupportValidation, [
    [
        "\$errors = (new ViewErrorBag)->put('default', \$this->component->getErrorBag());",
        "\$errors = (new ViewErrorBag)->put('default', \$this->component->getErrorBag() ?? new \\Illuminate\\Support\\MessageBag);",
    ],
    [
        '$errors = $this->component->getErrorBag()->toArray();',
        '$errors = ($this->component->getErrorBag() ?? new \\Illuminate\\Support\\MessageBag)->toArray();',
    ],
    [
        <<<'PHP'
        $context->addMemo('errors', collect($errors)
            ->filter(function ($value, $key) {
                return Utils::hasProperty($this->component, $key);
            })
            ->toArray()
        );
PHP
        ,
        <<<'PHP'
        $context->addMemo('errors', collect($errors)
            ->filter(function ($value, $key) {
                $segments = explode('.', (string) $key);
                for ($i = count($segments); $i >= 1; $i--) {
                    if (Utils::hasProperty($this->component, implode('.', array_slice($segments, 0, $i)))) {
                        return true;
                    }
                }

                return false;
            })
            ->toArray()
        );
PHP
        ,
    ],
]);

$n += patchFile($livewireHandlesValidation, [
    [
        <<<'PHP'
    public function getErrorBag()
    {
        if (! store($this)->has('errorBag')) {
            $previouslySharedErrors = app('view')->getShared()['errors'] ?? new ViewErrorBag;
            $this->setErrorBag($previouslySharedErrors->getMessages());
        }

        return store($this)->get('errorBag');
    }
PHP
        ,
        <<<'PHP'
    public function getErrorBag()
    {
        if (! store($this)->has('errorBag')) {
            $previouslySharedErrors = app('view')->getShared()['errors'] ?? new ViewErrorBag;
            $this->setErrorBag($previouslySharedErrors->getMessages());
        }

        $bag = store($this)->get('errorBag');
        if ($bag === null) {
            $this->setErrorBag([]);
            $bag = store($this)->get('errorBag');
        }

        return $bag;
    }
PHP
        ,
    ],
    [
        <<<'PHP'
    public function getErrorBag()
    {
        if (! store($this)->has('errorBag')) {
            $previouslySharedErrors = app('view')->getShared()['errors'] ?? new ViewErrorBag;
            $this->setErrorBag($previouslySharedErrors->getMessages());
        }

        return store($this)->get('errorBag') ?? new MessageBag;
    }
PHP
        ,
        <<<'PHP'
    public function getErrorBag()
    {
        if (! store($this)->has('errorBag')) {
            $previouslySharedErrors = app('view')->getShared()['errors'] ?? new ViewErrorBag;
            $this->setErrorBag($previouslySharedErrors->getMessages());
        }

        $bag = store($this)->get('errorBag');
        if ($bag === null) {
            $this->setErrorBag([]);
            $bag = store($this)->get('errorBag');
        }

        return $bag;
    }
PHP
        ,
    ],
]);

$n += patchFile($livewireSupportTesting, [
    [
        <<<'PHP'
        $errors = $target->getErrorBag();

        if (! $errors->isEmpty()) {
PHP
        ,
        <<<'PHP'
        $errors = $target->getErrorBag() ?? new \Illuminate\Support\MessageBag;

        if (! $errors->isEmpty()) {
PHP
        ,
    ],
]);

if ($n > 0) {
    fwrite(STDOUT, "Applied Livewire testbench patches ({$n} replacement(s)).\n");
}

// Orchestra Testbench keeps compiled Blade under vendor; after Filament (or other) package
// downgrades, stale .php files can reference removed hooks/constants and break Pest.
$testbenchCompiledViews = $root.'/vendor/orchestra/testbench-core/laravel/storage/framework/views';
if (is_dir($testbenchCompiledViews)) {
    $cleared = 0;
    foreach (glob($testbenchCompiledViews.'/*.php') ?: [] as $compiled) {
        if (is_file($compiled) && @unlink($compiled)) {
            $cleared++;
        }
    }
    if ($cleared > 0) {
        fwrite(STDOUT, "Cleared {$cleared} stale Orchestra Testbench compiled view(s).\n");
    }
}
