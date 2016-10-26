<?php

namespace Anax\Questions;

/**
 * A controller for tags
 *
 */
class TagsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
    
    
    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize()
    {
        $this->tags = new \Anax\Questions\Tag();
        $this->tags->setDI($this->di);

        $this->connections = new \Anax\Questions\TagConnection();
        $this->connections->setDI($this->di);

        $this->questions = new \Anax\Questions\Question();
        $this->questions->setDI($this->di);
    }


    /**
     * Setup the database and add a couple of tags
     *
     * @return void
     */
    public function setupAction()
    { 
        $this->theme->setTitle("Reset/Setup dB for tags");

        $this->db->dropTableIfExists('tag')->execute();
        $this->db->createTable(
            'tag',
            [
                'id'      => ['integer', 'primary key', 'not null', 'auto_increment'],
                'name'    => ['varchar(80)'],
                'text'    => ['varchar(80)'],
            ]
        )->execute();
        
        $this->db->insert('tag', ['name', 'text']);
        $this->db->execute(['Zlatan', 'Zlatan-related topics']);
        $this->db->execute(['Toplist', 'Topics regarding toplists']);
        $this->db->execute(['Bottomlist', 'Topics regarding bottomlists']);



        $this->db->dropTableIfExists('tagconnection')->execute();
        $this->db->createTable(
            'tagconnection',
            [
                'id'         => ['integer', 'primary key', 'not null', 'auto_increment'],
                'questionid' => ['integer'],
                'tagid'      => ['integer'],
            ]
        )->execute();

        $this->db->insert('tagconnection', ['questionid', 'tagid']);
        $this->db->execute(['1', '1']);
        $this->db->execute(['1', '2']);
        $this->db->execute(['2', '3']);
    }


    /**
     * Get tags connected to a question
     *
     * @param  int   $questionId the question id
     * @return array $tags       containing tag information
     */
    public function getTagsConnectedToQuestionAction($questionId)
    {
        $connections = $this->connections->query()
                     ->where('questionid is ' . $questionId)->execute();

        $tags = array();
        foreach ($connections as $c) {
            array_push($tags, $this->tags->query()
                ->where('id is ' . $c->tagid)
                ->execute()[0]);
        }
        return $tags;
    }


    /**
     * Display all tags
     *
     * @return void
     */
    public function showAction()
    {
        $this->theme->setTitle("Show all tags");
        $tags = $this->tags->findAll();
        $this->views->add('questions/tags-list', [
                        'tags' => $tags,
                        'title' => 'Tags'
        ]);
    }


    /**
     * Add a connection to a tag
     *
     * @param int   $questionId the question id
     * @param array $tagNames   containing the tag names to connect
     */
    public function addConnectionAction($questionId, $tagNames)
    {
        $tagmap = $this->getTagIds($tagNames);
        foreach ($tagmap as $tagname => $tagid) {
            $res = $this->connections->create([
                'questionid' => $questionId,
                'tagid' => $tagid
            ]);
            if ($res == false) { 
                throw new Exception('Failed to connect q ' . $questionId . ' with tag ' . $tagid . "($tagname)");
            }
        }
        return true;
    }


    /**
     * Get id for existing tag, or create new tag and get id for that
     *
     * @param  array $tagNames containing the tag names to connect
     * @return array $tags     containing name+id of tag
     */
    private function getTagIds($tagNames)
    {
        $tags = array();

        foreach ($tagNames as $tagname) {
            $res = $this->tags->query()
                 ->where("name is '$tagname'")
                 ->execute();

            if (count($res) == 1) {
                $tags[$tagname] = $res[0]->id;
            } else {
                $res = $this->tags->create([
                    'name' => $tagname,
                    'text' => 'No description',
                ]);

                if ($res == true) {
                    $tags[$tagname] = $this->tags->id;
                } else {
                    throw new Exception('Could not create new tag');
                }
            }
        }
        return $tags;
    }


    /**
     * [getQuestionsTaggedBy description]
     *
     * @param  [type] $tagId [description]
     * @return [type]        [description]
     */
    private function getQuestionsTaggedBy($tagId)
    {
        $questionIds = array();
        $connections = $this->connections->query()
                            ->where("tagid is $tagId")
                            ->execute();

        foreach ($connections as $c) {
            array_push($questionIds, $c->questionid);
        }
        return $questionIds;
    }


    /**
     * [cmp description]
     *
     * @param  [type] $a [description]
     * @param  [type] $b [description]
     * @return [type]    [description]
     */
    private function cmp($a, $b)
    {
        if ($a->popularity == $b->popularity) {
            return 0;
        }
        return ($a->popularity > $b->popularity) ? -1 : 1;
    }


    /**
     * [popularTagsAction description]
     *
     * @return [type] [description]
     */
    public function popularTagsAction()
    {
        $tags = $this->tags->findAll();

        foreach ($tags as $tag) {
            $tag->popularity = count($this->getQuestionsTaggedBy($tag->id));
        }
        usort($tags, array($this, "cmp"));
        $tags = array_slice($tags, 0, 5);
        return $tags;
    }


    /**
     * [idAction description]
     *
     * @param  [type] $tagId [description]
     * @return [type]        [description]
     */
    public function idAction($tagId = null)
    {
        if (!isset($tagId)) {
            die("Missing tagId");
        }

        $tag = $this->tags->find($tagId);
        $this->theme->setTitle("View tag with id $tagId");

        $questions_array = array();
        $tagged_questions = $this->getQuestionsTaggedBy($tagId);

        foreach ($tagged_questions as $q) {
            $quest = $this->questions->find($q);

            $questions_array[] = [$quest->id => $quest->topic];

        }
        $tag->related_questions = $questions_array;

        $this->views->add('questions/tags-one', [
            'tag' => $tag,
            'title' => "View tag with id $tagId",
        ]);
    }


    /**
     * Edit an existing tag
     *
     * @param  int $tagId [description]
     * @return void
     */
    public function editAction($tagId = null)
    {
        if (!isset($tagId)) {
            die("Missing tagId");
        }

        $this->theme->setTitle("Edit tag");
        $tag = $this->tags->find($tagId);

        $form = $this->form->create([], [
            'name' => [
                'type'        => 'text',
                'label'       => 'Name:',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value'       => $tag->name,
            ],
            'text' => [
                'type'        => 'text',
                'label'       => 'Text:',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value'       => $tag->text,
            ],
            'tagid' => [
                'type' => 'hidden',
                'value' => $tagId
            ],
            'submit' => [
                'type'      => 'submit',
                'value'     => 'Spara',
                'callback'  => function () {
                    $this->tags->update([
                        'id' => $this->tags->id,
                        'name' => $this->form->Value('name'),
                        'text' => $this->form->Value('text'),
                    ]);
                    $this->response->redirect($this->url->create('tags/id/' . $this->form->Value('tagid')));
                    return true;
                }
            ],
        ]);
                               
        $status = $form->check();    
        $this->views->add('default/page', [
            'title' => "Editera din tag",
            'content' => $form->getHTML()      
                                    
        ]);
    }
}
