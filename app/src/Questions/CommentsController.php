<?php

namespace Anax\Questions;

/**
 * A controller for users and admin related events.
 *
 */
class CommentsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    private $page;

    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize()
    {
        $this->comments = new \Anax\Comments\Comment();
        $this->comments->setDI($this->di);
    }


    /**
     * Setup the database and add a couple of comments
     *
     * @return void
     */
    public function setupAction()
    {

        $this->theme->setTitle("Reset/Setup dB for comments");
        $this->db->dropTableIfExists('comment')->execute();

        $this->db->createTable(
            'comment',
            [
                'id'         => ['integer', 'primary key', 'not null', 'auto_increment'],
                'questionid' => ['integer'],
                'answerid'   => ['integer'],
                'userid'     => ['integer'],
                'content'    => ['varchar(1024)'],
                'points'     => ['integer'],
                'created'  => ['datetime'],
            ]
        )->execute();

        $this->db->insert(
            'comment',
            ['questionid', 'answerid', 'userid', 'content', 'points', 'created']
        );

        $now = gmdate('Y-m-d H:i:s');

        $this->db->execute([1, null, 3, 'Question comment #1', 0, $now]);
        $this->db->execute([null, 1, 3, 'Answer comment #1', 0, $now]);
        $this->db->execute([null, 2, 3, 'Answer comment #2', 0, $now]);
        $this->db->execute([null, 2, 3, 'Answer comment #3', 0, $now]);
        $this->db->execute([null, 6, 3, 'Answer comment #4', 0, $now]);
    }


    /**
     * Get comments connected to a question
     *
     * @param  int   $questionId the id of the question
     * @return array $comments   the comments connected to question $questionId
     */
    public function getQuestionComments($questionId)
    {
        $comments = $this->comments->query()
            ->where("questionid IS '$questionId'")
            ->execute();

        return $comments;
    }


    /**
     * Get comments connected to an answer
     *
     * @param  int    $answerId the id of the answer
     * @return array  $comments the comments connected to answer $answerId
     */
    public function getAnswerComments($answerId)
    {
        $comments = $this->comments->query()
            ->where("answerid IS '$answerId'")
            ->execute();

        return $comments;
    }


    /**
     * Add a comment to a question
     *
     * @param  int $questionId the id of the question to comment on
     * @return void
     */
    public function commentQuestionAction($questionId)
    {
        $this->session();
        $user = $this->di->dispatcher->forward([
            'controller' => 'users',
            'action'     => 'getLoggedInUser',
        ]);

        if ($user == null) {
            $url = $this->url->create('users/login');
            $this->response->redirect($url);
            return;
        }

        $form = $this->di->form->create([], [
            'comment' => [
                'type' => 'textarea',
                'label' => 'Comment:',
                'required' => true,
                'validation' => ['not_empty']
            ],
            'questionid' => [
                'type' => 'hidden',
                'value' => $questionId
            ],
            'submit' => [
                'type' => 'submit',
                'callback' => function () {
                    $now = gmdate('Y-m-d H:i:s');
                    $user = $this->di->dispatcher->forward([
                        'controller' => 'users',
                        'action'     => 'getLoggedInUser',
                    ]);

                    $res = $this->comments->save([
                        'questionid' => $this->form->Value('questionid'),
                        'answerid' => null,
                        'userid' => $user[0],
                        'content' => $this->form->Value('comment'),
                        'points ' => 0,
                        'created' => $now,
                    ]);
                    $this->response->redirect($this->url->create('questions/view/' .  $this->form->Value('questionid')));
                    return true;
                }
            ],
        ]);
        $form->check();
        $this->di->views->add('default/page', [
            'title'   => "Comment on a question",
            'content' => $form->getHTML()
        ]);
    }


    /**
     * Add a comment to an answer
     *
     * @param  int $answerId the id of the answer to comment on
     * @return void
     */
    public function commentAnswerAction($answerId, $questionId)
    {
        $user = $this->di->dispatcher->forward([
            'controller' => 'users',
            'action'     => 'getLoggedInUser',
        ]);

        if ($user == null) {
            $url = $this->url->create('users/login');
            $this->response->redirect($url);
            return;
        }

        $form = $this->di->form->create([], [
            'comment' => [
                'type' => 'textarea',
                'label' => 'Comment:',
                'required' => true,
                'validation' => ['not_empty']
            ],
            'answerid' => [
                'type' => 'hidden',
                'value' => $answerId
            ],
            'questionid' => [
                'type' => 'hidden',
                'value' => $questionId
            ],
            'submit' => [
                'type' => 'submit',
                'callback' => function () {
                    $now = gmdate('Y-m-d H:i:s');
                    $user = $this->di->dispatcher->forward([
                        'controller' => 'users',
                        'action'     => 'getLoggedInUser',
                    ]);

                    $res = $this->comments->save([
                        'questionid' => null,
                        'answerid' => $this->form->Value('answerid'),
                        'userid' => $user[0],
                        'content' => $this->form->Value('comment'),
                        'points ' => 0,
                        'created' => $now,
                    ]);
                    $this->response->redirect($this->url->create('questions/view/' .  $this->form->Value('questionid')));
                    return true;
                }
            ],
        ]);
        $form->check();
        $this->di->views->add('default/page', [
            'title'   => "Comment on an answer",
            'content' => $form->getHTML()
        ]);
    }


    /**
     * Get comments written by a specific user
     *
     * @param  int    $userId   [description]
     * @return object $comments [description]
     */
    public function getCommentsBy($userId)
    {
        if (!isset($this->comments)) {
            $this->initialize();
        }

        $comments = $this->comments->query()
            ->where('userid is ' . $userId)
            ->execute();
        return $comments;
    }







