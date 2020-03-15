<?php
declare(strict_types=1);

namespace I4code\JaAuth;

function generateRandomCodeChallenge($verifier)
{
    $challengeBytes = hash("sha256", $verifier, true);
    $codeChallenge = rtrim(strtr(base64_encode($challengeBytes), "+/", "-_"), "=");
    return $codeChallenge;
}

function generateRandomCodeVerifier()
{
    $random = pack('H*', bin2hex(openssl_random_pseudo_bytes(43)));
    $verifierBytes = random_bytes(64);
    $codeVerifier = rtrim(strtr(base64_encode($verifierBytes), "+/", "-_"), "=");
    return $codeVerifier;
}

function generateState()
{
    $state = uniqid();
    return $state;
}

function extractParameterFromUrl($parameter, $url)
{
    $parsedUrl = parse_url($url);
    if (isset($parsedUrl['query'])) {
        $responseQuery = [];
        parse_str($parsedUrl['query'], $responseQuery);
        return $responseQuery[$parameter];
    }
}
