<?php
// private/gestion_mascotas.php
include '../includes/header.php';
requireAuth([4, 5]); // Solo Admins (4) y Refugios (5)

// Obtener todas las mascotas (simplificado)
$sql = "SELECT m.id_mascota, m.nombre, e.nombre_especie, m.estado_adopcion 
        FROM mascotas m 
        JOIN especies e ON m.id_especie = e.id_especie 
        ORDER BY m.id_mascota DESC";
$mascotas = $pdo->query($sql)->fetchAll();
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Gestión de Mascotas</h1>
        <a href="mascota_form.php" class="btn-submit" style="width: auto; padding: 10px 20px; text-decoration: none;">+ Nueva Mascota</a>
    </div>

    <table style="width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <thead>
            <tr style="background: var(--dark-color); color: white; text-align: left;">
                <th style="padding: 15px;">Nombre</th>
                <th style="padding: 15px;">Especie</th>
                <th style="padding: 15px;">Estado</th>
                <th style="padding: 15px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mascotas as $m): ?>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 15px;"><?php echo htmlspecialchars($m['nombre']); ?></td>
                <td style="padding: 15px;"><?php echo htmlspecialchars($m['nombre_especie']); ?></td>
                <td style="padding: 15px;">
                    <span style="padding: 5px 10px; border-radius: 15px; font-size: 0.9em; 
                          background: <?php echo $m['estado_adopcion'] == 'Disponible' ? '#d4edda' : '#f8d7da'; ?>;">
                        <?php echo htmlspecialchars($m['estado_adopcion']); ?>
                    </span>
                </td>
                <td style="padding: 15px;">
                    <a href="mascota_form.php?id=<?php echo $m['id_mascota']; ?>" style="color: var(--secondary-color); margin-right: 10px;">Editar</a>
                    <a href="#" onclick="alert('Función de eliminar pendiente de implementar')" style="color: var(--danger-color);">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>