/******************************************************************************************************
*************************** OLD COMMENT CONTROLLER - STUFF TO REUSE? **********************************
******************************************************************************************************/



    /**
     * Show Form
     *
     * @return void
     */
    public function showAction($page)
    {
        $this->page = $page;
        $form = $this->form->create([], [
            'comment' => [
                'type'        => 'textarea',
                'label'       => 'Comment',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'name' => [
                'type'        => 'text',
                'label'       => 'Name',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'homepage' => [
                'type'        => 'text',
                'label'       => 'Homepage',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'email' => [
                'type'        => 'text',
                'label'       => 'Email',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
            ],
            'submit' => [
                'type'      => 'submit',
                'value'     => 'Comment',
                'callback'  => function () {
                    $now = gmdate('Y-m-d H:i');
                    $this->comments->save([
                        'comment' => $this->form->Value('comment'),
                        'name' => $this->form->Value('name'),
                        'homepage' => $this->form->Value('homepage'),
                        'email' => $this->form->Value('email'),
                        'page' => $this->page,
                        'timestamp' => $now,
                    ]);
                    $this->response->redirect($this->url->create(''));
                    return true;
                }
            ],
        ]);
        $status = $form->check();
        $this->views->add('default/page', [
            'title' => "Skriv en kommentar",
            'content' => $form->getHTML()
        ]);
    }


    /**
     * View all comments for a page
     *
     * @return void
     */
    public function viewAction($page = null)
    {
        $all = $this->comments->query()
            ->where("page IS '$page'")
            ->execute();
        $this->views->add('comment/comments2', [
            'comments' => $all,
            'page'     => $page,
        ]);
    }
    
    
    /**
     * Delete all comments on a page
     *
     * @return void
     */
    public function deleteallAction($page = null)
    {
        $this->comments->deletePage($page);
        $url = $this->url->create('');
        $this->response->redirect($url);
    }


    /**
     * Edit a specific comment
     *
     * @return void
     */
    public function editAction($id)
    {
        $this->theme->setTitle("Edit comment");
        $comment = $this->comments->find($id);

        $form = $this->form->create([], [
            'comment' => [
                'type'        => 'textarea',
                'label'       => 'Comment',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value'       => $comment->comment,
            ],
            'name' => [
                'type'        => 'text',
                'label'       => 'Name',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value'       => $comment->name,
            ],
            'homepage' => [
                'type'        => 'text',
                'label'       => 'Homepage',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value'       => $comment->homepage,
            ],
            'email' => [
                'type'        => 'text',
                'label'       => 'Email',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
                'value'       => $comment->email,
            ],
            'submit' => [
                'type'      => 'submit',
                'value'     => 'Spara',
                'callback'  => function () {
                    $now = gmdate('Y-m-d H:i:s');
                    $this->comments->update([
                        'id' => $this->comments->id,
                        'comment' => $this->form->Value('comment'),
                        'name' => $this->form->Value('name'),
                        'homepage' => $this->form->Value('homepage'),
                        'email' => $this->form->Value('email'),
                    ]);
                    $this->response->redirect($this->url->create(''));
                    return true;
                }
            ],
            'delete' => [
                'type'     => 'submit',
                'value'    => 'Ta bort',
                'callback' => function () {
                    $this->comments->delete($this->comments->id);
                    $this->response->redirect($this->url->create(''));
                    return true; 
                }
            ],
        ]);
                               
        $status = $form->check();    
        $this->views->add('default/page', [
            'title' => "Editera din kommentar",
            'content' => $form->getHTML()      
                                    
        ]);
    }
}
