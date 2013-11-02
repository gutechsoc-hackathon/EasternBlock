<?php
class Media extends ActiveRecord
{
    protected static $_table = 'media';
    protected static $_relations = array (
        'type' => array (BELONGS_TO, 'MediaType', 'media_type_id'),
        'event' => array (BELONGS_TO, 'Event', 'event_id'),
        'author' => array (BELONGS_TO, 'User', 'user_id'),
    );
}
?>
