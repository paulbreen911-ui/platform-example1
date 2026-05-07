<?php
// PostgreSQL-backed session handler for Railway
// Include this before session_start() in config.php

class PgSessionHandler implements SessionHandlerInterface {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function open($path, $name): bool {
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read($id): string {
        $stmt = $this->pdo->prepare('SELECT session_data FROM sessions WHERE session_id = ? AND last_activity > ?');
        $stmt->execute([$id, time() - (int)ini_get('session.gc_maxlifetime')]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['session_data'] : '';
    }

    public function write($id, $data): bool {
        $stmt = $this->pdo->prepare('
            INSERT INTO sessions (session_id, session_data, last_activity)
            VALUES (?, ?, ?)
            ON CONFLICT (session_id) DO UPDATE
            SET session_data = EXCLUDED.session_data,
                last_activity = EXCLUDED.last_activity
        ');
        return $stmt->execute([$id, $data, time()]);
    }

    public function destroy($id): bool {
        $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE session_id = ?');
        return $stmt->execute([$id]);
    }

    public function gc($maxlifetime): int|false {
        $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE last_activity < ?');
        $stmt->execute([time() - $maxlifetime]);
        return $stmt->rowCount();
    }
}
