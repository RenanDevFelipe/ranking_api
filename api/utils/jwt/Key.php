<?php

namespace Firebase\JWT;

use InvalidArgumentException;
use TypeError;

class Key
{
    /**
     * @var string|resource|mixed
     */
    private $keyMaterial;

    /**
     * @var string
     */
    private $algorithm;

    /**
     * Construtor da classe Key (compatível com PHP 7.x)
     *
     * @param string|resource $keyMaterial
     * @param string $algorithm
     * @throws TypeError|InvalidArgumentException
     */
    public function __construct($keyMaterial, $algorithm)
    {
        if (
            !\is_string($keyMaterial)
            && !\is_resource($keyMaterial)
        ) {
            throw new TypeError('Key material must be a string or resource');
        }

        if (empty($keyMaterial)) {
            throw new InvalidArgumentException('Key material must not be empty');
        }

        if (empty($algorithm) || !\is_string($algorithm)) {
            throw new InvalidArgumentException('Algorithm must be a non-empty string');
        }

        $this->keyMaterial = $keyMaterial;
        $this->algorithm = $algorithm;
    }

    /**
     * Retorna o algoritmo definido para essa chave
     *
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * Retorna o material da chave
     *
     * @return string|resource
     */
    public function getKeyMaterial()
    {
        return $this->keyMaterial;
    }
}
