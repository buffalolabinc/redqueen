<?php

namespace BLInc\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Unique extends Constraint {

    public $table;
    public $column;

    public $message = 'This value is a duplicate of an existing value.';

    public function getRequiredOptions() {
        return array('table', 'column');
    }
}