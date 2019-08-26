<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initPagination()
    {
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(
            'pagination.phtml'
        );
    }
}

