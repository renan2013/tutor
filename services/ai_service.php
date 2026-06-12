<?php
/**
 * Servicio de IA para el Asistente de Diseño
 * Conecta con la API de Google Gemini
 */
session_start();
require_once '../config/api_keys.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$message = $_POST['message'] ?? '';

if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Mensaje vacío']);
    exit;
}

// Configuración de la petición a Gemini API
$url = "https://generativelanguage.googleapis.com/v1beta/models/" . AI_MODEL . ":generateContent?key=" . AI_API_KEY;

$system_instruction = "Eres un Asistente de Diseño experto en la suite de Adobe (Photoshop, Illustrator, InDesign). 
Tu objetivo es ayudar a estudiantes a aprender a usar estas herramientas. 
Proporciona respuestas técnicas precisas, consejos prácticos y atajos de teclado cuando sea relevante. 
Mantén un tono profesional, motivador y educativo. Responde siempre en español.";

$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $system_instruction . "\n\nUsuario: " . $message]
            ]
        ]
    ],
    "generationConfig" => [
        "temperature" => 0.7,
        "maxOutputTokens" => 800,
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode(['success' => false, 'error' => 'Error de cURL: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

$result = json_decode($response, true);

if ($httpCode === 200 && isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    $reply = $result['candidates'][0]['content']['parts'][0]['text'];
    echo json_encode(['success' => true, 'reply' => $reply]);
} else {
    $errorMsg = $result['error']['message'] ?? 'Error desconocido de la API';
    echo json_encode(['success' => false, 'error' => 'API Error: ' . $errorMsg]);
}
?>
