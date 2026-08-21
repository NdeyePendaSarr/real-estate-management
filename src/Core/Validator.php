<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Validation d'entrées simple et lisible : on enchaîne les règles par champ
 * et on récupère un tableau d'erreurs (champ => message).
 */
final class Validator
{
    private array $errors = [];

    public function __construct(private array $data)
    {
    }

    public function value(string $field): string
    {
        return trim((string) ($this->data[$field] ?? ''));
    }

    public function required(string $field, string $label): self
    {
        if ($this->value($field) === '') {
            $this->addError($field, "Le champ « {$label} » est obligatoire.");
        }
        return $this;
    }

    public function email(string $field, string $label): self
    {
        $v = $this->value($field);
        if ($v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "Le champ « {$label} » doit être un email valide.");
        }
        return $this;
    }

    public function min(string $field, string $label, int $min): self
    {
        if (mb_strlen($this->value($field)) < $min) {
            $this->addError($field, "Le champ « {$label} » doit faire au moins {$min} caractères.");
        }
        return $this;
    }

    public function numeric(string $field, string $label): self
    {
        $v = $this->value($field);
        if ($v !== '' && !is_numeric($v)) {
            $this->addError($field, "Le champ « {$label} » doit être un nombre.");
        }
        return $this;
    }

    public function in(string $field, string $label, array $allowed): self
    {
        $v = $this->value($field);
        if ($v !== '' && !in_array($v, $allowed, true)) {
            $this->addError($field, "Valeur invalide pour « {$label} ».");
        }
        return $this;
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    /** Liste plate de tous les messages, pratique pour l'affichage flash. */
    public function messages(): array
    {
        return array_merge(...array_values($this->errors ?: [[]]));
    }
}
