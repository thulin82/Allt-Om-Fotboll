<?php

namespace Anax\Comments;

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
        $this->form = new \Mos\HTMLForm\CForm();
    }


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
