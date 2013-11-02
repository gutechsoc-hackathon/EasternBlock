<?php
class Tag extends ActiveRecord
{
    protected static $_table = 'tags';
    protected static $_relations = array (
        'events' => array (MANY_MANY, 'Event', 'tag2event_rel', 'from_id', 'to_id'),
        'questions' => array (MANY_MANY, 'Question', 'tag2question_rel', 'from_id', 'to_id'),
    );
}
?>
