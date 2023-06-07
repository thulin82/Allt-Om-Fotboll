<?php

namespace Anax\Questions;

/**
 * A controller for answers
 *
 */
class AnswersController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;


    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize()
    {
        $this->answers = new \Anax\Questions\Answer();
        $this->answers->setDI($this->di);
    }


    /**
     * Setup the database and add a couple of answers
     *
     * @return void
     */
    public function setupAction()
    {

        $this->theme->setTitle("Reset/Setup dB for answers");
        $this->db->dropTableIfExists('answer')->execute();

        $this->db->createTable(
            'answer',
            [
                'id'         => ['integer', 'primary key', 'not null', 'auto_increment'],
                'text'       => ['varchar(1024)'],
                'userid'     => ['integer'],
                'questionid' => ['integer'],
                'points'     => ['integer'],
                'created'    => ['datetime'],
                'updated'    => ['datetime'],
                'deleted'    => ['datetime'],
                'active'     => ['datetime'],
            ]
        )->execute();

        $this->db->insert(
            'answer',
            ['text', 'userid', 'questionid', 'points', 'created', 'updated', 'deleted', 'active']
        );

        $now = gmdate('Y-m-d H:i:s');

        $this->db->execute([
            'My bet is on Ronaldo',
            '1',
            '1',
            '0',
            $now,
            $now,
            null,
            $now
        ]);

        $this->db->execute([
            'It is Messi, duuuuuu!',
            '2',
            '1',
            '0',
            $now,
            $now,
            null,
            $now
        ]);

        $this->db->execute([
            'Hahaha, it is of course Haakan Mild',
            '2',
            '2',
            '0',
            $now,
            $now,
            null,
            $now
        ]);

        $this->response->redirect($this->url->create('tags/setup/'));

    }


    /**
     * Get answers for a question
     *
     * @param  int $questionId id of question
     * @return object $answers
     */
    public function getAnswersAction($questionId)
    {
        $answers = $this->answers->query()
            ->where('questionid is ' . $questionId)->execute();
        return $answers;
    }


    /**
     * Add answer to a question
     *
     * @param string $text       The answer
     * @param int    $userId     The userid of poster
     * @param int    $questionId The question the answer belongs to
     */
    public function addAnswerAction($text, $userId, $questionId)
    {
        $now = gmdate('Y-m-d H:i:s');
        $res = $this->answers->save([
            'text'       => $text,
            'userid'     => $userId,
            'questionid' => $questionId,
            'points'     => 0,
            'created'    => $now,
            'updated'    => $now,
            'deleted'    => null,
            'active'     => $now
        ]);
        return $res;
    }


    /**
     * Get answers given by a specific user
     *
     * @param  int    $userId  The id of the user
     * @return object $answers 
     */
    public function getAnswersBy($userId)
    {
        if (!isset($this->answers)) {
            $this->initialize();
        }

        $answers = $this->answers->query()
            ->where('userid is ' . $userId)
            ->execute();
        return $answers;
    }


    /**
     * Upvote an answer
     *
     * @param  int $answerId  the id of the answer to upvote
     * @param  int $questionId the id of the question the answer belongs to
     * @return bool true
     */
    public function upVoteAction($answerId, $questionId)
    {
        $answer = $this->answers->find($answerId);
        $currentPoints = $answer->points;
        $this->answers->update([
            'id' => $answerId,
            'points' => $currentPoints+1
            ]);
        $this->response->redirect($this->url->create('questions/view/' . $questionId));
        return true;
    }


    /**
     * Downvote an answer
     *
     * @param  int $answerId  the id of the answer to downvote
     * @param  int $questionId the id of the question the answer belongs to
     * @return bool true
     */
    public function downVoteAction($answerId, $questionId)
    {
        $answer = $this->answers->find($answerId);
        $currentPoints = $answer->points;
        if ($currentPoints > 0) {
            $this->answers->update([
                'id' => $answerId,
                'points' => $currentPoints-1
                ]);
        }
        $this->response->redirect($this->url->create('questions/view/' . $questionId));
        return true;
    }
}
