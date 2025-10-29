<?php
if (!defined('SENDGRID_API_KEY')) {
  require_once __DIR__ . '/config.php';
}

/**
 * Envía un correo usando la API de SendGrid.
 *
 * @param string $to      Destinatario
 * @param string $subject Asunto
 * @param string $html    Cuerpo HTML
 * @param string $text    Cuerpo Texto (opcional)
 * @return array ['ok'=>bool, 'status'=>int, 'errno'=>int, 'error'=>string, 'response'=>string]
 */
function sg_send_mail(string $to, string $subject, string $html, string $text=''): array {
  $logDir = __DIR__ . '/log';
  if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
  }
  $logFile = $logDir . '/sendgrid_error.log';

  $endpoint = 'https://api.sendgrid.com/v3/mail/send';
  $payload = [
    "personalizations" => [[ "to" => [[ "email" => $to ]] ]],
    "from"  => [ "email" => MAIL_FROM, "name" => MAIL_FROM_NAME ],
    "subject" => $subject,
    "content" => [
      ["type" => "text/plain", "value" => ($text !== '' ? $text : strip_tags($html)) ],
      ["type" => "text/html",  "value" => $html ],
    ]
  ];

  $ch = curl_init($endpoint);
  curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER     => [
      'Authorization: Bearer '.SENDGRID_API_KEY,
      'Content-Type: application/json'
    ],
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 25,
    CURLOPT_SSL_VERIFYPEER => true,
  ]);

  $body   = curl_exec($ch);
  $errno  = curl_errno($ch);
  $error  = curl_error($ch);
  $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  $ok = ($errno === 0 && $status === 202);

  if (!$ok) {
    $log = "[SendGrid] ".date('c')." status={$status} errno={$errno} error={$error}\n".
           "Response: {$body}\n";
    @file_put_contents($logFile, $log, FILE_APPEND);
  }

  return [ 'ok'=>$ok, 'status'=>$status, 'errno'=>$errno, 'error'=>$error, 'response'=>$body ];
}
