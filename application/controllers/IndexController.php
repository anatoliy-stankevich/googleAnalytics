<?php

class IndexController extends Zend_Controller_Action
{
    private $errors = array();
    private $googleUrl = "https://www.google.com/search?";
    private $request;

    public function init()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
        $this->request = new Application_Model_RequestMapper();

        $this->view->title = "Google analytics";
        $this->view->home = "/index/";
        $this->view->history = "/index/history";
        $this->view->request_add = "/index/request_add";
    }

    /**
     * Action home page.
     */
    public function indexAction()
    {
        $this->view->header = "Get position on Google";
    }

    /**
     * Action for page history requests.
     */
    public function historyAction()
    {
        $this->view->header = "History requests";

        $paginator = Zend_Paginator::factory($this->request->fetchAll());

        $config = $this->getInvokeArg('bootstrap')->getOptions();
        $paginator->setDefaultItemCountPerPage($config['pagination']['per_page']);
        $allItems = $paginator->getTotalItemCount();

        $page = $this->getRequest()->getParam('page', 1);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;
        $this->view->countItems = $allItems;
    }

    /**
     * Action add new request.
     *
     * @param  GET domain
     * @param  GET keyword
     */
    public function requestAddAction()
    {
        $request = new Application_Model_Request();

        $request->setDomain($this->_getParam('domain'));
        $request->setKeyword($this->_getParam('keyword'));


        if($this->validation($request)) {

            $this->request->save($request);

            if($request->getId() != NULL){
                if ($this->googlePageParse($request)) {
                    $this->redirect('/index/history');
                }
            }
        }else{
            $this->redirect('/',array('errors', $this->errors));
        }

    }

    /**
     * Check for valid values for domain and keyword
     *
     * @param  Application_Model_Request $request
     * @throws Zend_Db_Table_Exception
     * @return boolean
     */
    private function validation($request){
        if ((strlen(utf8_decode($request->getDomain())) < 3) || (strlen(utf8_decode($request->getDomain())) > 255)) {
            $this->errors[] = "Domain must be greater than 3 and less than 255 characters!";
        }
        if ((strlen(utf8_decode($request->getKeyword())) < 3) || (strlen(utf8_decode($request->getKeyword())) > 255)) {
            $this->errors[] = "Keyword must be greater than 3 and less than 255 characters!";
        }

        return !$this->errors;
    }

    public function googlePageParse($request){
        $items = array();

        $url = $this->queryToUrl($request->getKeyword(),0, 100);

        $result = file_get_contents( $url );

        if(empty($result)){
            $this->errors[] = 'Not google page fount';
        }

        $htmlObj = str_get_html($result);

        if($htmlObj != null && !empty($htmlObj) && $htmlObj->innertext != ''){

            $items = $htmlObj->find('div[id=main]',0)->children;

            if(!empty($items)){

                $index = 1;
                foreach($items as $item){

                    if(!empty($item->find('div[class=x54gtf]', 0))) {
                        $blockData = $item->find('div[class=kCrYT]', 0);

                        if(!empty($blockData)) {
                            $link = $blockData->find('a',0);

                            if($link != null && !empty($link) && $link->innertext != ''){

                                $url = str_replace("/url?q=", "", $link->href);
                                $searchUrl =  str_replace(array("http://","https://"), "", $request->getDomain());

                                $res1 = stripos($url, $request->getDomain());
                                $res2 = stripos(urldecode($url), $request->getDomain());
                                $res3 = stripos(urldecode(urldecode($url)), $request->getDomain());

                                if ($res1 !== false || $res2 !== false || $res3 !== false ) {
                                    $titleBlock = $link->find('div[class=vvjwJb]',0);

                                    if($titleBlock != null && !empty($titleBlock) && $titleBlock->innertext != ''){
                                        $request->setTitle(trim($titleBlock->plaintext));
                                    }

                                    $request->setUrl(urldecode(urldecode($url)));
                                    $request->setPosition($index);

                                    $this->request->save($request);

                                    return true;
                                }
                            }
                        }
                        $index++;
                    }
                }
            }
        }
        return true;
    }

    private function queryToUrl($query, $start=null, $perPage=100)
    {
        return $this->googleUrl . http_build_query( array(	"q" 	=> $query,
                    "biw" 	=> 1242,
                    "bih" 	=> 597,
                    "prmd" 	=> "imvn",
                    "ei" 	=> 'fOxgXd-7Bcmd-gTf0YywCw&start',
                    "start" => $start,
                    "num" 	=> $perPage,
                )
            );
    }
}

