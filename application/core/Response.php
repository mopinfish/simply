<?php

class Response
{
    /**
     * レスポンス内容
     */
    protected $_content;

    /**
     * ステータスコード
     */
    protected $_statusCode = 200;

    /**
     * ステータステキスト
     */
    protected $_statusText = 'OK';

    /**
     * HTTPヘッダ
     */
    protected $_httpHeaders = array();

    /**
     * レスポンスの送信
     */
    public function send()
    {
        header('HTTP/1.1' . $this->_statusCode . ' ' . $this->_statusText);

        foreach ($this->_httpHeaders as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->_content;
    }

    /**
     * レスポンス内容をセット
     */
    public function setContent($content)
    {
        $this->_content = $content;
    }

    /**
     * ステータスコードをセット
     */
    public function setStatusCode($statusCode, $statusText = '')
    {
        $this->_statusCode = $statusCode;
        $this->_statusText = $statusText;
    }

    /**
     * HTTPヘッダをセット
     */
    public function setHttpHeader($name, $value)
    {
        $this->_httpHeaders[$name] = $value;
    }
}
