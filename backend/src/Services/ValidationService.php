<?php

namespace FormFlow\Services;

use Respect\Validation\Validator as v;

class ValidationService
{
    public function validateRegistration(array $data): array
    {
        $errors = [];

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        }

        if (empty($data['password']) || strlen($data['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        }

        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }

        return $errors;
    }

    public function validateLogin(array $data): array
    {
        $errors = [];

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        }

        return $errors;
    }

    public function validateForm(array $data): array
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = 'Form title is required';
        }

        if (!isset($data['fields']) || !is_array($data['fields'])) {
            $errors['fields'] = 'Fields must be an array';
        }

        return $errors;
    }

    public function validateFormSubmission(array $fields, array $answers): array
    {
        $errors = [];

        foreach ($fields as $field) {
            if ($field['required']) {
                $answer = $this->findAnswer($answers, $field['id']);

                if ($answer === null || empty($answer['value'])) {
                    $errors[$field['id']] = $field['label'] . ' is required';
                    continue;
                }

                // Type-specific validation
                if ($field['type'] === 'email' && !filter_var($answer['value'], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field['id']] = 'Valid email is required';
                }

                if ($field['type'] === 'number' && !is_numeric($answer['value'])) {
                    $errors[$field['id']] = 'Must be a number';
                }
            }
        }

        return $errors;
    }

    private function findAnswer(array $answers, string $fieldId): ?array
    {
        foreach ($answers as $answer) {
            if ($answer['fieldId'] === $fieldId) {
                return $answer;
            }
        }
        return null;
    }

    public function sanitizeHtml(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    public function validateSlug(string $slug): bool
    {
        return preg_match('/^[a-z0-9-]+$/', $slug) === 1;
    }
}
