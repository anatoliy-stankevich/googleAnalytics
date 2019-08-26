<?php

class Application_Model_Request
{
    protected $_id;
    protected $_domain;
    protected $_keyword;
    protected $_url;
    protected $_title;
    protected $_position;
    protected $_createdAt;

    public function __construct(array $options = null){
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function __set($name, $value){
        $method = 'set' . $name;

        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid request property');
        }
        $this->$method($value);
    }

    public function __get($name){
        $method = 'get' . $name;

        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid request property');
        }

        return $this->$method();
    }


    public function setOptions(array $options){
        $methods = get_class_methods($this);

        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }

    public function setDomain($domain)
    {
        $this->_domain = (string) $domain;
        return $this;
    }

    public function getDomain()
    {
        return $this->_domain;
	}

    public function setKeyword($keyword)
    {
        $this->_keyword = (string) $keyword;
        return $this;
    }

    public function getKeyword()
    {
        return $this->_keyword;
    }

    public function setUrl($url)
    {
        $this->_url = (string) $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    public function setTitle($title)
    {
        $this->_title = (string) $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setPosition($position)
    {
        $this->_position = (int) $position;
        return $this;
    }

    public function getPosition()
    {
        return $this->_position;
    }

    public function setCreatedAt($createdAt)
    {
        $this->_createdAt = $createdAt;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->_createdAt;
    }
}

