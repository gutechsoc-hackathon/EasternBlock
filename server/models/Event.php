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
}
?>
