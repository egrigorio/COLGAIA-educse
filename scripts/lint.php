<?php

$root = dirname(__DIR__);
$files = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
    static fn (SplFileInfo $file): bool => !$file->isDir() || !in_array($file->getFilename(), ['.git', 'vendor', 'node_modules'], true)
));
$count = 0;
$failed = false;
foreach ($files as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }
    exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file->getPathname()) . ' 2>&1', $output, $code);
    if ($code !== 0) {
        echo implode("\n", $output) . "\n";
        $failed = true;
    }
    $output = [];
    $count++;
}
echo "$count ficheiros PHP verificados.\n";
exit($failed ? 1 : 0);
