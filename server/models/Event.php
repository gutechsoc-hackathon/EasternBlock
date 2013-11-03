<?php
class Event extends ActiveRecord
{
    protected static $_table = 'events';
    protected static $_relations = array (
        'type' => array (BELONGS_TO, 'Type', 'type_id'),
        'location' => array (BELONGS_TO, 'Location', 'location_id'),
        'media' => array (HAS_MANY, 'Media', 'event_id'),
        'tags' => array (MANY_MANY, 'Tag', 'tag2event_rel', 'to_id', 'from_id'),
        'question' => array (BELONGS_TO, 'Question', 'question_id'),
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
            'description' => $this->description,
            'time_created' => $this->time_created,
            'expires' => $this->expires,
            'longtitude' => $this->longtitude,
            'latitude' => $this->latitude,
            'type' => $this->type_id,
            'tags' => Tag::getList ($this->tags),
            'user_id' => $this->author->id,
            'user_name' => $this->author->name,
            'question_id' => $this->question_id,
            'media' => Media::getList ($this->media),
            'location' => $this->location->getItemObject(),
        );
    }

    /**
     * Sets an expiration date for the event:
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
        System::doMysql ("UPDATE ".Event::getTableName()." SET expires = ".$q." WHERE id=".$this->id.";");
    }

}
?>
