<?php
class UserController extends Controller
{
    protected static $access = array (
        'default' => array ('guest'),
        'track' => array ('user'),
    );
    protected $defaultAction = 'login';
    
    /**
     * check session, if it is still valid
     */
    public function chsessAction ()
    {
        if (System::$user->checkAccess ('user'))
            $this->ajaxSuccess ();
        else
            throw new GameError ('Your session has expired');
    }
    
    /**
     * log in
     */
    public function loginAction ()
    {
        if (System::$user->checkAccess ('user'))
        {
            // if already logged in -- log out
            System::doMysql ("DELETE FROM ".Session::getTableName()." WHERE user_id = ".(System::$user->getPk()).";");
        }

        if (isset ($_REQUEST['email']) && isset ($_REQUEST['pass']))
            System::$user = User::authenticate ($_REQUEST['email'], $_REQUEST['pass']);

        if (System::$user->checkAccess ('user'))
            $this->ajaxRespond ('auth_response', array (
                'sess_id' => System::$user->session->sess_id,
                'name' => System::$user->name,
            ) );
        else
            throw new GameError ('Authorisation failed');
    }

    /**
     * register
     */
    public function registerAction ()
    {
        $email = Validators::getEmailFormat ($_REQUEST['email']);
        $pass = Validators::getPasswordFormat ($_REQUEST['pass']);
        $name = Validators::getMysqlSafe ($_REQUEST['name']);

        if (!$email || !$pass)
            throw new GameError ('All fields are required');
        if (strlen ($pass) < 6)
            throw new GameError ('Password should be at least 6 characters long');
        if (!$name)
            $name = $email;
        if (count (User::find ("email = '".$email."'")) != 0)
            throw new GameError ('Such user already exists');

        $u = new User();
        $u->email = $email;
        $u->pass = md5 ($pass);
        $u->name = $name;
        $u->save();

        // now that's a definition of a hack...
        // shame on me, as I should have thought about that
        // two years ago, while doing the framework...
        $_REQUEST['email'] = $email;
        $_REQUEST['pass'] = $pass;
        $this->loginAction();
    }

    /**
     * log out
     */
    public function logoutAction ()
    {
        System::doMysql ("DELETE FROM ".Session::getTableName()." WHERE user_id = ".(System::$user->getPk()).";");
        $this->ajaxSuccess();
    }

    /**
     * show who are onine
     */
    public function onlineAction ()
    {
        $list = array ();
        foreach (User::getOnline () as $u)
            $list[] = $u->getItemObject ();
        $this->ajaxRespond ('online_users', $list);
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
        $q .= " AND id IN (SELECT user_id FROM ".Session::getTableName()." WHERE last_activity > NOW() - INTERVAL 5 MINUTE);";
        foreach (User::find ($q) as $u)
            $list[] = $u->getItemObject();
        $this->ajaxRespond ('smart_online_list', $list);
    }

    /**
     * store location
     */
    public function trackAction ()
    {
        $lat = Validators::getFloat ($_REQUEST['lat']);
        $long = Validators::getFloat ($_REQUEST['long']);

        if (!$lat || !$long)
            throw new GameError ('Required fields are missing');

        System::$user->latitude = $lat;
        System::$user->longitude = $long;
        System::$user->save ();

        $this->ajaxSuccess ();
    }
}
?>
