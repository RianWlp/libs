<?php

namespace RianWlp\Libs\utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class Token
{
    private string $secretKey = 'dD7#3gH5@9nqRbLpX2v1A1zN6wYmL8E!';

    public function __construct()
    {
        // Configuração adicional, se necessário
    }

    /**
     * Cria um token JWT com os dados do usuário.
     *
     * @param array $data Dados que serão incluídos no payload do token (exemplo: ['userId' => 1, 'username' => 'usuario'])
     * @return string Retorna o token JWT gerado
     * 
     * @throws Exception Se houver algum erro durante a criação do token
     */
    public function create(array $data): string
    {
        $issuedAt = time();
        $expiration = $issuedAt + (15 * 60); // Token válido por 15 minutos

        $payload = [
            'iss'  => 'http://localhost:82', // Emissor
            'aud'  => 'http://localhost:82', // Audiência
            'iat'  => $issuedAt,             // Emitido em
            'exp'  => $expiration,           // Expiração
            'data' => $data                  // Dados do usuário
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    /**
     * Valida e decodifica um token JWT.
     *
     * @param string $token Token JWT a ser validado
     * @return array|null Retorna os dados do usuário do token, ou null se o token for inválido ou expirado
     * 
     * @throws Exception Se houver erro ao validar o token
     */
    public function validate(string $token): ?array
    {
        try {

            $token   = $this::replaceBearer($token);
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded->data; // Retorna dados do usuário no token
        } catch (Exception $e) {
            // Trate a exceção conforme necessário (por exemplo, registrar erro, lançar nova exceção etc.)
            return null;
        }
    }

    /**
     * Verifica se um token JWT é válido.
     *
     * @param string $token Token JWT a ser verificado
     * @return bool Retorna true se o token for válido, false caso contrário
     */
    public function isValid(string $token): bool
    {
        try {
            $token   = $this::replaceBearer($token);
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));

            // Verifica se a data de expiração já passou
            // return isset($decoded->exp) && $decoded->exp < time();
            return isset($decoded->exp) && $decoded->exp > time();
        } catch (Exception $e) {
            return false; // Token inválido ou erro de decodificação
        }
    }

    private function replaceBearer(string $token): string
    {
        return trim(str_replace('Bearer', '', $token));
    }
}
