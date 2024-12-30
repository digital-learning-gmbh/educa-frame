import './style.css'
import React from 'react'
import {darkColors, lightColors} from './FloatingColors'

const FloatingContainer = props => {
    return (
        <nav className={`fab-container ${props.className}`} style={props.styles}>
            {props.children}
        </nav>
    )
}

const FloatingButton = props => {
    return (
        <button onClick={props.onClick}
                className={`fab-item ${props.className} ${props.rotate ? 'fab-rotate' : ''}`}
                tooltip={props.tooltip} style={props.styles || defaultItemStyles}>
            <i className={props.icon} style={props.iconStyles}></i>
            {props.children}
        </button>
    )
}

const FloatingLink = props => {
    return (
        <a href={props.href} onClick={props.onClick}
           className={`fab-item ${props.className} ${props.rotate ? 'fab-rotate' : ''}`}
           tooltip={props.tooltip} style={props.styles || defaultItemStyles}>
            <i className={props.icon} style={props.iconStyles}></i>
            {props.children}
        </a>
    )
}

const defaultItemStyles = {
    backgroundColor: darkColors.lightBlue,
    color: darkColors.white,
    textDecoration: "none",
    border: "none"
}

export { FloatingContainer, FloatingLink, FloatingButton, darkColors, lightColors }
