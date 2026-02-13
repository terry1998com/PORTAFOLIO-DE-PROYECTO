<?php
$data = $_POST['image'] ?? '';
$matricula = $_POST['matricula'] ?? ''; 

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'No se recibió imagen']);
    exit;
}

// Quitar encabezado del base64
$data = str_replace('data:image/png;base64,', '', $data);
$data = str_replace(' ', '+', $data);
$imageData = base64_decode($data);

$filename = preg_replace('/[^a-zA-Z0-9_\-]/', '', $matricula) . '.png';
$filepath = '../temp/' . $filename;

// Guardar imagen (sobrescribe si ya existe)
if (file_put_contents($filepath, $imageData)) {
    echo json_encode(['status' => 'ok', 'filename' => $filename]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo guardar']);
}
?>  