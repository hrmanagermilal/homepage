<?php
class RoleModel extends BaseModel {
    public function getAll(): array { return $this->fetchAll('SELECT * FROM roles ORDER BY id ASC'); }
    public function findById(int $id): ?array { return $this->fetch('SELECT * FROM roles WHERE id=?',[$id]); }
    public function getAllPermissions(): array { return $this->fetchAll('SELECT * FROM permissions ORDER BY module,action'); }
    public function getPermissionsGrouped(): array {
        $rows=[]; foreach($this->getAllPermissions() as $r) $rows[$r['module']][]=$r;
        return $rows;
    }
    public function getPermissionsByRole(int $roleId): array {
        return array_column($this->fetchAll('SELECT permission_id FROM role_permissions WHERE role_id=?',[$roleId]),'permission_id');
    }
    public function create(array $d): string {
        $id=$this->insert('INSERT INTO roles(name,slug,description) VALUES(?,?,?)',[$d['name'],$d['slug'],$d['description']??null]);
        if(!empty($d['permissions'])) $this->syncPerms((int)$id,$d['permissions']);
        return $id;
    }
    public function update(int $id, array $d): int {
        $r=$this->execute('UPDATE roles SET name=?,slug=?,description=?,updated_at=NOW() WHERE id=?',[$d['name'],$d['slug'],$d['description']??null,$id]);
        $this->syncPerms($id,$d['permissions']??[]);
        return $r;
    }
    public function delete(int $id): int { return $this->execute('DELETE FROM roles WHERE id=?',[$id]); }
    private function syncPerms(int $roleId, array $ids): void {
        $this->execute('DELETE FROM role_permissions WHERE role_id=?',[$roleId]);
        if(empty($ids)) return;
        $ph=implode(',',array_fill(0,count($ids),'(?,?)'));
        $p=[]; foreach($ids as $pid){$p[]=$roleId;$p[]=(int)$pid;}
        $this->execute("INSERT INTO role_permissions(role_id,permission_id) VALUES $ph",$p);
    }
    public function slugExists(string $slug, int $ex=0): bool {
        return (bool)$this->fetch('SELECT id FROM roles WHERE slug=? AND id!=?',[$slug,$ex]);
    }
}
