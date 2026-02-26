<?php
$root = realpath(__DIR__ . '/..');
$excludeFiles = [
    'google3bacf333834d10cd.html',
];

function isPublicPage(string $relativePath, array $excludeFiles): bool
{
    $normalized = ltrim(str_replace('\\', '/', $relativePath), '/');

    if ($normalized === '' || in_array(basename($normalized), $excludeFiles, true)) {
        return false;
    }

    if (!str_ends_with(strtolower($normalized), '.html')) {
        return false;
    }

    if ($normalized === 'index.html') {
        return true;
    }

    return str_starts_with($normalized, 'pages/') || str_starts_with($normalized, 'services/');
}

function getPageTitle(string $filePath): string
{
    $contents = @file_get_contents($filePath, false, null, 0, 20000);
    if ($contents && preg_match('/<title>(.*?)<\/title>/is', $contents, $matches)) {
        $title = trim(html_entity_decode($matches[1], ENT_QUOTES));
        if ($title !== '') {
            return $title;
        }
    }

    $fallback = basename($filePath);
    $fallback = preg_replace('/\.[^.]+$/', '', $fallback);
    $fallback = str_replace(['-', '_'], ' ', $fallback);
    return ucwords($fallback);
}

$pages = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $fileInfo) {
    if (!$fileInfo->isFile()) {
        continue;
    }

    $absolutePath = $fileInfo->getPathname();
    $relativePath = str_replace($root, '', $absolutePath);
    $relativePath = str_replace('\\', '/', $relativePath);
    if ($relativePath === '' || !isPublicPage($relativePath, $excludeFiles)) {
        continue;
    }

    $pages[] = [
        'url' => $relativePath,
        'title' => getPageTitle($absolutePath),
    ];
}


usort($pages, static function ($a, $b) {
    return strnatcasecmp($a['title'], $b['title']);
});
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>      <html class="no-js"> <!--<![endif]-->
<html>
    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Trade+Winds&display=swap" rel="stylesheet">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Site Map | Warf Designs</title>
        <meta name="description" content="A comprehensive list of all pages on the Warf Designs website.">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/../../css/styles.css">
    </head>
    <div id="analytics"></div>
    <body>
        <div id="nav"></div>
        <div class="container" style="padding-top: 5%; padding-bottom: 5%;">
            <section>
                <h1 style="text-align: center;">Site Map</h1>
                    <ul>
                        <?php foreach ($pages as $page): ?>
                            <li class="list-no-dots">
                                <a href="<?= htmlspecialchars($page['url'], ENT_QUOTES) ?>">
                                    <?= htmlspecialchars($page['title'], ENT_QUOTES) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
            </section>
        </div>
        <footer id="footer"><script defer async src='https://cdn.trustindex.io/loader.js?f2a805d49e2247534c2688b9033'></script></footer>
        <div id="appMenu"></div>
        <script src="/js/script.js"></script>
    </body>
</html>