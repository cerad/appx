<?php
namespace Cerad\Module\RefereeModule;


class RefereeRepository
{
  private $db;
  
  public function __construct($db)
  {
    $this->db = $db;
  }
  public function findOne($id)
  {
    if (!$id) return null;
    
    $sql = 'SELECT * FROM referees WHERE id = ?;';
    $stmt = $this->db->executeQuery($sql,[$id]);
    $rows = $stmt->fetchAll();
    return (count($rows) == 1) ? $rows[0] : null;
  }
  public function findAll()
  {
    $sql = 'SELECT * FROM referees LIMIT ?;';
    $stmt = $this->db->executeQuery($sql,[3]); //,[\PDO::PARAM_INT]);
    return $stmt->fetchAll();
  }
}