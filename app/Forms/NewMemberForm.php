<?php

use \Phalcon\Validation\Validator\PresenceOf;

class NewMemberForm extends MemberForm {

    public function initialize(Members $member, $options, $edit = false) {
        parent::initialize($member, $options, $edit);

        $email = $this->get('email');
        $email->addValidator(new DuplicateDatabaseMemberValidator('email'));

        $password = $this->get('password');
        $password->addValidator(new PresenceOf(array(
            'message' => 'Password is required'
        )));

        $passwordConfirm = $this->get('password_confirm');
        $passwordConfirm->addValidator(new PresenceOf(array(
            'message' => 'Password is required'
        )));
    }
}
