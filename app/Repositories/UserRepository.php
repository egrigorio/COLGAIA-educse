<?php

namespace Educse\Repositories;

final class UserRepository
{
    public function findByIdentity(string $identity): ?array
    {
        $column = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $users = \my_query("SELECT * FROM users WHERE $column = ? LIMIT 1", 0, [$identity]);
        return $users[0] ?? null;
    }

    public function exists(string $email, string $username): bool
    {
        return \my_query('SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1', 0, [$email, $username]) !== [];
    }

    public function create(array $account): int
    {
        return \my_query(
            'INSERT INTO users (username, email, password, cargo, pfp, ativo) VALUES (?, ?, ?, ?, ?, 1)',
            0, [$account['user'], $account['email'], $account['pass'], $account['cargo'], $account['pfp']]
        );
    }

    public function recordLogin(int $id): void
    {
        \my_query('UPDATE users SET ultimo_login = NOW() WHERE id = ?', 0, [$id]);
    }

    public function changePassword(string $email, string $hash): void
    {
        \my_query('UPDATE users SET password = ? WHERE email = ?', 0, [$hash, $email]);
    }
}
