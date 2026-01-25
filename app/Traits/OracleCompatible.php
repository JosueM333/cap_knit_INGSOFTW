<?php

namespace App\Traits;

/**
 * Trait OracleCompatible
 * 
 * Permite que los modelos Eloquent trabajen correctamente con Oracle.
 * Soluciona el problema de case-sensitivity (mayúsculas/minúsculas).
 * 
 * Si el modelo pide $this->CLI_ID pero la base devuelve 'cli_id', este trait lo encuentra.
 * Si el modelo pide $this->cli_id pero la base devuelve 'CLI_ID', también lo encuentra.
 */
trait OracleCompatible
{
    /**
     * Sobreescribe getAttribute para búsqueda insensible a mayúsculas/minúsculas
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        // 1. Intento estándar de Laravel (rápido)
        $value = parent::getAttribute($key);
        if ($value !== null) {
            return $value;
        }

        // 2. Si es null, podría ser que la key no coincida en case
        // Buscamos en los atributos crudos
        $attributes = $this->getAttributes();

        // Si existe la key exacta pero es null, devolvemos null
        if (array_key_exists($key, $attributes)) {
            return $attributes[$key];
        }

        // 3. Búsqueda Insensible (Case-Insensitive)
        // Buscamos si existe alguna key que coincida ignorando mayúsculas
        foreach ($attributes as $k => $v) {
            if (strcasecmp($k, $key) === 0) {
                return $v;
            }
        }

        // 4. Si no está en atributos, delegamos al padre (accesores, relaciones, etc.)
        return parent::getAttribute($key);
    }

    /**
     * Asegura que el modelo sepa cuál es su ID real sin importar el case
     */
    public function getKeyName()
    {
        $keyName = parent::getKeyName();

        // Si tenemos atributos cargados, intentamos encontrar el case correcto de la PK
        if (method_exists($this, 'getAttributes')) {
            $attributes = $this->getAttributes();
            foreach ($attributes as $k => $v) {
                if (strcasecmp($k, $keyName) === 0) {
                    return $k;
                }
            }
        }

        return $keyName;
    }

    /**
     * Establece un atributo. Intenta respetar el case existente si ya existe.
     */
    public function setAttribute($key, $value)
    {
        // Verificamos si ya existe el atributo con otro casing
        if (method_exists($this, 'getAttributes')) {
            $attributes = $this->getAttributes();
            foreach ($attributes as $k => $v) {
                if (strcasecmp($k, $key) === 0) {
                    // Usamos el key existente para no duplicar (ej. no tener 'CLI_ID' y 'cli_id')
                    return parent::setAttribute($k, $value);
                }
            }
        }

        // Si no existe, usamos mayúsculas por convención de Oracle (o la que se pase)
        // Preferimos usar la definida en $fillable si existe
        if (isset($this->fillable)) {
            foreach ($this->fillable as $fillableKey) {
                if (strcasecmp($fillableKey, $key) === 0) {
                    return parent::setAttribute($fillableKey, $value);
                }
            }
        }

        return parent::setAttribute($key, $value);
    }
}
