<?php
class MediaController extends Controller
{
    protected static $access = array (
        'default' => array ('guest'),
        'upload' => array ('user'),
    );
    protected $defaultAction = 'item';
    
    /**
     * get an individual media info
     */
    public function itemAction ()
    {
        $id = Validators::getNum ($_REQUEST['media_id']);
        $media = Media::findByPk ($id);
        $this->ajaxRespond ('media_item', $media->getItemObject ());
    }
    
    /**
     * get a list of all media for an event
     */
    public function listAction ()
    {
        $evt_id = Validators::getNum ($_REQUEST['event_id']);
        $evt = Event::findByPk ($evt_id);
        $this->ajaxRespond ('locations_list', Media::getList ($evt->media));
    }

    /**
     * Save the uploaded action 
     */
    public function uploadAction ()
    {
        $uf = $_FILES['file'];

        if ($uf['error'])
            throw new GameError ($uf['error']);

        $allowed = array ('image/jpeg', 'image/jpg', 'image/jp_', 'application/jpg', 'application/x-jpg', 'image/pjpeg', 'image/pipeg', 'image/vnd.swiftview-jpeg', 'image/x-xbitmap', 'image/png', 'application/png', 'application/x-png' );
        if (!in_array ($uf['type'], $allowed)) 
            throw new GameError ('Forbidden file format');

        if ($uf['size'] > 10000000)
            throw new GameError ('File size exceeded 10 MB');

        $type = Validators::getNum ($_REQUEST['type_id']);
        $type = MediaType::findByPk ($type);
                
        $evt = Validators::getNum ($_REQUEST['event_id']);
        $evt = Event::findByPk ($evt);
                
        $file = substr (md5 (microtime ()), 0, 5).substr ($uf['tmp_name'], strrpos ($uf['tmp_name'], '/') + 1);

        move_uploaded_file ($uf['tmp_name'], BASE_DIR.'uploads/'.$file);
        $file = WEB_ROOT.'uploads/'.$file;

        $media = new Media ();
        $media->user_id = System::$user->id;
        $media->url = $file;
        $media->type_id = $type->id;
        $media->event_id = $evt->id;
        $media->save();

        $this->ajaxSuccess ();
    }
}
?>
