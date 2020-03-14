<?php
declare(strict_types=1);

namespace I4code\JaAuth;

function generateRandomCodeChallenge()
{
    $random = pack('H*', bin2hex(openssl_random_pseudo_bytes(43)));
    $verifierBytes = random_bytes(64);
    $codeVerifier = rtrim(strtr(base64_encode($verifierBytes), "+/", "-_"), "=");
    $challengeBytes = hash("sha256", $codeVerifier, true);
    $codeChallenge = rtrim(strtr(base64_encode($challengeBytes), "+/", "-_"), "=");
    return $codeChallenge;
}

function generateState()
{
    $state = uniqid();
    return $state;
}
