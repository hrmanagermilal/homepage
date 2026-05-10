<?php
class DepartmentModel extends BaseModel {
    public function getAll(string $type=''): array {
        if($type) return $this->fetchAll('SELECT * FROM departments WHERE department_type=? ORDER BY `order` ASC,id ASC',[$type]);
        return $this->fetchAll('SELECT * FROM departments ORDER BY department_type,`order` ASC,id ASC');
    }
    public function findById(int $id): ?array { return $this->fetch('SELECT * FROM departments WHERE id=?',[$id]); }
    public function create(array $d): string {
        return $this->insert(
            'INSERT INTO departments(department_type,name,description,image,age_group,ministry_type,worship_day,worship_time,worship_location,clergy_name,clergy_position,clergy_phone,`order`,is_active) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [$d['department_type']??'ministry',$d['name'],$d['description']??null,$d['image']??null,$d['age_group']??null,$d['ministry_type']??null,$d['worship_day']??null,$d['worship_time']??null,$d['worship_location']??null,$d['clergy_name']??null,$d['clergy_position']??null,$d['clergy_phone']??null,$d['order']??0,$d['is_active']??1]
        );
    }
    public function update(int $id, array $d): int {
        $f=['department_type=?','name=?','description=?','age_group=?','ministry_type=?','worship_day=?','worship_time=?','worship_location=?','clergy_name=?','clergy_position=?','clergy_phone=?','`order`=?','is_active=?','updated_at=NOW()'];
        $p=[$d['department_type']??'ministry',$d['name'],$d['description']??null,$d['age_group']??null,$d['ministry_type']??null,$d['worship_day']??null,$d['worship_time']??null,$d['worship_location']??null,$d['clergy_name']??null,$d['clergy_position']??null,$d['clergy_phone']??null,$d['order']??0,$d['is_active']??1];
        if(isset($d['image'])){$f[]='image=?';$p[]=$d['image'];}
        $p[]=$id;
        return $this->execute('UPDATE departments SET '.implode(',',$f).' WHERE id=?',$p);
    }
    public function delete(int $id): int { return $this->execute('DELETE FROM departments WHERE id=?',[$id]); }
    public function updateOrder(array $orders): void {
        foreach($orders as $item) $this->execute('UPDATE departments SET `order`=? WHERE id=?',[(int)$item['order'],(int)$item['id']]);
    }
    // 부서 공지
    public function getAnnouncements(int $deptId, int $page=1, int $perPage=ITEMS_PER_PAGE): array {
        $offset=($page-1)*$perPage;
        $rows=$this->fetchAll('SELECT * FROM department_announcements WHERE department_id=? ORDER BY id DESC LIMIT ? OFFSET ?',[$deptId,$perPage,$offset]);
        $total=$this->countQuery('SELECT COUNT(*) FROM department_announcements WHERE department_id=?',[$deptId]);
        return ['rows'=>$rows,'total'=>$total];
    }
    public function findAnnouncement(int $id): ?array { return $this->fetch('SELECT * FROM department_announcements WHERE id=?',[$id]); }
    public function createAnnouncement(int $deptId, array $d): string {
        return $this->insert('INSERT INTO department_announcements(department_id,title,content,link) VALUES(?,?,?,?)',[$deptId,$d['title'],$d['content'],$d['link']??null]);
    }
    public function updateAnnouncement(int $id, array $d): int {
        return $this->execute('UPDATE department_announcements SET title=?,content=?,link=?,updated_at=NOW() WHERE id=?',[$d['title'],$d['content'],$d['link']??null,$id]);
    }
    public function deleteAnnouncement(int $id): int { return $this->execute('DELETE FROM department_announcements WHERE id=?',[$id]); }
}
