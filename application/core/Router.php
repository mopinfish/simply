<?php

/**
 * ルーティング管理クラス
 */
class Router
{
    /**
     * ルーティング定義配列
     */
    protected $routes;

    /**
     * コンストラクタ
     */
    public function __construct($definitions)
    {
        $this->routes = $this->compileRoutes($definitions);
    }

    /**
     * ルーティング定義中の動的パラメータを変換する
     */
    public function compileRoutes($definitions)
    {
        $routes = array();

        foreach ($definitions as $url => $params) {
            $tokens = explode('/', ltrim($url, '/'));
            foreach ($tokens as $i => $token) {
                if (0 === strpos($token, ':')) {
                    $name = substr($token, 1);
                    $token = '(?P<' . $name . '>[^/]+)';
                }
                $tokens[$i] = $token;
            }

            $pattern = '/' . implode('/', $tokens);
            $routes[$pattern] = $params;
        }
        return $routes;
    }

    /**
     * URIパス情報からルーティングを決定する
     */
    public function resolve($pathInfo)
    {
        if ('/' !== substr($pathInfo, 0, 1)) {
            $pathInfo = '/' . $pathInfo;
        }

        foreach ($this->routes as $pattern => $params) {
            if (preg_match('#^' . $patternn . '$#', $pathInfo, $matches)) {
                $params = array_merge($params, $matches);
                return $params;
            }
        }
        return false;
    }
}
