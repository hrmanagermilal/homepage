<?php
class CmsModel extends BaseModel {
    public function getPages(): array { return $this->fetchAll('SELECT * FROM pages ORDER BY id ASC'); }
    public function findPage(int $id): ?array { return $this->fetch('SELECT * FROM pages WHERE id=?',[$id]); }
    public function findPageBySlug(string $slug): ?array { return $this->fetch('SELECT * FROM pages WHERE slug=? AND is_active=1',[$slug]); }
    public function createPage(array $d): string {
        return $this->insert('INSERT INTO pages(name,slug,description,is_active) VALUES(?,?,?,?)',[$d['name'],$d['slug'],$d['description']??null,$d['is_active']??1]);
    }
    public function updatePage(int $id, array $d): int {
        return $this->execute('UPDATE pages SET name=?,slug=?,description=?,is_active=?,updated_at=NOW() WHERE id=?',[$d['name'],$d['slug'],$d['description']??null,$d['is_active']??1,$id]);
    }
    public function deletePage(int $id): int { return $this->execute('DELETE FROM pages WHERE id=?',[$id]); }
    public function pageSlugExists(string $slug, int $ex=0): bool {
        return (bool)$this->fetch('SELECT id FROM pages WHERE slug=? AND id!=?',[$slug,$ex]);
    }
    public function getSectionsByPage(int $pageId): array {
        return $this->fetchAll('SELECT * FROM sections WHERE page_id=? ORDER BY sort_order ASC,id ASC',[$pageId]);
    }
    public function findSection(int $id): ?array { return $this->fetch('SELECT * FROM sections WHERE id=?',[$id]); }
    public function createSection(array $d): string {
        return $this->insert('INSERT INTO sections(page_id,name,slug,description,sort_order,is_active) VALUES(?,?,?,?,?,?)',
            [$d['page_id'],$d['name'],$d['slug'],$d['description']??null,$d['sort_order']??0,$d['is_active']??1]);
    }
    public function updateSection(int $id, array $d): int {
        return $this->execute('UPDATE sections SET name=?,slug=?,description=?,sort_order=?,is_active=?,updated_at=NOW() WHERE id=?',
            [$d['name'],$d['slug'],$d['description']??null,$d['sort_order']??0,$d['is_active']??1,$id]);
    }
    public function deleteSection(int $id): int { return $this->execute('DELETE FROM sections WHERE id=?',[$id]); }
    public function sectionSlugExists(int $pageId, string $slug, int $ex=0): bool {
        return (bool)$this->fetch('SELECT id FROM sections WHERE page_id=? AND slug=? AND id!=?',[$pageId,$slug,$ex]);
    }
    public function getTextsBySection(int $sectionId): array {
        return $this->fetchAll('SELECT * FROM texts WHERE section_id=? ORDER BY sort_order ASC,id ASC',[$sectionId]);
    }
    public function findText(int $id): ?array { return $this->fetch('SELECT * FROM texts WHERE id=?',[$id]); }
    public function createText(array $d): string {
        return $this->insert('INSERT INTO texts(section_id,key_name,content_ko,content_en,type,sort_order) VALUES(?,?,?,?,?,?)',
            [$d['section_id'],$d['key_name'],$d['content_ko']??'',$d['content_en']??'',$d['type']??'text',$d['sort_order']??0]);
    }
    public function updateText(int $id, array $d): int {
        return $this->execute('UPDATE texts SET key_name=?,content_ko=?,content_en=?,type=?,sort_order=?,updated_at=NOW() WHERE id=?',
            [$d['key_name'],$d['content_ko']??'',$d['content_en']??'',$d['type']??'text',$d['sort_order']??0,$id]);
    }
    public function deleteText(int $id): int { return $this->execute('DELETE FROM texts WHERE id=?',[$id]); }
    public function textKeyExists(int $sectionId, string $key, int $ex=0): bool {
        return (bool)$this->fetch('SELECT id FROM texts WHERE section_id=? AND key_name=? AND id!=?',[$sectionId,$key,$ex]);
    }
    public function getTextsForFrontend(string $pageSlug, string $sectionSlug, string $lang='ko'): array {
        $lang=in_array($lang,['ko','en'])?$lang:'ko';
        $col="content_{$lang}";
        $rows=$this->fetchAll("SELECT t.key_name,t.{$col} AS content FROM texts t JOIN sections s ON s.id=t.section_id JOIN pages p ON p.id=s.page_id WHERE p.slug=? AND s.slug=? AND p.is_active=1 AND s.is_active=1 ORDER BY t.sort_order ASC",[$pageSlug,$sectionSlug]);
        $r=[];foreach($rows as $row)$r[$row['key_name']]=$row['content'];
        return $r;
    }
}
