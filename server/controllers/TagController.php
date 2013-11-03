<?php
class TagController extends Controller
{
    protected static $access = array (
        'default' => array ('guest'),
    );
    protected $defaultAction = 'list';
    
    /**
     * get a list of all events
     */
    public function listAction ()
    {
        $list = array ();
        $q = "
            SELECT t.name AS name,
            (select count(*) from tag2event_rel where from_id = t.id)
            +
            (select count(*) from tag2question_rel where from_id = t.id) AS c
            FROM tags AS t
            GROUP BY t.name
            ORDER BY c
            LIMIT 100
        ";

        foreach (System::doMysql($q) as $tag)
        {
            $list[] = array (
                'name' => $tag['name'],
                'count' => $tag['c'],
            );
        }

        $this->ajaxRespond ('events_list', $list);
    }
}
?>
