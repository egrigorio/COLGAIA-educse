<?php

namespace Educse\Http;

use Educse\Exceptions\HttpException;

final class Input
{
    public function __construct(private array $data) {}

    public function string(string $key, int $max = 255, bool $required = true): string
    {
        $value = $this->data[$key] ?? '';
        if (!is_string($value) || ($required && trim($value) === '') || mb_strlen($value) > $max) {
            throw new HttpException('Campo inválido: ' . $key);
        }
        return trim($value);
    }

    public function int(string $key, int $min = 1, int $max = PHP_INT_MAX): int
    {
        $value = $this->data[$key] ?? null;
        if (!is_scalar($value) || filter_var($value, FILTER_VALIDATE_INT) === false || (int) $value < $min || (int) $value > $max) {
            throw new HttpException('Número inválido: ' . $key);
        }
        return (int) $value;
    }

    public function number(string $key, float $min = 0, float $max = 10000): float
    {
        $value = $this->data[$key] ?? null;
        if (!is_scalar($value) || !is_numeric($value) || !is_finite((float) $value) || $value < $min || $value > $max) {
            throw new HttpException('Valor inválido: ' . $key);
        }
        return (float) $value;
    }

    public function list(string $key, int $limit = 500): array
    {
        $value = $this->data[$key] ?? [];
        if (!is_array($value) || !array_is_list($value) || count($value) > $limit) {
            throw new HttpException('Lista inválida: ' . $key);
        }
        return $value;
    }

    public function ids(string $key): array
    {
        $ids = [];
        foreach ($this->list($key) as $value) {
            $ids[] = (new self(['id' => $value]))->int('id');
        }
        return array_values(array_unique($ids));
    }

    public function fields(string $pattern): array
    {
        $result = [];
        foreach ($this->data as $key => $value) {
            if (preg_match($pattern, (string) $key, $matches)) {
                $result[] = ['matches' => $matches, 'value' => $value];
            }
        }
        if (count($result) > 500) {
            throw new HttpException('Demasiados campos');
        }
        return $result;
    }

    public function date(string $key): \DateTimeImmutable
    {
        $value = $this->string($key, 10);
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if (!$date || $date->format('Y-m-d') !== $value) {
            throw new HttpException('Data inválida: ' . $key);
        }
        return $date;
    }

    public function has(string $key): bool { return array_key_exists($key, $this->data); }
}
