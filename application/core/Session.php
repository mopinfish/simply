<?php

class Session
{
    /**
     * セッション開始フラグ
     */
    protected static $_sessionStarted = false;

    /**
     * セッションID生成フラグ
     */
    protected static $_sessionIdRegenerated = false;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        if (!self::$_sessionStarted) {
            session_start();
        }
    }

    /**
     * セッションの値を格納
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * セッションの値を取得
     */
    public function set($name, $default = null)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return $default;
    }

    /**
     * セッションの値を削除
     */
    public function remove($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * セッションの値を全て削除
     */
    public function clear()
    {
        $_SESSION = array();
    }

    /**
     * セッションIDを再生成
     */
    public function regenerate($destroy = false)
    {
        if (!self::$_sessionIdRegenerated) {
            session_regenerate_id();
            self::$_sessionIdRegenerated = true;
        }
    }

    /**
     * セッションをログイン状態に設定
     */
    public function setAuthenticated($bool)
        $this->set('__authenticated', (bool)$bool);
        $this->regenerate();
    }

    /**
     * セッションからログイン中か判定
     */
    public function isAuthenticated()
    {
        return $this->get('__authenticated', false);
    }
}
