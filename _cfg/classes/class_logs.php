<?php

  class Logs extends Features{
    private $username;
    private $type;
    private $action;
    private $id;
    private $date;
    


      /**
       * Users constructor.
       * Including array from users
       */

      public function __construct(array $data)
      {
          $this->generate($data);
          print_r($this);
          echo "creation logs ok";
      }

      /**
       * Get the value of username
       */ 
      public function getUsername()
      {
          return $this->username;
      }

      /**
       * Set the value of username
       * @return  self
       */ 
      public function setUsername($username)
      {
          $this->username = $username;

          return $this;
      }

      /**
       * Get the value of type
       */ 
      public function getType()
      {
          return $this->type;
      }

      /**
       * Set the value of type
       * @return  self
       */ 
      public function setType($type)
      {
          $this->type = $type;

          return $this;
      }

      /**
       * Get the value of action
       */ 
      public function getAction()
      {
          return $this->action;
      }

      /**
       * Set the value of action
       * @return  self
       */ 
      public function setAction($action)
      {
          $this->action = $action;

          return $this;
      }

      /**
       * Get the value of id
       */ 
      public function getId()
      {
          return $this->id;
      }

      /**
       * Set the value of id
       * @return  self
       */ 
      public function setId($id)
      {
          $this->id = $id;

          return $this;
      }

    /**
     * Get the value of date
     */ 
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */ 
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }
  }
?>
