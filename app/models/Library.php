<?php

class Library {

    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getLibrary($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                li.id,
                li.item_type,
                li.item_id,
                li.metadata,
                li.is_pinned,
                li.created_at,
                CASE 
                    WHEN li.item_type = 'playlist' THEN p.name
                    ELSE JSON_UNQUOTE(JSON_EXTRACT(li.metadata, '$.name'))
                END as name,
                CASE 
                    WHEN li.item_type = 'playlist' THEN p.image_url
                    ELSE JSON_UNQUOTE(JSON_EXTRACT(li.metadata, '$.image'))
                END as image,
                p.is_default
            FROM library_items li
            LEFT JOIN playlists p ON li.item_type = 'playlist' AND li.item_id = p.id
            WHERE li.user_id = ?
            ORDER BY li.is_pinned DESC, li.created_at DESC
        ");

        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addToLibrary($userId, $type, $itemId, $metadata = [])
    {
        $stmt = $this->db->prepare("
            INSERT INTO library_items 
            (user_id, item_type, item_id, metadata, is_pinned) 
            VALUES (?, ?, ?, ?, ?)
        ");

        $isPinned = ($type === 'playlist' && isset($metadata['is_default']))
            ? 1 : 0;

        $stmt->execute([
            $userId,
            $type,
            $itemId,
            json_encode($metadata),
            $isPinned
        ]);

        return [
            'id' => $this->db->lastInsertId(),
            'user_id' => $userId,
            'item_type' => $type,
            'item_id' => $itemId,
            'metadata' => $metadata,
            'is_pinned' => $isPinned
        ];
    }

    public function removeFromLibrary($userId, $itemId)
    {
        $stmt = $this->db->prepare("
        DELETE FROM library_items 
        WHERE user_id = ? AND id = ?
    ");
        return $stmt->execute([$userId, $itemId]);
    }

    public function togglePinLibraryItem($userId, $itemId, $pinned)
    {
        $stmt = $this->db->prepare("
        UPDATE library_items 
        SET is_pinned = ?
        WHERE user_id = ? AND id = ?
    ");
        return $stmt->execute([$pinned ? 1 : 0, $userId, $itemId]);
    }

}