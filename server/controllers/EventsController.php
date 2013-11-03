<?php
class EventsController extends Controller
{
    protected static $access = array (
        'default' => array ('guest'),
        'register' => array ('user'),
        'upvote' => array ('user'),
    );
    protected $defaultAction = 'list';
    
    /**
     * get an individual event info
     */
    public function itemAction ()
    {
        $id = Validators::getNum ($_REQUEST['event_id']);
        $event = Event::findByPk ($id);
        $this->ajaxRespond ('event_item', $event->getItemObject ());
    }
    
    /**
     * get a list of all events
     */
    public function listAction ()
    {
        $list = array ();
        $where = '1=1 ORDER BY time_created DESC';
        
        if (isset ($_REQUEST['from']))
        {
            $from = Validators::getDateFormat ($_REQUEST['from']);
            $where = "time_created > '".$from."' ORDER BY time_created DESC";
        }

        foreach (Event::find($where) as $event)
            $list[] = $event->getItemObject();
        $this->ajaxRespond ('events_list', $list);
    }

    /**
     * get list of items within a particular distance
     */
    public function dlistAction ()
    {
        $dist = Validators::getFloat ($_REQUEST['dist']);
        $lat = Validators::getFloat ($_REQUEST['lat']);
        $long = Validators::getFloat ($_REQUEST['long']);

        if (!$dist || !$lat || !$long)
            throw new GameError ('Required fields are missing');

        $list = array ();
        $q = Query::getDistQuery ($dist, $lat, $long);

        if (isset ($_REQUEST['from']))
        {
            $from = Validators::getDateFormat ($_REQUEST['from']);
            $q .= " AND time_created > '".$from."' ORDER BY time_created DESC";
        }

        foreach (Event::find ($q) as $e)
            $list[] = $e->getItemObject();
        $this->ajaxRespond ('smart_locations_list', $list);
    }

    /**
     * register a new event
     */
    public function registerAction ()
    {
        $description = Validators::getMysqlSafe ($_REQUEST['description']);
        $longitude = Validators::getFloat ($_REQUEST['longitude']);
        $latitude = Validators::getFloat ($_REQUEST['latitude']);
        $type_id = Validators::getNum ($_REQUEST['type_id']);
        $tags = array ();
        if ($_REQUEST['tags'])
            $tags = json_decode ($_REQUEST['tags'], true);
        $location_id = Validators::getNum ($_REQUEST['location_id']);
        $question_id = Validators::getNum ($_REQUEST['question_id']);

        if (!$longitude || !$latitude || !$type_id || !$location_id)
            throw new GameError ('Required fields are missing');

        // this would crash if an id is wrong
        Type::findByPk ($type_id);
        Location::findByPk ($location_id);
        if ($question_id)
            Question::findByPk ($question_id);

        // store an event
        $evt = new Event ();
        $evt->description = $description;
        $evt->longitude = $longitude;
        $evt->latitude = $latitude;
        $evt->type_id = $type_id;
        if ($question_id)
            $evt->question_id = $question_id;
        $evt->user_id = System::$user->id; 
        $evt->location_id = $location_id;
        $evt->save();

        // setting an expiration date
        $evt->setExpirationDate ($_REQUEST['expires']);

        // add tags
        Tag::linkTagsToEvent ($tags, $evt);

        $this->ajaxRespond ('event_created', array (
            'event_id' => $evt->id,
        ) );
    }

    /**
     * upvote an event
     * +5 from the person, who asked
     * +1 from the rest
     * +0 for answering own questions
     */
    public function upvoteAction ()
    {
        $evt_id = Validators::getNum ($_REQUEST['event_id']);
        $evt = Event::findByPk ($evt_id);

        $plus = 1;
        if ($evt->question_id && $evt->question->author->id == System::$user->id)
            if ($evt->question->author->id == $evt->author->id)
                $plus = 0;
            else
                $plus = 5;

        $evt->upvoted += $plus;
        $evt->save();
        $evt->author->rating += $plus;
        $evt->author->save();

        $this->ajaxRespond ('upvoted', array (
            'upvotes' => $evt->upvoted,
        ) );
    }
}
?>
