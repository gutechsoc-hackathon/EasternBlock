<?php
class MediaType extends ActiveRecord
{
    protected static $_table = 'media_types';
    protected static $_relations = array (
        'events' => array (HAS_MANY, 'Event', 'media_type_id'),
    );
}
?>
