<?php
class HeroModel extends BaseModel {

    // ── Heroes ────────────────────────────────────────────
    public function getAll(): array {
        return $this->fetchAll('SELECT * FROM heroes ORDER BY id ASC');
    }
    public function getActive(): array {
        return $this->fetchAll('SELECT * FROM heroes WHERE is_active=1 ORDER BY id ASC');
    }
    public function findById(int $id): ?array {
        return $this->fetch('SELECT * FROM heroes WHERE id=?', [$id]);
    }
    public function create(array $d): string {
        return $this->insert(
            'INSERT INTO heroes(title, subtitle, is_active) VALUES(?,?,?)',
            [$d['title']??null, $d['subtitle']??null, $d['is_active']??1]
        );
    }
    public function update(int $id, array $d): int {
        return $this->execute(
            'UPDATE heroes SET title=?, subtitle=?, is_active=?, updated_at=NOW() WHERE id=?',
            [$d['title']??null, $d['subtitle']??null, $d['is_active']??1, $id]
        );
    }
    public function delete(int $id): int {
        return $this->execute('DELETE FROM heroes WHERE id=?', [$id]);
    }

    // ── Hero Links ────────────────────────────────────────
    public function getLinks(): array {
        return $this->fetchAll('SELECT * FROM hero_link ORDER BY id ASC');
    }
    public function findLink(int $id): ?array {
        return $this->fetch('SELECT * FROM hero_link WHERE id=?', [$id]);
    }
    public function createLink(array $d): string {
        return $this->insert(
            'INSERT INTO hero_link(title, icon_url, link_url) VALUES(?,?,?)',
            [$d['title']??null, $d['icon_url']??null, $d['link_url']??null]
        );
    }
    public function updateLink(int $id, array $d): int {
        return $this->execute(
            'UPDATE hero_link SET title=?, icon_url=?, link_url=?, updated_at=NOW() WHERE id=?',
            [$d['title']??null, $d['icon_url']??null, $d['link_url']??null, $id]
        );
    }
    public function deleteLink(int $id): int {
        return $this->execute('DELETE FROM hero_link WHERE id=?', [$id]);
    }

    // ── Background Images ─────────────────────────────────
    public function getBgImages(int $heroId): array {
        return $this->fetchAll(
            'SELECT * FROM hero_background_images WHERE hero_id=? ORDER BY `order` ASC',
            [$heroId]
        );
    }
    public function addBgImage(int $heroId, string $url, int $order=0, ?string $alt=null): string {
        return $this->insert(
            'INSERT INTO hero_background_images(hero_id, image_url, `order`, alt_text) VALUES(?,?,?,?)',
            [$heroId, $url, $order, $alt]
        );
    }
    public function deleteBgImage(int $id): ?array {
        $row = $this->fetch('SELECT * FROM hero_background_images WHERE id=?', [$id]);
        if ($row) $this->execute('DELETE FROM hero_background_images WHERE id=?', [$id]);
        return $row;
    }
    public function reorderBgImages(array $orders): void {
        foreach ($orders as $item)
            $this->execute('UPDATE hero_background_images SET `order`=? WHERE id=?',
                [(int)$item['order'], (int)$item['id']]);
    }

    // ── Front Image ───────────────────────────────────────
    public function getFrontImage(int $heroId): ?array {
        return $this->fetch('SELECT * FROM hero_front_images WHERE hero_id=?', [$heroId]);
    }
    public function upsertFrontImage(int $heroId, string $url, ?string $alt=null): void {
        $existing = $this->getFrontImage($heroId);
        if ($existing)
            $this->execute(
                'UPDATE hero_front_images SET image_url=?, alt_text=?, uploaded_at=NOW() WHERE hero_id=?',
                [$url, $alt, $heroId]
            );
        else
            $this->execute(
                'INSERT INTO hero_front_images(hero_id, image_url, alt_text) VALUES(?,?,?)',
                [$heroId, $url, $alt]
            );
    }
    public function deleteFrontImage(int $heroId): ?array {
        $row = $this->getFrontImage($heroId);
        if ($row) $this->execute('DELETE FROM hero_front_images WHERE hero_id=?', [$heroId]);
        return $row;
    }
}
