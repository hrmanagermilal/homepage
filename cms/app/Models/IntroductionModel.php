<?php
class IntroductionModel extends BaseModel {

    /* ── Vision Statements ─────────────────────────────── */
    public function getVisions(): array {
        return $this->fetchAll('SELECT * FROM vision_statements ORDER BY `order` ASC, id ASC');
    }
    public function findVision(int $id): ?array {
        return $this->fetch('SELECT * FROM vision_statements WHERE id=?', [$id]);
    }
    public function createVision(array $d): string {
        return $this->insert(
            'INSERT INTO vision_statements(title,title_en,points,points_en,`order`,is_active) VALUES(?,?,?,?,?,?)',
            [$d['title'], $d['title_en']??null, $d['points']??null, $d['points_en']??null, $d['order']??0, $d['is_active']??1]
        );
    }
    public function updateVision(int $id, array $d): int {
        return $this->execute(
            'UPDATE vision_statements SET title=?,title_en=?,points=?,points_en=?,`order`=?,is_active=?,updated_at=NOW() WHERE id=?',
            [$d['title'], $d['title_en']??null, $d['points']??null, $d['points_en']??null, $d['order']??0, $d['is_active']??1, $id]
        );
    }
    public function deleteVision(int $id): int {
        return $this->execute('DELETE FROM vision_statements WHERE id=?', [$id]);
    }
    public function reorderVisions(array $orders): void {
        foreach ($orders as $item) {
            $this->execute('UPDATE vision_statements SET `order`=? WHERE id=?', [(int)$item['order'], (int)$item['id']]);
        }
    }

    /* ── Section Titles ────────────────────────────────── */
    public function getSectionTitles(): array {
        return $this->fetchAll('SELECT * FROM section_titles ORDER BY id ASC');
    }
    public function findSectionTitle(int $id): ?array {
        return $this->fetch('SELECT * FROM section_titles WHERE id=?', [$id]);
    }
    public function createSectionTitle(array $d): string {
        return $this->insert(
            'INSERT INTO section_titles(category,title,subtitle) VALUES(?,?,?)',
            [$d['category'], $d['title'], $d['subtitle']??null]
        );
    }
    public function updateSectionTitle(int $id, array $d): int {
        return $this->execute(
            'UPDATE section_titles SET category=?,title=?,subtitle=?,updated_at=NOW() WHERE id=?',
            [$d['category'], $d['title'], $d['subtitle']??null, $id]
        );
    }
    public function deleteSectionTitle(int $id): int {
        return $this->execute('DELETE FROM section_titles WHERE id=?', [$id]);
    }

    /* ── Pastor Introduction ───────────────────────────── */
    public function getPastor(): ?array {
        return $this->fetch('SELECT * FROM pastor_introduction ORDER BY id ASC LIMIT 1');
    }
    public function upsertPastor(array $d): void {
        $existing = $this->getPastor();
        $fields = ['photo_alt_ko','photo_alt_en','title_line1_ko','title_line2_ko','title_line1_en','title_line2_en',
                   'paragraphs_ko','paragraphs_en','pastor_role_ko','pastor_role_en','pastor_name_ko','pastor_name_en',
                   'career_title_ko','career_title_en','career_ko','career_en','is_active'];
        $vals = array_map(fn($f) => $d[$f] ?? null, $fields);
        if ($existing) {
            $sets = implode(',', array_map(fn($f) => "$f=?", $fields));
            // photo_image는 값이 있을 때만 업데이트
            if (isset($d['photo_image'])) { $sets .= ',photo_image=?'; $vals[] = $d['photo_image']; }
            $this->execute("UPDATE pastor_introduction SET $sets,updated_at=NOW() WHERE id=?", array_merge($vals, [$existing['id']]));
        } else {
            if (isset($d['photo_image'])) { $fields[] = 'photo_image'; $vals[] = $d['photo_image']; }
            $cols = implode(',', $fields);
            $phs  = implode(',', array_fill(0, count($fields), '?'));
            $this->insert("INSERT INTO pastor_introduction($cols) VALUES($phs)", $vals);
        }
    }

    /* ── Together Items ────────────────────────────────── */
    public function getTogetherItems(): array {
        return $this->fetchAll('SELECT * FROM together_items ORDER BY `order` ASC, id ASC');
    }
    public function findTogether(int $id): ?array {
        return $this->fetch('SELECT * FROM together_items WHERE id=?', [$id]);
    }
    public function createTogether(array $d): string {
        return $this->insert(
            'INSERT INTO together_items(title,description,image,link,`order`,is_active) VALUES(?,?,?,?,?,?)',
            [$d['title'], $d['description']??null, $d['image']??null, $d['link']??null, $d['order']??0, $d['is_active']??1]
        );
    }
    public function updateTogether(int $id, array $d): int {
        $fields = ['title=?','description=?','link=?','`order`=?','is_active=?','updated_at=NOW()'];
        $params = [$d['title'], $d['description']??null, $d['link']??null, $d['order']??0, $d['is_active']??1];
        if (isset($d['image'])) { $fields[] = 'image=?'; $params[] = $d['image']; }
        $params[] = $id;
        return $this->execute('UPDATE together_items SET ' . implode(',', $fields) . ' WHERE id=?', $params);
    }
    public function deleteTogether(int $id): int {
        return $this->execute('DELETE FROM together_items WHERE id=?', [$id]);
    }
    public function reorderTogether(array $orders): void {
        foreach ($orders as $item) {
            $this->execute('UPDATE together_items SET `order`=? WHERE id=?', [(int)$item['order'], (int)$item['id']]);
        }
    }
}
