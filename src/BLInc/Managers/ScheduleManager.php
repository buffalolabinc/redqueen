<?php

namespace BLInc\Managers;

use Doctrine\DBAL\Connection;

class ScheduleManager extends TimestampedManager
{

  public function findByCard($cardId)
  {
    $query = 'SELECT s.*,cs.card_id FROM schedules s LEFT JOIN card_schedule cs ON (s.id = cs.schedule_id) WHERE cs.card_id = :cardId';
    return array_map([$this, 'transformRow'], $this->dbal->fetchAll($query, array('cardId' => $cardId)));
  }

  public function findByCards(array $cardIds)
  {
    $query = 'SELECT s.*,cs.card_id FROM schedules s LEFT JOIN card_schedule cs ON (s.id = cs.schedule_id) WHERE cs.card_id IN (:cardIds)';
    return array_map([$this, 'transformRow'], $this->dbal->fetchAll($query, array('cardIds' => $cardIds), ['cardIds' => Connection::PARAM_INT_ARRAY]));
  }

  protected function transformRow(array $data)
  {
    $data['mon'] = (bool)$data['mon'];
    $data['tue'] = (bool)$data['tue'];
    $data['wed'] = (bool)$data['wed'];
    $data['thu'] = (bool)$data['thu'];
    $data['fri'] = (bool)$data['fri'];
    $data['sat'] = (bool)$data['sat'];
    $data['sun'] = (bool)$data['sun'];

    return $data;
  }

  public function getTable()
  {
    return 'schedules';
  }
}
