<?php
class Question extends ActiveRecord
{
    protected static $_table = 'questions';
    protected static $_relations = array (
        'location' => array (BELONGS_TO, 'Location', 'location_id'),
        'tags' => array (MANY_MANY, 'Tag', 'tag2question_rel', 'to_id', 'from_id'),
        'answers' => array (HAS_MANY, 'Event', 'question_id'),
        'author' => array (BELONGS_TO, 'User', 'user_id'),
    );

    /**
     * object representation of the things, that are
     * sent to the client
     */
    public function getItemObject()
    {
        return array (
            'id' => $this->id,
            'question' => $this->question,
            'time_created' => $this->time_created,
            'expires' => $this->expires,
            'longtitude' => $this->longtitude,
            'latitude' => $this->latitude,
            'tags' => Tag::getList ($this->tags),
            'user_id' => $this->author->id,
            'user_name' => $this->author->name,
            'location' => $this->location->getItemObject(),
            'answer_count' => count ($this->answers),
        );
    }

    /**
     * Sets an expiration date for the question:
     * 0 -> 1h
     * 1 -> 24h
     * 2 -> 7d
     * 3 -> 30d
     */
    public function setExpirationDate ($expires)
    {
        $q = '';
        switch ($expires)
        {
            case '3': $q = 'time_created + INTERVAL 30 DAY'; break;
            case '2': $q = 'time_created + INTERVAL 7 DAY'; break;
            case '1': $q = 'time_created + INTERVAL 24 HOUR'; break;
            default: $q = 'time_created + INTERVAL 1 HOUR'; break;
        }
        System::doMysql ("UPDATE ".Question::getTableName()." SET expires = ".$q." WHERE id=".$this->id.";");
    }
}
?>
