<?php
$password = '101010'; // ⬅️ CAMBIA ESTA CONTRASEÑA
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "El HASH que debes usar es: " . $hash;
?>