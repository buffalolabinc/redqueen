<?php

class Cards extends \Phalcon\Mvc\Model
{
    /*
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false
     */
    protected $id;

    /*
     * @Column(type="integer", nullale=false)
     */
    protected $member_id;

    /*
     * @Column(type="string", length=6)
     */
    protected $code;

    /*
     * @Column(type="string", length=32)
     */
    protected $pin;

    /*
     * @Column(type="string")
     */
    protected $created_at;

    /*
     * @Column(type="string")
     */
    protected $updated_at;

    public function getSource() {
        return "cards";
    }

    public function initialize() {
        $this->belongsTo("member_id", "Members", "id");
        $this->hasMany("id", "Logs", "card_id");
    }

    /*
     * @TODO I should be replaced
     */
    private function dateFormat($date) {
        if ($date instanceof \DateTime) {
            return $date->format('Y-m-d H:i:s P');
        } else {
            return new \Datetime($date);
        }
    }

    public function getLastLog() {
        $last_log = $this->getLogs(array('order' => 'logged_at DESC', 'limit' => 1));

        if (count($last_log)) {
            return $last_log->getFirst();
        }

        return null;
    }

    public function getId() {
        return $this->id;
    }

    public function getMemberId() {
        return $this->member_id;
    }

    public function setMemberId($id) {
        $this->member_id = $id;
        return $this;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    public function getPin() {
        return $this->pin;
    }

    public function setPin($pin) {
        $this->pin = $pin;
        return $this;
    }

    public function getCreatedAt() {
        return $this->dateFormat($this->created_at);
    }

    public function setCreatedAt($date) {
        $this->created_at = $this->dateFormat($date);

        return $this;
    }

    public function getUpdatedAt() {
        return $this->dateFormat($this->updated_at);
    }

    public function setUpdatedAt($date) {
        $this->updated_at = $this->dateFormat($date);

        return $this;
    }
}
