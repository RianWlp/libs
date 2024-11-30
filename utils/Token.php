<?php

namespace RianWlp\Libs\utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class Token
{
    private string $secretKey = 'dD7#3gH5@9nqRbLpX2v1A1zN6wYmL8E!';

    public function __construct() {}

    /**
     * Cria um token JWT com os dados do usuário.
     *
     * @param array $data Dados que serão incluídos no payload do token (exemplo: ['userId' => 1, 'username' => 'usuario'])
     * @return string Retorna o token JWT gerado
     * 
     * @throws Exception Se houver algum erro durante a criação do token
     */

    public function create(array $data, bool $noExpiration = false): string
    {
        $issuedAt = time();

        // Payload base
        $payload = [
            'iss'  => 'http://localhost:82', // Emissor    (Aqui vai ir o IP da oficial fornecida pelo professor)
            'aud'  => 'http://localhost:82', // Audiência  (Aqui vai ir o IP da oficial fornecida pelo professor)
            'iat'  => $issuedAt,             // Emitido em
            'data' => $data                  // Dados do usuário
        ];

        error_log("\n -------------------------\n");
        error_log("\n Criacao do TOKEN\n");
        error_log("\n -------------------------\n");

        error_log("\n -------------------------\n");
        error_log("\n Se expira ou nao $noExpiration \n");
        error_log("\n -------------------------\n");

        // Adiciona o campo exp apenas se o token tiver um tempo limite
        if (!$noExpiration) {
            error_log("\n -------------------------\n");
            error_log("\n Vai expirar \n");
            error_log("\n -------------------------\n");
            $payload['exp'] = $issuedAt + (30 * 60); // Expiração em 15 minutos
        }

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    // /**
    //  * Valida e decodifica um token JWT.
    //  *
    //  * @param string $token Token JWT a ser validado
    //  * @return array|null Retorna os dados do usuário do token, ou null se o token for inválido ou expirado
    //  * 
    //  * @throws Exception Se houver erro ao validar o token
    //  */
    // public function validate(string $token): ?array
    // {
    //     try {

    //         $token   = $this::replaceBearer($token);
    //         $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
    //         return (array) $decoded->data; // Retorna dados do usuário no token
    //     } catch (Exception $e) {
    //         // Trate a exceção conforme necessário (por exemplo, registrar erro, lançar nova exceção etc.)
    //         return null;
    //     }
    // }

    private function replaceBearer(string $token): string
    {
        return trim(str_replace('Bearer', '', $token));
    }

    /**
     * Verifica se um token JWT é válido.
     *
     * @param string $token Token JWT a ser verificado
     * @return bool Retorna true se o token for válido, false caso contrário
     */
    protected function validateToken(string $token): bool
    {
        try {

            $token   = $this::replaceBearer($token);
            error_log("\n-------------------------\n");
            error_log("\nToken que veio $token \n");
            error_log("\n-------------------------\n");
            $decoded = JWT::decode($token,  new Key($this->secretKey, 'HS256'));

            error_log("\n -------------------------\n");
            error_log("\n $decoded\n");
            error_log("\n -------------------------\n");
            // Se for false significa que ele nao (e) mais valido:
            // return isset($decoded->exp) && $decoded->exp < time();
            return isset($decoded);
        } catch (\Exception $e) {
            return false;
        }
    }
}
