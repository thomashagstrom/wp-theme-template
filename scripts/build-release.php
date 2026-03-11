<?php
declare(strict_types=1);

/**
 * Build a distributable ZIP archive of the current theme.
 *
 * Responsibilities:
 *  - bump the semantic version in style.css & package.json (defaults to patch)
 *  - regenerate CHANGELOG.md from conventional commits
 *  - regenerate pattern preview HTML snapshots
 *  - clean the dist/ directory and emit the release ZIP inside it
 *
 * Usage examples
 *  php scripts/build-release.php                 # bump patch (default)
 *  php scripts/build-release.php --bump=minor    # bump the minor version
 *  php scripts/build-release.php --skip-previews # do not refresh previews
 */

$themeRoot = realpath(__DIR__ . '/..');
if ($themeRoot === false) {
    fwrite(STDERR, "Unable to resolve theme root.\n");
    exit(1);
}

$stylePath = $themeRoot . DIRECTORY_SEPARATOR . 'style.css';
if (!is_readable($stylePath)) {
    fwrite(STDERR, "Could not read style.css to determine version.\n");
    exit(1);
}

/**
 * @return array{bump:string, refreshPreviews:bool, help:bool}
 */
function parseOptions(): array
{
    $options = getopt('', ['bump::', 'skip-previews', 'help']);

    $bump = $options['bump'] ?? 'patch';
    if (!in_array($bump, ['major', 'minor', 'patch'], true)) {
        fwrite(STDERR, "Invalid bump type '{$bump}'. Allowed: major, minor, patch.\n");
        exit(1);
    }

    return [
        'bump' => $bump,
        'refreshPreviews' => !array_key_exists('skip-previews', $options),
        'help' => array_key_exists('help', $options),
    ];
}

/**
 * @param string $version
 */
function bumpVersion(string $version, string $type): string
{
    if (!preg_match('/^(\d+)\.(\d+)\.(\d+)(.*)$/', $version, $matches)) {
        fwrite(STDERR, "Version '{$version}' is not in a supported format (x.y.z).\n");
        exit(1);
    }

    [$full, $major, $minor, $patch, $suffix] = $matches;

    $major = (int) $major;
    $minor = (int) $minor;
    $patch = (int) $patch;

    switch ($type) {
        case 'major':
            $major++;
            $minor = 0;
            $patch = 0;
            break;
        case 'minor':
            $minor++;
            $patch = 0;
            break;
        default:
            $patch++;
            break;
    }

    return sprintf('%d.%d.%d%s', $major, $minor, $patch, $suffix);
}

function updateStyleVersion(string $stylePath, string $newVersion): string
{
    $styleContents = file_get_contents($stylePath);
    if ($styleContents === false) {
        fwrite(STDERR, "Unable to read style.css to update version.\n");
        exit(1);
    }

    $count = 0;
    $updated = preg_replace_callback(
        '/^(\s*Version:\s*)(.+)$/mi',
        static function (array $matches) use ($newVersion): string {
            return $matches[1] . $newVersion;
        },
        $styleContents,
        -1,
        $count
    );

    if ($updated === null || $count === 0) {
        fwrite(STDERR, "Failed to locate Version header inside style.css.\n");
        exit(1);
    }

    if (file_put_contents($stylePath, $updated) === false) {
        fwrite(STDERR, "Unable to write updated style.css.\n");
        exit(1);
    }

    return $updated;
}

function updatePackageJsonVersion(string $packagePath, string $newVersion): void
{
    $packageContents = file_get_contents($packagePath);
    if ($packageContents === false) {
        fwrite(STDERR, "Unable to read package.json.\n");
        exit(1);
    }

    $data = json_decode($packageContents, true);
    if (!is_array($data)) {
        fwrite(STDERR, "package.json did not decode into an array.\n");
        exit(1);
    }

    $data['version'] = $newVersion;

    $encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($encoded === false) {
        fwrite(STDERR, "Unable to encode updated package.json.\n");
        exit(1);
    }

    // npm prefers trailing newlines and two-space indentation for human readability.
    $encoded = str_replace('    ', '  ', $encoded) . "\n";

    if (file_put_contents($packagePath, $encoded) === false) {
        fwrite(STDERR, "Unable to write package.json.\n");
        exit(1);
    }
}

function removeDirectory(string $path): void
{
    if (!is_dir($path)) {
        return;
    }

    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($items as $item) {
        $remove = $item->isDir() ? 'rmdir' : 'unlink';
        if (!$remove($item->getPathname())) {
            fwrite(STDERR, "Failed to remove {$item->getPathname()} while cleaning dist.\n");
            exit(1);
        }
    }

    if (!rmdir($path)) {
        fwrite(STDERR, "Failed to remove dist directory {$path}.\n");
        exit(1);
    }
}

function runPatternPreviewUpdate(string $scriptPath): void
{
    if (!is_file($scriptPath) || !is_readable($scriptPath)) {
        fwrite(STDERR, "Pattern preview script not found at {$scriptPath}.\n");
        exit(1);
    }

    $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($scriptPath);
    $descriptorSpec = [
        0 => STDIN,
        1 => STDOUT,
        2 => STDERR,
    ];

    $process = proc_open($command, $descriptorSpec, $pipes, dirname($scriptPath));
    if (!is_resource($process)) {
        fwrite(STDERR, "Unable to execute pattern preview refresh script.\n");
        exit(1);
    }

    $status = proc_close($process);
    if ($status !== 0) {
        fwrite(STDERR, "Pattern preview refresh failed with exit code {$status}.\n");
        exit($status);
    }
}

