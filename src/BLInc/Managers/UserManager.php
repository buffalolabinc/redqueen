<?php

namespace BLInc\Managers;

class UserManager extends TimestampedManager {
    public function getTable() {
        return 'users';
    }
}