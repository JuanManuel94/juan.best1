<?php

function encrypt_aes($text, $key)
{
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($text, 'aes-256-cbc', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decrypt_aes($text, $key)
{
    $text = base64_decode($text);
    $iv = substr($text, 0, openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = substr($text, openssl_cipher_iv_length('aes-256-cbc'));
    return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
}

function humanReadableBytes($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $index = 0;
    while ($bytes >= 1024 && $index < count($units) - 1) {
        $bytes /= 1024;
        $index++;
    }
    return round($bytes, 2) . ' ' . $units[$index];
}
function getAvailableIPs($ipAddress, $excludeList)
{
    $ipParts = explode('/', $ipAddress);
    $ipAddress = $ipParts[0];
    $subnetMask = isset($ipParts[1]) ? (int)$ipParts[1] : 24;

    $hostPart = explode('.', $ipAddress);
    $hostPart = array_slice($hostPart, 0, 3);

    $availableIPs = [];

    if ($subnetMask == 25) {
        $startRange = 2;
        $endRange = 126;
    } else {
        $startRange = 2;
        $endRange = 254;
    }

    for ($i = $startRange; $i <= $endRange; $i++) {
        $ip = implode('.', array_merge($hostPart, [$i]));
        if (!in_array($ip, $excludeList)) {
            $availableIPs[] = $ip;
        }
    }

    return $availableIPs;
}
function convertToBytes($value, $denomination)
{
    $denominations = [
        'BPS' => 1,
        'KBPS' => 1024,
        'MBPS' => pow(1024, 2),
        'GBPS' => pow(1024, 3),
        'TBPS' => pow(1024, 4),
        'PBPS' => pow(1024, 5),
        'EBPS' => pow(1024, 6),
        'ZBPS' => pow(1024, 7),
        'YBPS' => pow(1024, 8),
    ];

    $denomination = strtoupper($denomination);
    if (!isset($denominations[$denomination])) {
        throw new InvalidArgumentException("Invalid denomination");
    }

    return $value * $denominations[$denomination];
}
function simplePing($host, $count = 4)
{
    $output = array();
    exec("ping -n $count $host", $output, $status);

    if ($status === 0) {
        foreach ($output as $line) {
            echo $line, "\n";
        }
        return true;
    } else {
        echo "Ping failed.";
        return false;
    }
}
function convert_uptime($time)
{
    $units = array(
        's' => 'segundos',
        'm' => 'minutos',
        'h' => 'horas',
        'd' => 'días'
    );

    $parts = array();
    $current_value = '';

    for ($i = 0; $i < strlen($time); $i++) {
        $char = $time[$i];

        if (ctype_digit($char)) {
            $current_value .= $char;
        } else {
            $parts[] = array('value' => (int) $current_value, 'unit' => $char);
            $current_value = '';
        }
    }

    $output = '';

    foreach ($parts as $part) {
        $value = $part['value'];
        $unit = $part['unit'];

        if ($value == 1) {
            $unit = str_replace('s', '', $units[$unit]);
        }

        if ($output != '') {
            $output .= ' ';
        }

        $output .= $value . ' ' . $units[$unit];
    }

    return 'Hace ' . $output;
}

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return strpos($haystack, $needle) !== false;
    }
}

