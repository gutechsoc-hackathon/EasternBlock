<?php
class IndexController extends Controller
{
    protected static $access = array (
        'default' => array ('guest'),
    );
    protected $defaultAction = 'index';

    /**
     * project introduction page
     */
    public function indexAction ()
    {
        $this->render ('index/index');
    }
}
?>
