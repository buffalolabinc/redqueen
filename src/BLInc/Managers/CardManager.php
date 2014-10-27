<?php

namespace BLInc\Managers;

class CardManager extends TimestampedManager
{

    public function getTable()
    {
        return 'cards';
    }

    public function find($id)
    {
        $result = parent::find($id);

        unset($result['pin']);

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

}