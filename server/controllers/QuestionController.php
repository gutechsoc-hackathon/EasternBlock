<?php
class QuestionController extends Controller
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
        $id = Validators::getNum ($_REQUEST['question_id']);
        $question = Question::findByPk ($id);
        $this->ajaxRespond ('question_item', $question->getItemObject ());
    }
    
    /**
     * get a list of all events
     */
    public function listAction ()
    {
        $list = array ();
        foreach (Question::find() as $question)
            $list[] = $question->getItemObject();
        $this->ajaxRespond ('questions_list', $list);
    }

    /**
     * register a new event
     */
    public function registerAction ()
    {
        $question = Validators::getMysqlSafe ($_REQUEST['question']);
        $longtitude = Validators::getFloat ($_REQUEST['longtitude']);
        $latitude = Validators::getFloat ($_REQUEST['latitude']);
        $tags = array ();
        if ($_REQUEST['tags'])
            $tags = json_decode ($_REQUEST['tags'], true);
        $location_id = Validators::getNum ($_REQUEST['location_id']);

        if (!$longtitude || !$latitude || !$location_id)
            throw new GameError ('Required fields are missing');

        // this would crash if an id is wrong
        Location::findByPk ($location_id);

        // store a question
        $q = new Question ();
        $q->question = $question;
        $q->longtitude = $longtitude;
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