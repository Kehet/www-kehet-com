<?php

header('Content-Type: application/json');

try {
    echo json_encode(
        [
            'headers' => getallheaders(),
            'get' => $_GET,
            'post' => $_POST,
        ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
} catch (JsonException $e) {
    echo $e->getMessage();
}
