<?php

/**
 * コントローラ抽象クラス
 */
abstract class Controller
{
    /**
     * コントローラ名
     */
    protected $_controllerName;

    /**
     * アクション名
     */
    protected $_actionName;

    /**
     * アプリケーションオブジェクト
     */
    protected $_application;

    /**
     * リクエストオブジェクト
     */
    protected $_request;

    /**
     * レスポンスオブジェクト
     */
    protected $_response;

    /**
     * セッションオブジェクト
     */
    protected $_session;

    /**
     * DbManagerオブジェクト
     */
    protected $_dbManager;

    /**
     * コンストラクタ
     */
    public function __construct($application)
    {
        $this->_controllerName = strtolower(substr(get_class($this), 0, -10));
        $this->_application = $application;
        $this->_request = $application->getRequest();
        $this->_response = $application->getResponse();
        $this->_session = $application->getSession();
        $this->_dbManager = $application->getDbManager();
    }

    /**
     * アクションの実行
     */
    public function run($action, $params = array())
    {
        $this->_actionName = $action;
        $actionMethod = $action . 'Action';
        if (!method_exists($this, $actionMethod)) {
            $this->forward404();
        }

        $content = $this->$actionMehtod($params);
        return $content;
    }
}
