<?php
class EventsController extends Controller
{
    protected static $access = array (
        'default' => array ('guest'),
        'register' => array ('user'),
    );
    protected $defaultAction = 'item';
    
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
        foreach (Event::find() as $event)
            $list[] = $event->getItemObject();
        $this->ajaxRespond ('events_list', $list);
    }

    /**
     * register a new event
     */
    public function registerAction ()
    {
        $description = Validators::getMysqlSafe ($_REQUEST['description']);
        $longtitude = Validators::getFloat ($_REQUEST['longtitude']);
        $latitude = Validators::getFloat ($_REQUEST['latitude']);
        $type_id = Validators::getNum ($_REQUEST['type_id']);
        $tags = array ();
        if ($_REQUEST['tags'])
            $tags = json_decode ($_REQUEST['tags'], true);
        $location_id = Validators::getNum ($_REQUEST['location_id']);
        $question_id = Validators::getNum ($_REQUEST['question_id']);

        if (!$longtitude || !$latitude || !$type_id || !$location_id)
            throw new GameError ('Required fields are missing');

        // this would crash if an id is wrong
        Type::findByPk ($type_id);
        Location::findByPk ($location_id);
        if ($question_id)
            Question::findByPk ($question_id);

        // store an event
        $evt = new Event ();
        $evt->description = $description;
        $evt->longtitude = $longtitude;
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
}
?>