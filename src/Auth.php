<?php

namespace Easysite\Library;

use Easysite\Library\Instance\Session;
use Easysite\Library\Interface\SqlRepositoryInterface;

/**
 * Login mechanics: session + a separate remember-me cookie (survives closing the
 * browser; table user_remember_tokens holds selector + token hash, rotated on
 * every restore). Knows nothing about email/password — that is the application
 * layer (UserModel). The only schema it knows is users (id, role) and
 * user_remember_tokens.
 */
class Auth
{
    private const SESSION_KEY = 'auth_user_id';
    private const COOKIE_NAME = 'score_remember';
    private const COOKIE_TTL = 60 * 60 * 24 * 30; // 30 days

    /** Hierarchy: superuser also passes user-level gates. */
    private const ROLE_RANK = ['user' => 1, 'superuser' => 2];

    private ?array $user = null;
    private bool $userLoaded = false;

    public function __construct(private SqlRepositoryInterface $db) {}

    public function login(int $userId, bool $remember = false): void
    {
        // against session fixation: new session id whenever the user changes
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        Session::set(self::SESSION_KEY, $userId);
        $this->user = null;
        $this->userLoaded = false;

        if ($remember) {
            $this->issueRememberCookie($userId);
        }
    }

    public function logout(): void
    {
        Session::remove(self::SESSION_KEY);
        Session::destroy();
        $this->user = null;
        $this->userLoaded = false;

        $cookie = $_COOKIE[self::COOKIE_NAME] ?? '';
        if ($cookie !== '' && str_contains($cookie, ':')) {
            [$selector] = explode(':', $cookie, 2);
            $this->db->delete('user_remember_tokens', ['selector' => $selector]);
        }
        $this->forgetCookie();
    }

    /** Session OR a valid remember cookie (restores the session, rotating the token). */
    public function check(): bool
    {
        if (Session::get(self::SESSION_KEY)) {
            return true;
        }

        return $this->restoreFromCookie();
    }

    public function id(): ?int
    {
        return $this->check() ? (int) Session::get(self::SESSION_KEY) : null;
    }

    /** Current users row, cached per instance. */
    public function user(): ?array
    {
        if ($this->userLoaded) {
            return $this->user;
        }
        $this->userLoaded = true;

        $id = $this->id();
        $this->user = $id ? ($this->db->fetchOne('SELECT * FROM users WHERE id = :id', ['id' => $id]) ?: null) : null;

        return $this->user;
    }

    public function hasRole(string $required): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        $rank = self::ROLE_RANK[$user['role']] ?? 0;

        return $rank >= (self::ROLE_RANK[$required] ?? PHP_INT_MAX);
    }

    private function restoreFromCookie(): bool
    {
        $cookie = $_COOKIE[self::COOKIE_NAME] ?? '';
        if ($cookie === '' || !str_contains($cookie, ':')) {
            return false;
        }
        [$selector, $validator] = explode(':', $cookie, 2);

        $row = $this->db->fetchOne('SELECT * FROM user_remember_tokens WHERE selector = :s LIMIT 1', ['s' => $selector]);
        if (!$row || strtotime($row['expires_at']) < time() || !hash_equals($row['token_hash'], hash('sha256', $validator))) {
            $this->forgetCookie();

            return false;
        }

        // the token is single-use: rotate right away, even if the cookie was copied/stolen
        $this->db->delete('user_remember_tokens', ['selector' => $selector]);
        Session::set(self::SESSION_KEY, (int) $row['user_id']);
        $this->issueRememberCookie((int) $row['user_id']);

        return true;
    }

    private function issueRememberCookie(int $userId): void
    {
        $selector = $this->randomToken(9);
        $validator = $this->randomToken(33);

        $this->db->insert('user_remember_tokens', [
            'user_id'    => $userId,
            'selector'   => $selector,
            'token_hash' => hash('sha256', $validator),
            'expires_at' => date('Y-m-d H:i:s', time() + self::COOKIE_TTL),
        ]);

        setcookie(self::COOKIE_NAME, $selector . ':' . $validator, [
            'expires'  => time() + self::COOKIE_TTL,
            'path'     => '/',
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE[self::COOKIE_NAME] = $selector . ':' . $validator;
    }

    private function forgetCookie(): void
    {
        setcookie(self::COOKIE_NAME, '', ['expires' => time() - 3600, 'path' => '/']);
        unset($_COOKIE[self::COOKIE_NAME]);
    }

    private function randomToken(int $bytes): string
    {
        return rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');
    }
}
