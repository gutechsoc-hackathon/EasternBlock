<?php
class LocationController extends Controller
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
        $this->render ('events/item');
    }
    
    /**
     * get a list of all events
     */
    public function listAction ()
    {
        $this->render ('events/list');
    }

    /**
     * register a new event
     */
    public function registerAction ()
    {
        $name = Validators::getMysqlSafe ($_REQUEST['name']);
        $longtitude = Validators::getFloat ($_REQUEST['longtitude']);
        $latitude = Validators::getFloat ($_REQUEST['latitude']);

        if (!$longtitude || !$latitude || !$name)
            throw new GameError ('Required fields are missing');

        // store a location
        $loc = new Location ();
        $loc->name = $name;
        $loc->seo = Validators::getSeo ($name);
        $loc->longtitude = $longtitude;
        $loc->latitude = $latitude;
        $loc->save();

        $this->ajaxRespond ('location_created', array (
            'location_id' => $loc->id,
        ) );
    }

    public function getExpires ($expires)
    {
        $q = '';
        switch ($expires)
        {
            case '3': $q = 'NOW() + INTERVAL 30 DAY'; break;
            case '2': $q = 'NOW() + INTERVAL 7 DAY'; break;
            case '1': $q = 'NOW() + INTERVAL 24 HOUR'; break;
            default: $q = 'NOW() + INTERVAL 1 HOUR'; break;
        }
        System::doMysql ("UPDATE ".Event::getTableName()." SET expires = '".$q."' WHERE id=".$this->id.";");
    }
}
?>
