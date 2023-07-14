<?php

namespace App\Service;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Response;

class JWTService
{
    /**
     * Génération du JWT
     *
     * @param array $header
     * @param array $payload
     * @param string $secret
     * @param integer $validity
     * @return string
     */
    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string
    {
        if ($validity > 0) {
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;
    
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }


        //en encode en base64

        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        // on nettoi les valeur encodées (retrait des + / =
        $base64Header = str_replace(['+','/','='],['-','_',''],$base64Header);
        $base64Payload = str_replace(['+','/','='],['-','_',''],$base64Payload);

        //genere la signature
        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header. '.' .$base64Payload, $secret, true);
        $base64Signature = base64_encode($signature);
        $base64Signature = str_replace(['+','/','='],['-','_',''],$base64Signature);

        $jwt = $base64Header . '.' . $base64Payload . '.' . $secret . '.' . $base64Signature;

        return $jwt;
    }

    //verifier si le token est valide (formé)
    public function isValid(string $token) : bool 
    {
        // return preg_match(
        //     '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
        //     $token
        // ) === 1;
        return true;
    }

    // on récupere le payload
    public function getPayload(string $token) : array {
        $array = explode(".",$token);

        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }

    // on récupere le hzader
    public function getHeader(string $token) : array {
        $array = explode(".",$token);

        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

    // on vérifie la date d'expiration du token
    public function isExpired(string $token) : bool 
    {
        $payload = $this->getPayload($token);

        $now = new DateTimeImmutable();

        return $payload["exp"] < $now->getTimestamp();
    }

    //verification du signature du token
    public function checkToken(string $token, string $secret)
    {
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);
        
        //regenerer un nouveau token
        $verifToken = $this->generate($header,$payload,$secret,0);

        return $token === $verifToken;
    }
}