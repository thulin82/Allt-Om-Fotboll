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
     * @param  int    $userId   the id of the user
     * @return object $comments all comments given by $user
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
}