function runNpmScript(string $themeRoot, string $scriptName): void
{
    $npmBinary = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'npm.cmd' : 'npm';
    $command = escapeshellarg($npmBinary) . ' run ' . escapeshellarg($scriptName);
    $descriptorSpec = [
        0 => STDIN,
        1 => STDOUT,
        2 => STDERR,
    ];

    $process = proc_open($command, $descriptorSpec, $pipes, $themeRoot);
    if (!is_resource($process)) {
        fwrite(STDERR, "Unable to execute npm script '{$scriptName}'.\n");
        exit(1);
    }

    $status = proc_close($process);
    if ($status !== 0) {
        fwrite(STDERR, "npm script '{$scriptName}' failed with exit code {$status}.\n");
        exit($status);
    }
}

$options = parseOptions();
if ($options['help']) {
    echo "Usage: php scripts/build-release.php [--bump=patch|minor|major] [--skip-previews]\n";
    exit(0);
}

$styleContents = file_get_contents($stylePath) ?: '';
$currentVersion = null;
if (preg_match('/^\s*Version:\s*(.+)$/mi', $styleContents, $matches)) {
    $currentVersion = trim($matches[1]);
}

if ($currentVersion === null || $currentVersion === '') {
    fwrite(STDERR, "Version header missing in style.css.\n");
    exit(1);
}

$newVersion = bumpVersion($currentVersion, $options['bump']);
$styleContents = updateStyleVersion($stylePath, $newVersion);

$packageJsonPath = $themeRoot . DIRECTORY_SEPARATOR . 'package.json';
if (is_file($packageJsonPath)) {
    updatePackageJsonVersion($packageJsonPath, $newVersion);
}

runNpmScript($themeRoot, 'build:changelog');

if ($options['refreshPreviews']) {
    runPatternPreviewUpdate($themeRoot . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'update-pattern-previews.php');
}

if (!class_exists('ZipArchive')) {
    fwrite(STDERR, "The ZipArchive extension is required to build the release package.\n");
    exit(1);
}

$distDir = $themeRoot . DIRECTORY_SEPARATOR . 'dist';
if (is_dir($distDir)) {
    removeDirectory($distDir);
}
if (!mkdir($distDir, 0775, true) && !is_dir($distDir)) {
    fwrite(STDERR, "Unable to create dist directory at {$distDir}.\n");
    exit(1);
}

$themeSlug = basename($themeRoot);
$textDomain = null;
if (preg_match('/^\s*Text Domain:\s*(.+)$/mi', $styleContents, $matches)) {
    $textDomain = trim($matches[1]);
}

if ($textDomain !== null && $textDomain !== '') {
    $themeSlug = $textDomain;
}

$zipPath = $distDir . DIRECTORY_SEPARATOR . sprintf('%s-%s.zip', $themeSlug, $newVersion);
if (file_exists($zipPath) && !unlink($zipPath)) {
    fwrite(STDERR, "Unable to overwrite existing archive at {$zipPath}.\n");
    exit(1);
}

$zip = new ZipArchive();
if (true !== $zip->open($zipPath, ZipArchive::CREATE)) {
    fwrite(STDERR, "Unable to open archive for writing at {$zipPath}.\n");
    exit(1);
}

$exclusions = [
    'dist',
    'node_modules',
    '.git',
    '.github',
];

$directoryIterator = new RecursiveDirectoryIterator(
    $themeRoot,
    FilesystemIterator::SKIP_DOTS
);

$filterIterator = new RecursiveCallbackFilterIterator(
    $directoryIterator,
    static function (SplFileInfo $fileInfo) use ($themeRoot, $zipPath, $exclusions): bool {
        $pathName = $fileInfo->getPathname();
        if ($pathName === $zipPath) {
            return false;
        }

        $relativePath = substr($pathName, strlen($themeRoot) + 1);
        if ($relativePath === false) {
            return false;
        }

        $relativePath = str_replace('\\', '/', $relativePath);
        $segments = explode('/', $relativePath);
        $topLevel = $segments[0] ?? '';

        return !in_array($topLevel, $exclusions, true);
    }
);

$iterator = new RecursiveIteratorIterator(
    $filterIterator,
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $fileInfo) {
    $pathName = $fileInfo->getPathname();
    $relativePath = substr($pathName, strlen($themeRoot) + 1);
    if ($relativePath === false) {
        continue;
    }

    $relativePath = str_replace('\\', '/', $relativePath);
    $zipEntryName = $themeSlug . '/' . $relativePath;

    if ($fileInfo->isDir()) {
        $zip->addEmptyDir($zipEntryName);
        continue;
    }

    if (!$zip->addFile($pathName, $zipEntryName)) {
        fwrite(STDERR, "Failed to add {$relativePath} to archive.\n");
        $zip->close();
        exit(1);
    }
}

if (!$zip->close()) {
    fwrite(STDERR, "Failed to finalize archive {$zipPath}.\n");
    exit(1);
}

echo "Created release archive: {$zipPath}\n";
