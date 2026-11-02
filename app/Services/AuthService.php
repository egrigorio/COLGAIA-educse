<?php

namespace Educse\Services;

use Educse\Repositories\UserRepository;
use DomainException;

final class AuthService
{
    public function __construct(private UserRepository $users = new UserRepository()) {}

    public function login(string $identity, string $password): array
    {
        if (trim($identity) === '' || $password === '') {
            throw new DomainException('Preencha todos os campos');
        }
        $user = $this->users->findByIdentity(trim($identity));
        if (!$user || !password_verify($password, $user['password'])) {
            throw new DomainException('Credenciais incorretas');
        }
        if (!(int) $user['ativo']) {
            throw new DomainException('Conta não verificada');
        }
        $this->users->recordLogin((int) $user['id']);
        return $user;
    }

    public function prepareRegistration(string $email, string $username, string $password, string $confirmation, string $role): array
    {
        $email = trim($email);
        $username = trim($username);
        if ($email === '' || $username === '' || $password === '' || $confirmation === '') {
            throw new DomainException('Preencha todos os campos');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DomainException('Email inválido');
        }
        if (!in_array($role, ['aluno', 'professor', 'instituicao'], true)) {
            throw new DomainException('Cargo inválido');
        }
        if ($password !== $confirmation) {
            throw new DomainException('As passwords não coincidem');
        }
        if ($this->users->exists($email, $username)) {
            throw new DomainException('Utilizador já existe');
        }
        return ['email' => $email, 'user' => $username, 'pass' => password_hash($password, PASSWORD_DEFAULT), 'cargo' => $role, 'pfp' => 'e.png'];
    }

    public static function startSession(array $user): void
    {
        session_regenerate_id(true);
        unset($_SESSION['csrf_token']);
        $_SESSION['id'] = (int) $user['id'];
        $_SESSION['user'] = $user['username'];
        $_SESSION['ultimo_login'] = $user['ultimo_login'];
        $_SESSION['cargo'] = strtolower($user['cargo']);
        $_SESSION['pfp'] = $user['pfp'];
        $_SESSION['theme'] = 'mytheme';
    }
}
