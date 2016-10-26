<?php

namespace Anax\Questions;

/**
 * A controller for questions
 *
 */
class QuestionsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;


    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize()
    {
        $this->questions = new \Anax\Questions\Question();
        $this->questions->setDI($this->di);
    }


    /**
     * Setup the database and add a couple of question
     *
     * @return void
     */
    public function setupAction()
    {

        $this->theme->setTitle("Reset/Setup dB for questions");
        $this->db->dropTableIfExists('question')->execute();

        $this->db->createTable(
            'question',
            [
                'id'      => ['integer', 'primary key', 'not null', 'auto_increment'],
                'topic'   => ['varchar(80)'],
                'text'    => ['varchar(1024)'],
                'userid'  => ['integer'],
                'points'  => ['integer'],
                'created' => ['datetime'],
                'updated' => ['datetime'],
                'deleted' => ['datetime'],
                'active'  => ['datetime'],
            ]
        )->execute();
        
        $this->db->insert(
            'question',
            ['topic', 'text', 'userid', 'points', 'created', 'updated', 'deleted', 'active']
        );

        $now = gmdate('Y-m-d H:i:s');

        $this->db->execute([
            'Who is the greatest footballer?',
            'I wonder, could it be Zlatan?',
            '1',
            '0',
            $now,
            $now,
            null,
            $now
        ]);

        $this->db->execute([
            'Who is the worst footballer?',
            'I wonder, could it be Mikael Lustig?',
            '2',
            '0',
            $now,
            $now,
            null,
            $now
        ]);

    }


    /**
     * Show questions
     *
     * @return void
     */
    public function showAction()
    {   
        $questions = $this->questions->query()->execute();

        foreach ($questions as $q) {
            $user = $this->dispatcher->forward([
                'controller' => 'users',
                'action'     => 'getUser',
                'params'     => ['id' => $q->userid]
            ]);
            $q->userAcronym = $user->acronym;

            $answers = $this->dispatcher->forward([
                'controller' => 'answers',
                'action'     => 'getAnswers',
                'params'     => ['questionId' => $q->id]
            ]);
            $q->numAnswers = count($answers);

            $tags = $this->dispatcher->forward([
                'controller' => 'tags',
                'action'     => 'getTagsConnectedToQuestion',
                'params'     => ['questionId' => $q->id]
            ]);
            $q->numTags = count($tags);
        }

        $this->views->add('questions/basic', [
            'questions' => $questions,
            'title'     => 'All questions'
            ], 'main');
    }


    /**
     * Show a question
     *
     * @param  int $id id of the question
     * @return void
     */
    public function viewAction($id)
    {
        $question = $this->questions->find($id);
        $user = $this->dispatcher->forward([
                'controller' => 'users',
                'action'     => 'getUser',
                'params'     => ['id' => $question->userid]
            ]);

        $question->userAcronym = $user->acronym;

        $tags = $this->dispatcher->forward([
                'controller' => 'tags',
                'action'     => 'getTagsConnectedToQuestion',
                'params'     => ['questionId' => $id]
            ]);
        $question->tags = $tags;
        $this->CommentsController->initialize();
        $question->comments = $this->CommentsController->getQuestionComments($id);

        $answers = $this->dispatcher->forward([
                'controller' => 'answers',
                'action'     => 'getAnswers',
                'params'     => ['questionId' => $id]
            ]);
     
        foreach ($answers as $a) {
            $answerer = $this->dispatcher->forward([
                'controller' => 'users',
                'action'     => 'getUser',
                'params'     => ['id' => $a->userid]
            ]);

            $a->userAcronym = $answerer->acronym;
            $a->comments    = $this->CommentsController->getAnswerComments($a->id);
        }
        
        $this->theme->setTitle($question->topic);
        $this->views->add('questions/basic-one', [
            'question' => $question,
            'answers'  => $answers,
            'title'    => $question->topic
            ], 'main');
    }


    /**
     * Ask a question
     *
     * @return void
     */
    public function askAction()
    {
        $this->session();
        $this->theme->setTitle("Ask a question");

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
            'topic' => [
                'type'       => 'text',
                'label'      => 'Topic:',
                'required'   => true,
                'validation' => ['not_empty'],
            ],
            'question' => [
                'type'       => 'text',
                'label'      => 'Question:',
                'required'   => true,
                'validation' => ['not_empty'],
            ],
            'tags' => [
                'type'       => 'text',
                'label'      => 'Tags: (separete by space) ',
                'required'   => true,
                'validation' => ['not_empty'],
            ],
            'submit' => [
                'type'      => 'submit',
                'value'     => 'Ask',
                'callback'  => function () {
                    $now = gmdate('Y-m-d H:i:s');
                    $user = $this->di->dispatcher->forward([
                        'controller' => 'users',
                        'action'     => 'getLoggedInUser',
                    ]);
                    $this->questions->save([
                        'topic'   => $this->form->Value('topic'),
                        'text'    => $this->form->Value('question'),
                        'userid'  => $user[0],
                        'points'  => 0,
                        'created' => $now,
                        'updated' => $now,
                        'active'  => $now,
                    ]);
                    $tagnames = explode(' ', $this->form->Value('tags'));
                    $res = $this->di->dispatcher->forward([
                        'controller' => 'tags',
                        'action'     => 'addConnection',
                        'params'     => [
                            'questionId' => $this->questions->id,
                            'tagNames'   => $tagnames
                        ]
                    ]);
                    $this->response->redirect($this->url->create('questions/show'));
                    return true;
                }
            ],
        ]);
        
        $form->check();
        $this->di->views->add('default/page', [
            'title'   => "Ask a question",
            'content' => $form->getHTML()
        ]);
    }


    /**
     * Answer a question
     *
     * @param  int $questionid the id of the question
     * @return void
     */
    public function answerAction($questionid)
    {
        $this->session();
        $this->theme->setTitle("Answer a question");

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
            'text' => [
                'type'       => 'textarea',
                'label'      => 'Answer:',
                'required'   => true,
                'validation' => ['not_empty'],
            ],
            'questionid' => [
                'type'       => 'hidden',
                'value'      => $questionid,
                'required'   => true,
                'validation' => ['not_empty'],
            ],
            'submit' => [
                'type'      => 'submit',
                'value'     => 'Answer',
                'callback'  => function () {
                    $now = gmdate('Y-m-d H:i:s');
                    $user = $this->di->dispatcher->forward([
                        'controller' => 'users',
                        'action'     => 'getLoggedInUser',
                    ]);
 
                    $this->dispatcher->forward([
                        'controller' => 'answers',
                        'action'     => 'addAnswer',
                        'params'     => ['text' => $this->form->Value('text'),
                                         'userId' => $user[0],                  
                                         'questionId' => $this->form->Value('questionid')
                                         ]
                    ]);
                    
                    $url = $this->url->create('questions/view/' . $this->form->Value('questionid'));
                    $this->response->redirect($url);
                }
            ],
        ]);
        
        $form->check();
        $this->di->views->add('default/page', [
            'title'   => "Answer a question",
            'content' => $form->getHTML()
        ]);
    }

    /**
     * Get questions asked by a specific user
     *
     * @param  int    $userId    [description]
     * @return object $questions [description]
     */
    public function getQuestionsBy($userId)
    {
        if (!isset($this->questions)) {
            $this->initialize();
        }

        $questions = $this->questions->query()
            ->where('userid is ' . $userId)
            ->execute();
        return $questions;
    }


    /**
     * [getLatestQuestionsAction description]
     *
     * @return [type] [description]
     */
    public function getLatestQuestionsAction()
    {
        $questions = $this->questions->query()
            ->orderby('created DESC')
            ->limit(5)
            ->execute();
        return $questions;
    }


    /**
     * [upVoteAction description]
     *
     * @param  [type] $questionId [description]
     * @return [type]             [description]
     */
    public function upVoteAction($questionId)
    {
        $question = $this->questions->find($questionId);
        $currentPoints = $question->points;
        $this->questions->update([
            'id' => $questionId,
            'points' => $currentPoints+1
            ]);
        $this->response->redirect($this->url->create('questions/view/' . $questionId));
        return true;
    }


    /**
     * [downVoteAction description]
     *
     * @param  [type] $questionId [description]
     * @return [type]             [description]
     */
    public function downVoteAction($questionId)
    {
        $question = $this->questions->find($questionId);
        $currentPoints = $question->points;
        if ($currentPoints > 0) {
            $this->questions->update([
                'id' => $questionId,
                'points' => $currentPoints-1
                ]);
        }
        $this->response->redirect($this->url->create('questions/view/' . $questionId));
        return true;
    }
}
