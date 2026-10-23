<?php
require_once __DIR__ . '/../../nucleo/ApiResponse.php';
ApiResponse::send(410, 'error', 'Los catálogos independientes fueron integrados en Inventario y laboratorios.');
