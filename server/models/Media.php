<?php
class Media extends ActiveRecord
{
    protected static $_table = 'media';
    protected static $_relations = array (
        'type' => array (BELONGS_TO, 'MediaType', 'media_type_id'),
        'event' => array (BELONGS_TO, 'Event', 'event_id'),
        'author' => array (BELONGS_TO, 'User', 'user_id'),
    );

    public static function getList ($arr)
    {
        $list = array();
        foreach ($arr as $media)
        {
            $list[] = array (
                'id' => $media->id,
                'user_name' => $media->author->name,
                'user_id' => $media->author->id,
                'url' => $media->address,
                'type_id' => $media->media_type_id,
            );
        }
        return $list;
    }
}
?>
