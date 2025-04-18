<?php 

header("Content-Type: application/json");
// Permite qualquer origem (pode restringir se quiser depois)
header("Access-Control-Allow-Origin: *");

// Métodos HTTP permitidos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Cabeçalhos personalizados permitidos
header("Access-Control-Allow-Headers: Content-Type, Authorization");

?>