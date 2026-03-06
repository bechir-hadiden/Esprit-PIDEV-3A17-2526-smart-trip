<?php
// Placez ce fichier dans : C:\Users\MSI\Desktop\symfouny\pi_symfony\public\download_models.php
// Puis ouvrez : http://localhost:8000/download_models.php
// Supprimez-le après utilisation !

set_time_limit(300);
echo "<pre style='font-family:monospace;font-size:14px;padding:20px'>";
echo "=== Téléchargement des modèles face-api.js ===\n\n";

$base = 'https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/weights/';

$files = [
    'tiny_face_detector_model-weights_manifest.json',
    'tiny_face_detector_model-shard1',
    'face_expression_recognition_model-weights_manifest.json',
    'face_expression_recognition_model-shard1',
];

$dir = __DIR__ . '/face-models/';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
    echo "✅ Dossier créé : public/face-models/\n\n";
}

foreach ($files as $file) {
    echo "⬇️  Téléchargement de $file ... ";
    $content = @file_get_contents($base . $file);
    if ($content === false) {
        echo "❌ ERREUR (vérifiez votre connexion internet)\n";
    } else {
        file_put_contents($dir . $file, $content);
        echo "✅ OK (" . round(strlen($content)/1024) . " KB)\n";
    }
}

echo "\n=== Terminé ! ===\n";
echo "\n👉 Maintenant supprimez ce fichier et testez : http://localhost:8000/face-travel\n";
echo "</pre>";