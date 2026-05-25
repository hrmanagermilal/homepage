<?php
class MinistryModel extends BaseModel {

    public function getAll(): array {
        return $this->fetchAll('SELECT * FROM ministry ORDER BY `order` ASC, id ASC');
    }

    public function findById(int $id): ?array {
        return $this->fetch('SELECT * FROM ministry WHERE id=?', [$id]);
    }

    public function findByKey(string $key): ?array {
        return $this->fetch('SELECT * FROM ministry WHERE `key`=?', [$key]);
    }

    public function create(array $d): string {
        return $this->insert(
            'INSERT INTO ministry(`key`,name,subtitle,title,image,description,points,
             notice_title,notice_description,notice_button_label,notice_button_href,notice_button_external,
             cta_label,cta_href,cta_external,`order`,is_active) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [$d['key'], $d['name'], $d['subtitle']??null, $d['title']??null, $d['image']??null,
             $d['description']??null, $d['points']??null,
             $d['notice_title']??null, $d['notice_description']??null, $d['notice_button_label']??null,
             $d['notice_button_href']??null, $d['notice_button_external']??0,
             $d['cta_label']??null, $d['cta_href']??null, $d['cta_external']??0,
             $d['order']??0, $d['is_active']??1]
        );
    }

    public function update(int $id, array $d): int {
        $f = ['name=?','subtitle=?','title=?','description=?','points=?',
              'notice_title=?','notice_description=?','notice_button_label=?','notice_button_href=?','notice_button_external=?',
              'cta_label=?','cta_href=?','cta_external=?','`order`=?','is_active=?','updated_at=NOW()'];
        $p = [$d['name'], $d['subtitle']??null, $d['title']??null, $d['description']??null, $d['points']??null,
              $d['notice_title']??null, $d['notice_description']??null, $d['notice_button_label']??null,
              $d['notice_button_href']??null, $d['notice_button_external']??0,
              $d['cta_label']??null, $d['cta_href']??null, $d['cta_external']??0,
              $d['order']??0, $d['is_active']??1];
        if (isset($d['image'])) { $f[] = 'image=?'; $p[] = $d['image']; }
        $p[] = $id;
        return $this->execute('UPDATE ministry SET ' . implode(',', $f) . ' WHERE id=?', $p);
    }

    public function delete(int $id): int {
        return $this->execute('DELETE FROM ministry WHERE id=?', [$id]);
    }

    public function reorder(array $orders): void {
        foreach ($orders as $item) {
            $this->execute('UPDATE ministry SET `order`=? WHERE id=?', [(int)$item['order'], (int)$item['id']]);
        }
    }
}
