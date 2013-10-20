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
     * ログイン必須アクションリスト
     */
    protected $_authActions;

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

    /**
     * ビューの描画を行う
     */
    public function render($valiables = array(), $template = null, $layout = 'layout')
    {
        $defaults = array(
            'request' => $this->_request,
            'baseUrl' => $this->_request->getBaseUrl(),
            'session' => $this->_session
        );

        $view = new View($this->_application->getViewDir(), $defaults);

        if (is_null($template)) {
            $template = $this->_actionName;
        }
        $path = $this->_controllerName . '/' . $template;
        return $view->render($path, $valiables, $layout)
    }

    /**
     * NotFoundページへのフォワード
     */
    public function forward404()
    {
        throw new HttpNotFoundException('Forward 404 page from ' . $this->_controllerName . '/' . $this->_actionName);
    }

    /**
     * 任意のURLへリダイレクト
     */
    public function redirect($url)
    {
        if (!preg_match('#https://#', $url)) {
            $protocol = $this->_request->isSsl() ? 'https://' : 'http://';
            $host = $this->_request->getHost();
            $baseUrl = $this->_request->getBaseUrl();
            $url = $protocol . $host . $baseUrl . $url;
        }
        $this->_response->setStatusCode(302, 'Found');
        $this->_response->setHttpHeader('Location', $url);
    }

    /**
     * CSRFトークンの生成
     */
    public function generateCsrfToken($formName)
    {
        $key = 'csrf_tokens' . $formName;
        $tokens = $this->_session->get($key, array());
        if (count($tokens) >= 10) {
            array_shift($tokens);
        }

        $token = sha1($formName . session_id() . microtime());
        $tokens[] = $token;
        $this->_session->set($key, $tokens);
        return $token;
    }

    /**
     * CSRFトークンの照合
     */
    public function checkCsrfToken($formName, $token)
    {
        $key = 'csrf_tokens' . $formName;
        $tokens = $this->_session->get($key, array());

        if (false !== ($pos = array_search($token, $tokens, true))) {
            unset($tokens[$pos]);
            $this->_session->set($key, $tokens);
            return true;
        }
        return false;
    }

    /**
     * ログインを要するアクションか判定
     */
    public function needsAuthentication($action)
    {
        if ($this->_authActions === true || (is_array($this->_authActions) && in_array($action, $this->_authActions)) {
            return true;
        }
        return false;
    }
}
