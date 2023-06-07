<?php

namespace Anax\Users;

/**
 * A controller for users and admin related events.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
    
    
    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize()
    {
        $this->users = new \Anax\Users\User();
        $this->users->setDI($this->di);
    }


    /**
     * Setup the database and add a couple of users
     *
     * @return void
     */
    public function setupAction()
    {

        $this->theme->setTitle("Reset/Setup dB for users");
        $this->db->dropTableIfExists('user')->execute();

        $this->db->createTable(
            'user',
            [
                'id'       => ['integer', 'primary key', 'not null', 'auto_increment'],
                'acronym'  => ['varchar(20)', 'unique', 'not null'],
                'email'    => ['varchar(80)'],
                'name'     => ['varchar(80)'],
                'password' => ['varchar(255)'],
                'created'  => ['datetime'],
                'updated'  => ['datetime'],
                'deleted'  => ['datetime'],
                'active'   => ['datetime'],
            ]
        )->execute();
        
        $this->db->insert(
            'user',
            ['acronym', 'email', 'name', 'password', 'created', 'active']
        );

        $now = gmdate('Y-m-d H:i:s');

        $this->db->execute(['admin', 'admin@dbwebb.se', 'Administrator', password_hash('admin', PASSWORD_DEFAULT), $now, $now]);
        $this->db->execute(['doe', 'doe@dbwebb.se', 'John Doe', password_hash('doe', PASSWORD_DEFAULT), $now, $now]);
        $this->db->execute(['smith', 'smith@dbwebb.se', 'John Smith', password_hash('smith', PASSWORD_DEFAULT), $now, $now]);

        $this->response->redirect($this->url->create('questions/setup/'));
    }
    
    
    
    /**
     * List all users.
     *
     * @return void
     */
    public function listAction()
    {
        $all = $this->users->findAll();

        $this->theme->setTitle("List all users");
        $this->views->add('users/view-all', [
            'users' => $all,
            'title' => "View all users",
            'subtitle' => "Subtitle",
        ]);
    }


    /**
     * Get user from id
     *
     * @return string $user
     */
    public function getUserAction($id)
    {
        $user = $this->users->find($id);
        return $user;
    }


    /**
     * List user with id.
     *
     * @param int $id of user to display
     *
     * @return void
     */
    public function idAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $user = $this->users->find($id);
        $user->gravatar = '<img class="avatar" src="http://www.gravatar.com/avatar/' . md5(strtolower(trim($user->email))) . '">';

        $this->theme->setTitle("View user with id $id");
        $this->views->add('users/view', [
            'users' => $user,
            'title' => "View user with id $id",
        ]);

        $questions = $this->QuestionsController->getQuestionsBy($id);
        $this->views->add('questions/questions-list', [
            'list' => $questions,
            'title' => "Questions asked by $user->name",
        ]);
        $answers   = $this->AnswersController->getAnswersBy($id);
        $this->views->add('questions/answers-list', [
            'list' => $answers,
            'title' => "Answers given by $user->name",
        ]);
        $comments   = $this->CommentsController->getCommentsBy($id);
        $this->views->add('questions/comments-list', [
            'list' => $comments,
            'title' => "Comments by $user->name",
        ]);
    }
    
    
    /**
     * Add new user.
     *
     * @return void
     */
    public function addAction()
    {
        $form = new \Mata16\Form\CFormAddUser();
        $form->setDI($this->di);
        $status = $form->check();

        $this->theme->setTitle("Add user");
        $this->di->views->add('users/userForm', [
            'title' => "Användare",
            'subtitle' => "Lägg till användare",
            'content' => $form->getHTML(),
        ]);
    }


    /**
     * Edit existing user.
     *
     * @param integer $id of user to delete.
     *
     * @return void
     */
    public function editAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }
        
        $user = $this->users->find($id);
        
        $form = new \Mata16\Form\CFormEditUser($user);
        $form->setDI($this->di);
        $status = $form->check();

        $this->theme->setTitle("Edit user");
        $this->di->views->add('users/userForm', [
            'title' => "Editera användare",
            'content' => $form->getHTML(),
        ]);
    }
    
    
    /**
     * Delete user.
     *
     * @param integer $id of user to delete.
     *
     * @return void
     */
    public function deleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $res = $this->users->delete($id);

        $url = $this->url->create('users');
        $this->response->redirect($url);
    }
    
    
    /**
     * Delete (soft) user.
     *
     * @param integer $id of user to delete.
     *
     * @return void
     */
    public function softDeleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $now = gmdate('Y-m-d H:i:s');

        $user = $this->users->find($id);

        $user->deleted = $now;
        $user->save();

        $url = $this->url->create('users/id/' . $id);
        $this->response->redirect($url);
    }


    /**
     * Undo soft-delete for a user.
     *
     * @param integer $id of user.
     *
     * @return void
     */
    public function undoSoftDeleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $user = $this->users->find($id);

        $user->deleted = null;
        $user->save();

        $url = $this->url->create('users/id/' . $id);
        $this->response->redirect($url);
    }


    /**
     * List all active and not deleted users.
     *
     * @return void
     */
    public function activeAction()
    {
        $all = $this->users->query()
            ->where('active IS NOT NULL')
            ->andWhere('deleted is NULL')
            ->execute();

        $this->theme->setTitle("Users that are active");
        $this->views->add('users/view-all-no-edit', [
            'users' => $all,
            'title' => "Users that are active",
        ]);
    }


    /**
     * List all inactive users.
     *
     * @return void
     */
    public function inactiveAction()
    {
        $all = $this->users->query()
            ->where('active is NULL')
            ->execute();
        $this->theme->setTitle("Users that are inactive");
        $this->views->add('users/view-all-no-edit', [
            'users' => $all,
            'title' => "Users that are inactive",
        ]);
    }


    /**
     * List all soft-deleted users.
     *
     * @return void
     */
    public function trashAction()
    {
        $all = $this->users->query()
            ->where('deleted IS NOT NULL')
            ->execute();

        $this->theme->setTitle("Users that are soft-deleted");
        $this->views->add('users/view-all-soft-delete', [
            'users' => $all,
            'title' => "Users that are soft-deleted",
        ]);
    }


    /**
     * Login user
     *
     * @return void
     */
    public function loginAction()
    {
        $this->session();
        $this->theme->setTitle("Log in");

        $form = $this->di->form->create([], [
            'user' => [
                'type'       => 'text',
                'label'      => 'Acronym:',
                'required'   => true,
                'validation' => ['not_empty'],
            ],
            'password' => [
                'type'       => 'password',
                'label'      => 'Password:',
                'required'   => true,
                'validation' => ['not_empty'],
            ],
            'submit' => [
                'type'      => 'submit',
                'value'     => 'Log In',
                'callback'  => function () {
                    $now = gmdate('Y-m-d H:i:s');

                    $all = $this->users->query()
                        ->where('acronym is "' . $this->form->Value('user') . '"')
                        ->andWhere('deleted is NULL')
                        ->andWhere('active is not NULL')
                        ->execute();

                    if (count($all) == 1) {
                        $this->loggedInUser = $all[0];
                        return password_verify($this->form->Value('password'), $all[0]->password);
                    }

                    return false;
                }
            ],
        ]);
        $form->check([$this, 'onLoginSuccess'], [$this, 'onLoginFail']);
        $this->di->views->add('default/page', [
            'title' => "Log in",
            'content' => $form->getHTML()
        ]);
    }


    /**
     * What to do when login is successful
     *
     * @param  object $form The loginform
     * @return void
     */
    public function onLoginSuccess($form)
    {
        $this->session->set('login_id', $this->loggedInUser->id);
        $this->session->set('login_acronym', $this->loggedInUser->acronym);
        $form->AddOutput("<p><i>Successfully logged in " . $this->loggedInUser->acronym . "</i></p>");
        $url = $this->di->request->getCurrentUrl();
        $this->response->redirect($url);
    }


    /**
     * What to do when login is not successful
     *
     * @param  object $form The loginform
     * @return void
     */
    public function onLoginFail($form)
    {
        $form->AddOutput("<p><i>Failed to log in</i></p>");
        $url = $this->di->request->getCurrentUrl();
        $this->response->redirect($url);
    }

    /**
     * Logout user
     *
     * @return void
     */
    public function logoutAction()
    {
        $this->session->set('login_id', null);
        $this->session->set('login_acronym', null);
        $url = $this->url->create('');
        $this->response->redirect($url);
    }


    /**
     * Get id and acronym for logged in user
     *
     * @return array with $id and $acronym
     */
    public function getLoggedInUserAction()
    {
        $id = $this->session->get('login_id', null);
        if ($id == null) {
            return null;
        }
        $acronym = $this->session->get('login_acronym', null);
        return [$id, $acronym];
    }


    /**
     * Get the most active users
     *
     * @return array with most active users
     */
    public function mostActiveUsersAction()
    {
        $users = $this->users->findAll();
        foreach ($users as $u) {
            $answers     = $this->AnswersController->getAnswersBy($u->id);
            $questions   = $this->QuestionsController->getQuestionsBy($u->id);
            $comments    = $this->CommentsController->getCommentsBy($u->id);
            $u->numPosts = count($answers) + count($questions) + count($comments);
        }
        usort($users, array($this, "cmp"));
        $users = array_slice($users, 0, 5);
        return $users;
    }


    /**
     * Sort Active users by most active
     *
     * @param  object $a User1
     * @param  object $b User2
     * @return sorted users
     */
    private function cmp($a, $b)
    {
        if ($a->numPosts == $b->numPosts) {
            return 0;
        }
        return ($a->numPosts > $b->numPosts) ? -1 : 1;
    }
}
