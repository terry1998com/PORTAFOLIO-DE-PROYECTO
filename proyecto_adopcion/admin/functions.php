<?php
function renderEstadoBadge($estado) {
    $estado = $estado ?? 'Pendiente';
    $icon = '';
    $badgeClass = '';

    switch ($estado) {
        case 'Aprobado':
            $icon = 'bi-check-circle-fill';
            $badgeClass = 'bg-success';
            break;
        case 'Rechazado':
            $icon = 'bi-x-octagon-fill';
            $badgeClass = 'bg-danger';
            break;
        case 'Pendiente':
            $icon = 'bi-clock-fill';
            $badgeClass = 'bg-warning text-dark';
            break;
        default:
            $icon = 'bi-info-circle-fill';
            $badgeClass = 'bg-secondary';
    }

    return "<span class=\"badge rounded-pill {$badgeClass} p-2\">
                <i class=\"bi {$icon} me-1\"></i>" . ucfirst($estado) . "
            </span>";
}
