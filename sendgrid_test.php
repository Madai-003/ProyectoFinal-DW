<?php
require __DIR__.'/config.php';
require __DIR__.'/mailer_sendgrid.php';

$to      = 'mario.rodas0417@gmail.com';
$subject = 'Prueba SendGrid';
$html    = '<p>Hola, esto es una prueba desde InfinityFree.</p>';

$res = sg_send_mail($to, $subject, $html);

header('Content-Type: text/plain; charset=utf-8');
var_dump($res);
echo "\nRevisa logs/sendgrid_error.log si 'ok' = false.\n";
