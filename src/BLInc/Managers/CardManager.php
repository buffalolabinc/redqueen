<?php

namespace BLInc\Managers;

class CardManager extends TimestampedManager
{

    public function getTable()
    {
        return 'cards';
    }

    public function transformRow(array $data)
    {
        $data['isActive'] = (bool)$data['isActive'];

        return $data;
    }

    public function update($id, array $data)
    {
        if (isset($data['schedules'])) {
            $schedules = $data['schedules'];
            unset($data['schedules']);

            $scheduleIds = [];
            foreach($schedules as $schedule) {
                $scheduleIds[] = $schedule['id'];
            }

            $currentScheduleIds = $this->getScheduleIds($id);

            $schedulesToRemove = array_diff($currentScheduleIds, $scheduleIds);

            foreach ($schedulesToRemove as $removeId) {
                $this->removeSchedule($id, $removeId);
            }

            $schedulesToAdd = array_diff($scheduleIds, $currentScheduleIds);

            foreach ($schedulesToAdd as $addId) {
                $this->addSchedule($id, $addId);
            }
        }

        parent::update($id, $data);
    }

    public function create(array $data)
    {
        if (isset($data['schedules'])) {
            $schedules = $data['schedules'];
            unset($data['schedules']);
        }

        $id = parent::create($data);

        if (isset($schedules)) {
            foreach ($schedules as $schedule) {
                $this->addSchedule($id, $schedule['id']);
            }
        }

        return $id;
    }

    public function delete($id)
    {
        $this->dbal->delete('card_schedules', ['card_id' => $id]);
        parent::delete($id);
    }

    public function find($id)
    {
        $result = parent::find($id);

        unset($result['pin']);

        $result['schedules'] = $this->getScheduleIds($id);

        return $result;
    }

    public function findAll()
    {
        $results = parent::findAll();

        return array_map(function($card) {
            unset($card['pin']);

            return $card;
        }, $results);
    }

    protected function getScheduleIds($id)
    {
        $query = 'SELECT schedule_id FROM card_schedule WHERE card_id = :cardId';

        $rows = $this->dbal->fetchAll($query, array('cardId' => $id));

        $ids = array();
        foreach($rows as $row) {
            $ids[] = $row['schedule_id'];
        }

        return $ids;
    }

    public function addSchedule($id, $scheduleId)
    {
        $scheduleIds = $this->getScheduleIds($id);

        if (false === in_array($scheduleId, $scheduleIds)) {
            $this->dbal->insert('card_schedule', array(
                'card_id' => $id,
                'schedule_id' => $scheduleId,
            ));
        }
    }

    public function removeSchedule($id, $scheduleId)
    {
        $scheduleIds = $this->getScheduleIds($id);

        if (in_array($scheduleId, $scheduleIds)) {
            $this->dbal->delete('card_schedule', array(
                'card_id' => $id,
                'schedule_id' => $scheduleId,
            ));
        }
    }
}
