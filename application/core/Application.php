<?php

/**
 * アプリケーションを統括する抽象クラス
 */
abstract class Application
{
    /**
     * デバッグモード判定フラグ
     */
    protected $_debug = false;

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
     * DB管理オブジェクト
     */
    protected $_dbManager;

    /**
     * ログイン用コントローラ/アクションのセット
     */
    protected $_loginAction = array();

    /**
     * コンストラクタ
     */
    public function __construct($debug = false)
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }

    /**
     * デバッグモードを設定する
     */
    public function setDebugMode($debug)
    {
        if ($debug) {
            $this->_debug = true;
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            $this->_debug = false;
            ini_set('display_errors', 0);
        }
    }

    /**
     * 初期化処理
     */
    public function initialize()
    {
        $this->_request = new Request();
        $this->_response = new Response();
        $this->_session = new Session();
        $this->_dbManager = new DbManager();
        $this->_router = new Router($this->registerRoutes());
    }

    /**
     * 各アプリケーション固有の設定（オーバーライドして使用）
     */
    public function configure()
    {
    }

    /**
     * ルートディレクトリの取得（抽象メソッド）
     */
    abstract public function getRootDir();

    /**
     * ルーティング定義の登録（抽象メソッド）
     */
    abstract public function registerRoutes();

    /**
     * デバッグモードか判定
     */
    public function isDebugMode()
    {
        return $this->_debug;
    }

    /**
     * リクエストオブジェクトの取得
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * レスポンスオブジェクトの取得
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * セッションオブジェクトの取得
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * DbManagerオブジェクトの取得
     */
    public function getDbManager()
    {
        return $this->dbManager;
    }

    /**
     * コントローラクラスディレクトリのパスを取得
     */
    public function getControllerDir()
    {
        return $this->getRootDir() . '/controllers/';
    }

    /**
     * ビューテンプレートディレクトリのパスを取得
     */
    public function getViewDir()
    {
        return $this->getRootDir() . '/views/';
    }

    /**
     * モデルクラスディレクトリのパスを取得
     */
    public function getModelDir()
    {
        return $this->getRootDir() . '/models/';
    }

    /**
     * 公開ディレクトリのパスを取得
     */
    public function getWebDir()
    {
        return $this->getRootDir() . '/web/';
    }

    /**
     * アプリケーションの実行
     */
    public function run()
    {
        try {
            $params = $this->_router->resolve($this->_request->getPathInfo());

            if ($params === false) {
                throw new HttpNotFoundException('No route found for' . $this->_request->getPathInfo());
            }

            $controller = $params['controller'];
            $action = $params['action'];
            $this->runAction($controller, $action, $params);
        } catch (HttpNotFoundException $e) {
            $this->render404Page($e);
        } catch (UnauthorizedActionException $e) {
            list($controller, $action) = $this->_loginAction;
            $this->runAction)$controller, $action);
        }

        $this->_response->send();
    }

    /**
     * コントローラのアクションを実行する
     */
    public function runAction($controllerName, $action, $params = array())
    {
        $controllerClass = ucfirst($controllerName) . 'Controller';
        $controller = $this->findController($controllerClass,);
        if ($controller === false) {
            throw new HttpNotFoundException($controllerClass . ' controller is not found.'));
        }

        $content = $controller->run($action, $params);
        $this->_response->setContent($content);
    }

    /**
     * コントローラクラスを探索してインスタンスを作成
     */
    public function findController($controllerClass)
    {
        // クラスが存在しなければファイルを読み込む
        if (!class_exists($controllerClass)) {
            $controllerFile = $this->getControllerDir . '/' . $controllerClass . '.php';
            if (!is_readable($controllerFile)) {
                return false;
            } else {
                require_once $controllerFile;

                if (!class_exists($controllerClass)) {
                    return false;
                }
            }
        }
        return new $controllerClass($this);
    }

    /**
     * NotFoundページの描画
     */
    public function render404Page($e)
    {
        $this->_response->setStatusCode(404, 'Not Found');
        $message = $this->isDebugMode() ? $e->getMessage() : 'Page not found';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $this->_response->setContent(
<<<EOF
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>404</title>
<body>
    {$message}
</body>
</html>
EOF
        );
    }
}
