<?php namespace App\Third\Push\Getui\Igetui;


/**
 * @author: wanghui
 * @date: 2017/5/10 下午4:30
 * @email: hank.huiwang@gmail.com
 */

class Button extends PBMessage
{
    var $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
    public function __construct($reader=null)
    {
        parent::__construct($reader);
        $this->fields["1"] = "PBString";
        $this->values["1"] = "";
        $this->fields["2"] = "PBInt";
        $this->values["2"] = "";
    }
    function text()
    {
        return $this->_get_value("1");
    }
    function set_text($value)
    {
        return $this->_set_value("1", $value);
    }
    function next()
    {
        return $this->_get_value("2");
    }
    function set_next($value)
    {
        return $this->_set_value("2", $value);
    }
}
