<?php
namespace Mata16\Form;

class CFormEditUser extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionAware,
        \Anax\MVC\TRedirectHelpers;

    private $id;
    private $password;
    private $created;
    private $active;

    /**
     * Constructor
     *
     */
    public function __construct($data)
    {
        $this->id = htmlentities($data->id);
        $this->password = htmlentities($data->password);
        $this->created = htmlentities($data->created);
        $this->active = htmlentities($data->active);
        
        $isChecked = isset($this->active) ? true : false;
        
        parent::__construct([], [
            'acronym' => [
                'type'        => 'text',
                'label'       => 'Akronym',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value'       => htmlentities($data->acronym),
            ],
            'name' => [
                'type'        => 'text',
                'label'       => 'Namn',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value'       => htmlentities($data->name),
            ],
            'email' => [
                'type'        => 'text',
                'label'       => 'E-post',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
                'value'       => htmlentities($data->email),
            ],
            'active' => [
                'type'        => 'checkbox',
                'label'       => 'Aktiv',
                'checked'     => $isChecked,
            ],
            'submit' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackSubmit'],
                'value'     => 'Spara',
            ],
        ]);
    }


    /**
     * Customise the check() method.
     *
     * @param callable $callIfSuccess handler to call if function returns true.
     * @param callable $callIfFail    handler to call if function returns true.
     */
    public function check($callIfSuccess = null, $callIfFail = null)
    {
        return parent::check([$this, 'callbackSuccess'], [$this, 'callbackFail']);
    }


    /**
     * Callback for submit-button.
     *
     * @return boolean true if data was added in db, false otherwise.
     */
    public function callbackSubmit()
    {
        $now = gmdate('Y-m-d H:i:s');
        $active = $this->active;
        if (isset($active) && empty($_POST['active'])) {
            $active = null;
        }
        if (!isset($active) && !empty($_POST['active'])) {
            $active = $now;
        }
        
        
        $this->editUser = new \Anax\Users\User();
        $this->editUser->setDI($this->di);
        $isSaved = $this->editUser->save(array(
            'id'        => $this->id,
            'acronym'   => $this->Value('acronym'),
            'email'     => $this->Value('email'),
            'name'      => $this->Value('name'),
            'password'  => $this->password,
            'created'   => $this->created,
            'updated'   => $now,
            'deleted'   => null,
            'active'    => $active
        ));
        return $isSaved;
    }


    /**
     * Callback What to do if the form was submitted?
     *
     */
    public function callbackSuccess()
    {
        $this->redirectTo('users/');
    }


    /**
     * Callback What to do when form could not be processed?
     *
     */
    public function callbackFail()
    {
        $this->redirectTo();
    }
}
