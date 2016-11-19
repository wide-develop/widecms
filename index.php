<?php

if (!is_dir('vendor')) {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'You must install composer dependencies.';
    exit(1); // EXIT_ERROR
} elseif (is_file('install.php')) {
    header('Location: install.php');
    die();
}

echo utf8_decode('<h1>Parece que ocorreu tudo bem com a instalação :] <br> <a href="wd-admin">Crie um projeto.</a></h1>');
