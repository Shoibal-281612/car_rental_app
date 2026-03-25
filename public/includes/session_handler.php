<?php
class SessionHandlerDB implements SessionHandlerInterface {
    private $conn;

    public function open($savePath, $sessionName): bool {
        $this->conn = getDbConnection();
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read($id): string|false {
        $stmt = $this->conn->prepare("SELECT session_data FROM sessions WHERE session_id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['session_data'];
        }
        return '';
    }

    public function write($id, $data): bool {
        $stmt = $this->conn->prepare("REPLACE INTO sessions (session_id, session_data, last_accessed) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $id, $data);
        return $stmt->execute();
    }

    public function destroy($id): bool {
        $stmt = $this->conn->prepare("DELETE FROM sessions WHERE session_id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function gc($maxlifetime): int|false {
        $old = time() - $maxlifetime;
        $stmt = $this->conn->prepare("DELETE FROM sessions WHERE last_accessed < FROM_UNIXTIME(?)");
        $stmt->bind_param("i", $old);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}
?>