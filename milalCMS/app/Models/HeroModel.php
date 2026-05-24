<?php
class HeroModel extends BaseModel {

    // ── Background Images ─────────────────────────────────
    public function getBgImages(): array {
        return $this->fetchAll('SELECT * FROM hero_background_images ORDER BY `order` ASC');
    }
    public function addBgImage(string $url, int $order = 0, ?string $alt = null): string {
        return $this->insert(
            'INSERT INTO hero_background_images(image_url, `order`, alt_text) VALUES(?,?,?)',
            [$url, $order, $alt]
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
    public function getFrontImage(): ?array {
        return $this->fetch('SELECT * FROM hero_front_images LIMIT 1');
    }
    public function upsertFrontImage(string $url, ?string $alt = null): void {
        $existing = $this->getFrontImage();
        if ($existing)
            $this->execute(
                'UPDATE hero_front_images SET image_url=?, alt_text=?, uploaded_at=NOW() WHERE id=?',
                [$url, $alt, $existing['id']]
            );
        else
            $this->execute(
                'INSERT INTO hero_front_images(image_url, alt_text) VALUES(?,?)',
                [$url, $alt]
            );
    }
    public function deleteFrontImage(): ?array {
        $row = $this->getFrontImage();
        if ($row) $this->execute('DELETE FROM hero_front_images WHERE id=?', [$row['id']]);
        return $row;
    }

    // ── Quick Links ───────────────────────────────────────
    public function getLinks(): array {
        return $this->fetchAll('SELECT * FROM quick_links ORDER BY id ASC');
    }
    public function findLink(int $id): ?array {
        return $this->fetch('SELECT * FROM quick_links WHERE id=?', [$id]);
    }
    public function createLink(array $d): string {
        return $this->insert(
            'INSERT INTO quick_links(title, link, image, `desc`) VALUES(?,?,?,?)',
            [$d['title'] ?? null, $d['link'] ?? null, $d['image'] ?? null, $d['desc'] ?? null]
        );
    }
    public function updateLink(int $id, array $d): int {
        return $this->execute(
            'UPDATE quick_links SET title=?, link=?, image=?, `desc`=? WHERE id=?',
            [$d['title'] ?? null, $d['link'] ?? null, $d['image'] ?? null, $d['desc'] ?? null, $id]
        );
    }
    public function deleteLink(int $id): int {
        return $this->execute('DELETE FROM quick_links WHERE id=?', [$id]);
    }
}
