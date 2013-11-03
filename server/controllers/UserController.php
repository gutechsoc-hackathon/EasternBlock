<?php
class UserController extends Controller
{
    protected static $access = array (
        'default' => array ('guest'),
    );
    protected $defaultAction = 'dashboard';
    
    /**
     * index page is basically a html page.
     */
    public function indexAction ()
    {
        $this->render ('user/index');
    }
    
    /**
     * dashboard
     */
    public function dashboardAction ()
    {
        $this->render ('user/dashboard');
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
}
?>
