<?php
    class BoardsController extends Controller
    {
        protected static $access = array (
            'default' => array ('guest'),
            'create' => array ('employer'),
            'edit' => array ('employer'),
            'delete' => array ('employer'),
            'accept' => array ('employer'),
            'accept_one' => array ('employer'),
            'decline' => array ('employer'),
            'undo' => array ('employer'),
            'finish' => array ('employer'),
            'create_post' => array ('freelancer'),
            'edit_post' => array ('freelancer'),
            'delete_post' => array ('freelancer'),
            'newcat' => array ('moderator'),
            'editcat' => array ('moderator'),
            'delcat' => array ('moderator'),
        );
        
        /**
         * before everything else
         */
        protected function _beforeAction ()
        {
            // categories will be used a lot
            System::registerConfig ('categories', Category::find ());
        }
        
        /**
         * boards
         */
        public function indexAction ()
        {
            // if the user is employer, only then can it post
            $canPost = (System::$user->employer) ? true : false;
            $this->render ('boards/index', array ('canPost' => $canPost));
        }
        
        /**
         * advertisement
         */
        public function advAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $adv = Work::findByPk ($id);
            $cat = Category::findByPk ($adv->cat_id);
            System::registerConfig ('selectedCat', $cat);
                
            // if the user is the creator, updating last visit time
            if ($adv->empl_id == System::$user->id)
                $adv->last_seen = time ();
                
            // if the user is freelancer and has already posted, then no need for him to post again
            $canPost = (System::$user->freelancer) ? true : false;
            if (Application::find ('work_id = '.$adv->id.' AND frlnc_id = '.System::$user->id) || $adv->status > 0)
                $canPost = false;
                
            // and now displaying the view
            $this->render ('boards/adv', array ('adv' => $adv, 'canPost' => $canPost));
        }
        
        /**
         * category
         */
        public function catAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $cat = Category::findByPk ($id);
            System::registerConfig ('selectedCat', $cat);
            
            // if the user is employer, only then can it post
            $canPost = (System::$user->employer) ? true : false;
                
            $this->render ('boards/cat', array ('category' => $cat, 'canPost' => $canPost));
        }
        
        /**
         * creating work
         */
        public function create_postAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $work = Work::findByPk ($id);
            $cat = Category::findByPk ($work->cat_id);
            System::registerConfig ('selectedCat', $cat);
            
            if (!System::$user->freelancer || Application::find ('work_id = '.$work->id.' AND frlnc_id = '.System::$user->id) || $work->status > 0)
                throw new GameError ('You can not apply to this assignment!');
            
            // creating form
            $form = new CreateApplicationForm ();
            $form->work_id = $id;
            $form->frlnc_id = System::$user->id;
            
            // displaying it if there is no data, or there are errors
            if (!$form->retrieveData () || !$form->validate ())
                $this->render ('boards/create_post', array ('form' => $form, 'work' => $work));
            else 
            {
                // success achieved, now let's save new application
                $form->storeTo ('Application');
                $work->last_reply = time ();
                $this->render ('boards/post_created', array ('category' => $cat, 'work' => $work));
            }
        }
        
        /**
         * editting post
         */
        public function edit_postAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $appl = Application::findByPk ($id);
            $cat = $appl->work->category;
            System::registerConfig ('selectedCat', $cat);
            
            // checking rights
            if ($appl->frlnc_id != System::$user->id && !System::$user->checkAccess ('moderator'))
                throw new GameError ('You do not have permission to do that!');
            
            // creating form
            $form = new CreateApplicationForm ();
            $form->loadFrom ($appl);
            
            // displaying it if there is no data, or there are errors
            if (!$form->retrieveData () || !$form->validate ())
                $this->render ('boards/create_post', array ('form' => $form, 'work' => $appl->work));
            else 
            {
                // success achieved, now let's save new work
                $form->storeTo ('Application');
                $this->render ('boards/post_editted', array ('category' => $cat, 'work' => $appl->work));
            }
        }
        
        /**
         * deleting post
         */
        public function delete_postAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $appl = Application::findByPk ($id);
            $cat = $appl->work->category;
            System::registerConfig ('selectedCat', $cat);
            
            // checking rights
            if ($appl->frlnc_id != System::$user->id && !System::$user->checkAccess ('moderator'))
                throw new GameError ('You do not have permission to do that!');
            
            // deleting associated values if possible
            if ($appl->status != 0 && !System::$user->checkAccess ('moderator'))
                throw new GameError ('This application cannot be deleted!');
            $appl->delete ();
            
            // displaying message
            $this->render ('boards/post_deleted', array ('category' => $cat, 'work' => $appl->work));
        }
        
        /**
         * creating work
         */
        public function createAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $cat = Category::findByPk ($id);
            System::registerConfig ('selectedCat', $cat);
            
            // creating form
            $form = new CreateWorkForm ();
            $form->cat_id = $id;
            $form->empl_id = System::$user->id;
            
            // displaying it if there is no data, or there are errors
            if (!$form->retrieveData () || !$form->validate ())
                $this->render ('boards/create', array ('form' => $form, 'category' => $cat));
            else 
            {
                // success achieved, now let's save new work
                $form->seo = Validators::getSeo ($form->label);
                $form->storeTo ('Work');
                $this->render ('boards/work_created', array ('category' => $cat, 'work' => Work::findByPk (System::lastInsertId ())));
            }
        }
        
        /**
         * editiong work
         */
        public function editAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $work = Work::findByPk ($id);
            $cat = Category::findByPk ($work->cat_id);
            System::registerConfig ('selectedCat', $cat);
            
            // checking rights
            if ($work->empl_id != System::$user->id && !System::$user->checkAccess ('moderator'))
                throw new GameError ('You do not have permission to do that!');
            
            // creating form
            $form = new CreateWorkForm ();
            $form->loadFrom ($work);
            
            // displaying it if there is no data, or there are errors
            if (!$form->retrieveData () || !$form->validate ())
                $this->render ('boards/create', array ('form' => $form, 'category' => $cat));
            else 
            {
                // success achieved, now let's save new work
                $form->seo = Validators::getSeo ($form->label);
                $form->storeTo ('Work');
                $this->render ('boards/work_editted', array ('category' => $cat, 'work' => $work));
            }
        }
        
        /**
         * deleting work
         */
        public function deleteAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $work = Work::findByPk ($id);
            $cat = Category::findByPk ($work->cat_id);
            System::registerConfig ('selectedCat', $cat);
            
            // checking rights
            if ($work->empl_id != System::$user->id && !System::$user->checkAccess ('moderator'))
                throw new GameError ('You do not have permission to do that!');
            
            // deleting associated values if possible
            if ($work->status != 0 || $work->givenApplications || $work->completedApplications)
                throw new GameError ('This work cannot be deleted!');
            if ($work->applications)
                foreach ($work->applications as $appl)
                    $appl->delete ();
            $work->delete ();
            
            // displaying message
            $this->render ('boards/work_deleted', array ('category' => $cat, 'work' => $work));
        }
        
        /**
         * creating category
         */
        public function newcatAction ()
        {
            // creating form
            $form = new CreateCatForm ();
            
            // displaying it if there is no data, or there are errors
            if (!$form->retrieveData () || !$form->validate ())
                $this->render ('boards/newcat', array ('form' => $form));
            else 
            {
                // success achieved, now let's save new category
                $form->seo = Validators::getSeo ($form->label);
                $form->storeTo ('Category');
                $this->render ('boards/cat_created', array ('category' => Category::findByPk (System::lastInsertId ())));
            }
        }
        
        /**
         * editiong category
         */
        public function editcatAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $cat = Category::findByPk ($id);
            
            // creating form
            $form = new CreateCatForm ();
            $form->loadFrom ($cat);
            
            // displaying it if there is no data, or there are errors
            if (!$form->retrieveData () || !$form->validate ())
                $this->render ('boards/newcat', array ('form' => $form));
            else 
            {
                // success achieved, now let's save new category
                $form->seo = Validators::getSeo ($form->label);
                $form->storeTo ('Category');
                $this->render ('boards/cat_editted', array ('category' => $cat));
            }
        }
        
        /**
         * deleting category
         */
        public function delcatAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $cat = Category::findByPk ($id);
            
            // deleting only when category is empty
            if ($cat->works)
                throw new GameError ('This work cannot be deleted, because it is not empty!');
            $cat->delete ();
            
            // displaying message
            $this->render ('boards/cat_deleted', array ('category' => $cat));
        }
        
        /**
         * accept application
         */
        public function acceptAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $appl = Application::findByPk ($id);
            $cat = Category::findByPk ($appl->work->cat_id);
            System::registerConfig ('selectedCat', $cat);
            
            // only author can change status
            if ($appl->work->empl_id != System::$user->id)
                throw new GameError ('You are not authorised to do this action!');
            
            // and now changing 
            $appl->status = 1;
            $appl->worked_from = date ('Y-m-d H:i:s');
            $this->render ('boards/appl_status_editted', array ('category' => $cat, 'work' => $appl->work));
        }
        
        /**
         * accept last application and close hiring
         */
        public function accept_oneAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $appl = Application::findByPk ($id);
            $cat = Category::findByPk ($appl->work->cat_id);
            System::registerConfig ('selectedCat', $cat);
            
            // only author can change status
            if ($appl->work->empl_id != System::$user->id)
                throw new GameError ('You are not authorised to do this action!');
            
            // and now changing 
            $appl->status = 1;
            $appl->worked_from = date ('Y-m-d H:i:s');
            // closing work
            $appl->work->status = 1;
            // displaying
            $this->render ('boards/appl_status_editted', array ('category' => $cat, 'work' => $appl->work));
        }
        
        /**
         * decline application
         */
        public function declineAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $appl = Application::findByPk ($id);
            $cat = Category::findByPk ($appl->work->cat_id);
            System::registerConfig ('selectedCat', $cat);
            
            // only author can change status
            if ($appl->work->empl_id != System::$user->id)
                throw new GameError ('You are not authorised to do this action!');
            
            // and now changing 
            $appl->status = 2;
            $this->render ('boards/appl_status_editted', array ('category' => $cat, 'work' => $appl->work));
        }
        
        /**
         * finish application
         */
        public function finishAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $appl = Application::findByPk ($id);
            $cat = Category::findByPk ($appl->work->cat_id);
            System::registerConfig ('selectedCat', $cat);
            
            // only author can change status
            if ($appl->work->empl_id != System::$user->id)
                throw new GameError ('You are not authorised to do this action!');
                
            // creating form
            $form = new ApplStatusForm ();
            
            // displaying it if there is no data, or there are errors
            if (!$form->retrieveData () || !$form->validate ())
                $this->render ('boards/finish', array ('form' => $form, 'appl' => $appl));
            else 
            {
                // success achieved, now let's save 
                $appl->status = 3;
                $appl->mark = $form->mark;
                $appl->worked_untill = date ('Y-m-d H:i:s');
                $this->render ('boards/appl_status_editted', array ('category' => $cat, 'work' => $appl->work));
            }
        }
        
        /**
         * undo declining application
         */
        public function undoAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $appl = Application::findByPk ($id);
            $cat = Category::findByPk ($appl->work->cat_id);
            System::registerConfig ('selectedCat', $cat);
            
            // only author can change status
            if ($appl->work->empl_id != System::$user->id)
                throw new GameError ('You are not authorised to do this action!');
            
            // and now changing 
            $appl->status = 0;
            $this->render ('boards/appl_status_editted', array ('category' => $cat, 'work' => $appl->work));
        }
    }
?>