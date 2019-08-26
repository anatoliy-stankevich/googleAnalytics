<?php

class Application_Model_RequestMapper{
    protected $_dbTable;

    public function setDbTable($dbTable){
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable(){
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_Request');
        }
        return $this->_dbTable;
    }

    public function save(Application_Model_Request $request){
        $data = array(
            'domain'     => $request->getDomain(),
            'keyword'   => $request->getKeyword(),
            'url'       => $request->getUrl(),
            'title'     => $request->getTitle(),
            'position'  => $request->getPosition(),
            'created_at' => date('Y-m-d H:i:s'),
        );

        if (null === ($id = $request->getId())) {
            unset($data['id']);
            $id = $this->getDbTable()->insert($data);
            $request->setId($id);
        }else{
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }

        return $request;
    }

    public function find($id, Application_Model_Request $request){
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return NULL;
        }

        $row = $result->current();

        $request->setId($row->id)
                ->setDomain($row->domain)
                ->setKeyword($row->keyword)
                ->setUrl($row->url)
                ->setTitle($row->title)
                ->setPosition($row->position)
                ->setCreatedAt($row->created_at);

        return $request;
    }

    public function fetchAll(){
        $resultSet = $this->getDbTable()->fetchAll();

        $entries = array();
        foreach ($resultSet->toArray() as $row) {

            $entry = new Application_Model_Request();

            $entry->setId($row['id'])
                    ->setDomain($row['domain'])
                    ->setKeyword($row['keyword'])
                    ->setUrl($row['url'])
                    ->setTitle($row['title'])
                    ->setPosition($row['position'])
                    ->setCreatedAt($row['created_at']);

            $entries[] = $entry;
        }
        return $entries;
    }
}