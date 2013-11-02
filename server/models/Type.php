<?php
class Type extends ActiveRecord
{
    protected static $_table = 'types';
    protected static $_relations = array (
        'events' => array (HAS_MANY, 'Event', 'type_id'),
    );
}
?>
