<?php
class Location extends ActiveRecord
{
    protected static $_table = 'locations';
    protected static $_relations = array (
        'events' => array (HAS_MANY, 'Event', 'location_id'),
        'questions' => array (HAS_MANY, 'Question', 'location_id'),
    );

    public function getItemObject()
    {
        return array (
            'id' => $this->id,
            'name' => $this->name,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
        );
    }
}
?>
