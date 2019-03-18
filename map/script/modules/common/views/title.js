/**
 * @file 页标题 Reflux View
 * @author 崔健 cuijian03@baidu.com 2016.08.20
 */

import React, { Component } from 'react'
import { render } from 'react-dom'

var Title = React.createClass({
    render: function () {
    	var logo = __uri('/static/images/logo_2x.png');
        return (
            <div className="title">
                <span className="headName">长江智链</span>
            </div>
        )
    }
});

export default Title;
