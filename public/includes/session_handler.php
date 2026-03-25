<?php
class SessionHandlerDB implements SessionHandlerInterface {
    private $conn;

    public function open($savePath, $sessionName) {
        $this->conn = getDbConnection();
        return true;
    }

    public function close() {
        return true;
    }

    public function read($id) {
        $stmt = $this->conn->prepare("SELECT session_data FROM sessions WHERE session_id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['session_data'];
        }
        return '';
    }

    public function write($id, $data) {
        $stmt = $this->conn->prepare("REPLACE INTO sessions (session_id, session_data, last_accessed) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $id, $data);
        return $stmt->execute();
    }

    public function destroy($id) {
        $stmt = $this->conn->prepare("DELETE FROM sessions WHERE session_id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function gc($maxlifetime) {
        $old = time() - $maxlifetime;
        $stmt = $this->conn->prepare("DELETE FROM sessions WHERE last_accessed < FROM_UNIXTIME(?)");
        $stmt->bind_param("i", $old);
        return $stmt->execute();
    }
}

// Helper to get the database connection (reuse from config.php)
if (!function_exists('getDbConnection')) {
    function getDbConnection() {
        global $conn;
        return $conn;
    }
}