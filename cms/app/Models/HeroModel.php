<?php
class HeroModel extends BaseModel {
    
    // ── 히어로(배너) - heroes 테이블 없이 bg/front 테이블 직접 사용 ──
    
    // 가상의 "히어로 목록" — 실제로는 배경이미지 그룹이 없으므로
    // 단일 히어로(id=1)만 사용하는 구조로 단순화
    public function getAll(): array {
        $bg    = $this->fetchAll('SELECT * FROM hero_background_images ORDER BY `order` ASC');
        $front = $this->fetch('SELECT * FROM hero_front_images ORDER BY id DESC LIMIT 1');
        return [[
            'id'        => 1,
            'title'     => '메인 히어로',
            'subtitle'  => '',
            'is_active' => 1,
        ]];
    }
    
    public function findById(int $id): ?array {
        // id=1 고정
        return ['id' => 1, 'title' => '메인 히어로', 'subtitle' => '', 'is_active' => 1];
    }
    
    public function create(array $d): string {
        // DB 구조상 heroes 테이블 없음 — 생성 불필요, 항상 1 반환
        return '1';
    }
    
    public function update(int $id, array $d): int {
        // 제목/부제목 저장할 테이블 없음 — 스킵
        return 1;
    }
    
    public function delete(int $id): int {
        // 연결 이미지 삭제만 처리 (Controller에서 직접)
        return 1;
    }
    
    // ── Hero Links (quick_links 테이블 활용) ──────────────────────────
    public function getLinks(): array {
        return $this->fetchAll('SELECT * FROM quick_links ORDER BY id ASC');
    }
    
    public function findLink(int $id): ?array {
        return $this->fetch('SELECT * FROM quick_links WHERE id=?', [$id]);
    }
    
    public function createLink(array $d): string {
        return $this->insert(
            'INSERT INTO quick_links(title, image, link) VALUES(?,?,?)',
            [$d['title'] ?? null, $d['icon_url'] ?? null, $d['link_url'] ?? null]
            );
    }
    
    public function updateLink(int $id, array $d): int {
        return $this->execute(
            'UPDATE quick_links SET title=?, image=?, link=?, updated_at=NOW() WHERE id=?',
            [$d['title'] ?? null, $d['icon_url'] ?? null, $d['link_url'] ?? null, $id]
            );
    }
    
    public function deleteLink(int $id): int {
        return $this->execute('DELETE FROM quick_links WHERE id=?', [$id]);
    }
    
    // ── Background Images ─────────────────────────────────────────────
    public function getBgImages(int $heroId): array {
        // hero_id 컬럼 없음 — 전체 반환
        return $this->fetchAll('SELECT * FROM hero_background_images ORDER BY `order` ASC');
    }
    
    public function addBgImage(int $heroId, string $url, int $order = 0, ?string $alt = null): string {
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
            $this->execute(
                'UPDATE hero_background_images SET `order`=? WHERE id=?',
                [(int)$item['order'], (int)$item['id']]
                );
    }
    
    // ── Front Image ───────────────────────────────────────────────────
    public function getFrontImage(int $heroId): ?array {
        // hero_id 컬럼 없음 — 최신 1개 반환
        return $this->fetch('SELECT * FROM hero_front_images ORDER BY id DESC LIMIT 1');
    }
    
    public function upsertFrontImage(int $heroId, string $url, ?string $alt = null): void {
        $existing = $this->getFrontImage($heroId);
        if ($existing) {
            $this->execute(
                'UPDATE hero_front_images SET image_url=?, alt_text=?, uploaded_at=NOW() WHERE id=?',
                [$url, $alt, $existing['id']]
                );
        } else {
            $this->execute(
                'INSERT INTO hero_front_images(image_url, alt_text) VALUES(?,?)',
                [$url, $alt]
                );
        }
    }
    
    public function deleteFrontImage(int $heroId): ?array {
        $row = $this->getFrontImage($heroId);
        if ($row) $this->execute('DELETE FROM hero_front_images WHERE id=?', [$row['id']]);
        return $row;
    }
}
