<?php
class QuestionController extends Controller
{
    protected static $access = array (
        'default' => array ('guest'),
        'register' => array ('user'),
    );
    protected $defaultAction = 'list';
    
    /**
     * get an individual question info
     */
    public function itemAction ()
    {
        $id = Validators::getNum ($_REQUEST['question_id']);
        $question = Question::findByPk ($id);
        $this->ajaxRespond ('question_item', $question->getItemObject ());
    }
    
    /**
     * get a list of all questions
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

        foreach (Question::find ($where) as $question)
            $list[] = $question->getItemObject();
        $this->ajaxRespond ('questions_list', $list);
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

        foreach (Question::find ($q) as $q)
            $list[] = $q->getItemObject();
        $this->ajaxRespond ('smart_questions_list', $list);
    }

    /**
     * register a new question
     */
    public function registerAction ()
    {
        $question = Validators::getMysqlSafe ($_REQUEST['question']);
        $longitude = Validators::getFloat ($_REQUEST['longitude']);
        $latitude = Validators::getFloat ($_REQUEST['latitude']);
        $tags = array ();
        if ($_REQUEST['tags'])
            $tags = explode (',', Validators::getMysqlSafe ($_REQUEST['tags']));
        $location_id = Validators::getNum ($_REQUEST['location_id']);

        if (!$longitude || !$latitude || !$location_id)
            throw new GameError ('Required fields are missing');

        // this would crash if an id is wrong
        Location::findByPk ($location_id);

        // store a question
        $q = new Question ();
        $q->question = $question;
        $q->longitude = $longitude;
        $q->latitude = $latitude;
        $q->user_id = System::$user->id; 
        $q->location_id = $location_id;
        $q->save();

        // setting an expiration date
        $q->setExpirationDate ($_REQUEST['expires']);

        // add tags
        Tag::linkTagsToQuestion ($tags, $q);

        $this->ajaxRespond ('question_created', array (
            'question_id' => $q->id,
        ) );
    }
}
?>
