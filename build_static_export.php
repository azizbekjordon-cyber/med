<?php

use App\Http\Controllers\Web\MedPortalController;
use App\Models\Med;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$distDir = __DIR__.'/dist';
if (! is_dir($distDir)) {
    mkdir($distDir, 0777, true);
}

// 1. Render Main Portal
$controller = $app->make(MedPortalController::class);
$response = $controller->index(request());
$html = $response->render();

file_put_contents($distDir.'/index.html', $html);
echo '✓ Generated dist/index.html ('.strlen($html)." bytes)\n";

// 2. Copy manifest and service worker
if (file_exists(__DIR__.'/public/manifest.json')) {
    copy(__DIR__.'/public/manifest.json', $distDir.'/manifest.json');
    echo "✓ Copied manifest.json\n";
}
if (file_exists(__DIR__.'/public/sw.js')) {
    copy(__DIR__.'/public/sw.js', $distDir.'/sw.js');
    echo "✓ Copied sw.js\n";
}

// 3. Render QR Scan view as scan.html
$med = Med::where('med_number', 'MED-2026-7841-9012')->first() ?? Med::first();
if ($med) {
    try {
        $scanView = $controller->scan(request(), $med->qr_token)->render();
        file_put_contents($distDir.'/scan.html', $scanView);
        echo "✓ Generated dist/scan.html\n";
    } catch (Throwable $e) {
        echo 'Scan render notice: '.$e->getMessage()."\n";
    }
}

echo "\n🎉 Netlify ga yuklash uchun 'dist' papkasi tayyor!\n";
