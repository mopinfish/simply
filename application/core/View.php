<?php

/**
 * ビュー管理クラス
 */
class View
{
    /**
     * View用のベースディレクトリ
     */
    protected $_baseDir;

    protected $_defaults;

    protected $_layoutValiables = array();

    /**
     * コンストラクタ
     */
    public function __construct($baseDir, $defaults = array())
    {
        $this->_baseDir = $baseDir;
        $this->_defaults = $defaults;
    }

    /**
     * レイアウト用変数をセット
     */
    public function setLayoutVar($name, $value)
    {
        $this->_layoutValiables[$name] = $value;
    }

    /**
     * ビューの描画
     */
    public function render($path, $valiables = array(), $layout = false)
    {
        $file = $this->_baseDir . $path . '.php';
        extract(array_merge($this->_defaults, $valiables));

        ob_start();
        ob_implicit_flush(0);

        require $file;
        $content = ob_get_clean();
        if ($layout) {
            $content = $this->render($layout, array_merge($this->_layoutValiables, array(
                    '_content' => $content
                )
            ));
        }
        return $content;

    }

    /**
     * 文字列のエスケープ処理
     */
    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
