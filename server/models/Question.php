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
}
?>
