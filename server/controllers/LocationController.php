<?php
class LocationController extends Controller
{
    protected static $access = array (
        'default' => array ('guest'),
        'register' => array ('user'),
    );
    protected $defaultAction = 'item';
    
    /**
     * get an individual location info
     */
    public function itemAction ()
    {
        $id = Validators::getNum ($_REQUEST['location_id']);
        $location = Location::findByPk ($id);
        $this->ajaxRespond ('location_item', $location->getItemObject ());
    }
    
    /**
     * get a list of all locations
     */
    public function listAction ()
    {
        $list = array ();
        foreach (Location::find () as $loc)
            $list[] = $loc->getItemObject();
        $this->ajaxRespond ('locations_list', $list);
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
        foreach (Location::find ($q) as $l)
            $list[] = $l->getItemObject();
        $this->ajaxRespond ('smart_locations_list', $list);
    }

    /**
     * register a new location
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
