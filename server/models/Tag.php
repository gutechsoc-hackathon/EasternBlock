<?php
class Tag extends ActiveRecord
{
    protected static $_table = 'tags';
    protected static $_relations = array (
        'events' => array (MANY_MANY, 'Event', 'tag2event_rel', 'from_id', 'to_id'),
        'questions' => array (MANY_MANY, 'Question', 'tag2question_rel', 'from_id', 'to_id'),
    );

    public static function getList ($arr)
    {
        $list = array();
        if (!$arr)
            return $list;
        foreach ($arr as $tag)
            $list[] = trim ($tag->name);
        return $list;
    }

    public static function linkTagsToEvent ($tags, $event)
    {
        $tagIds = Tag::getIds ($tags);
        foreach ($tagIds as $id)
            System::doMysql ("INSERT INTO tag2event_rel VALUES(0, ".$id.", ".$event->id.");");
    }

    public static function linkTagsToQuestion ($tags, $question)
    {
        $tagIds = Tag::getIds ($tags);
        foreach ($tagIds as $id)
            System::doMysql ("INSERT INTO tag2question_rel VALUES(0, ".$id.", ".$question->id.");");
    }

    public static function getIds ($tags)
    {
        $ids = array ();
        foreach ($tags as $t)
        {
            $tag = Tag::find ("seo = '".Validators::getSeo($t)."'");
            if (!empty ($tag))
                $ids[] = $tag[0]->id;
            else
            {
                $tag = new Tag();
                $tag->name = $t;
                $tag->seo = Validators::getSeo($t);
                $tag->save();
                $ids[] = $tag->id;
            }
        }
        return $ids;
    }
}
?>
