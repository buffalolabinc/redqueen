<?php

namespace BLInc\Managers;

class CardManager extends TimestampedManager {
    public function getTable() {
        return 'cards';
    }
}