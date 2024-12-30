import React from 'react'
import PropTypes from 'prop-types'

const EducaFormPair = (props) => {
    const {flex = true, flexGrow, label, children, isMandatory, hasError, hint, className = "m-2"} = props

    const mandatoryField = (
        <div style={{color: 'red'}} className={'ml-1'}>
            *
        </div>
    )

    const getContent = (title, content, hasError) => {
        let comp =  <div style={{display :"flex", flexDirection : "column", flex : 1}} className={className}>
            <div style={{marginBottom :"-2px", fontSize :"12px", display: "flex", flexDirection :"row"}}>{title}</div>
            <div>{content}</div>
        </div>

        return hasError? <div style={{ display :"flex", flex : 1,border: "1px solid rgba(255, 0, 0, 0.58)", borderRadius : "10px", marginBottom : "2px"}}>
            {comp}
        </div> : comp
    }


    return (
        <div style={flex ? {display: "flex", flexGrow : flexGrow? 1 : "unset"} : {}}>
            {getContent(
                isMandatory ?
                    <>
                        {label} {mandatoryField}
                    </>
                 :
                    <>{label}</>
                ,
                children,
                hasError
            )}
            {hint && (
                <span
                    style={{
                        fontStyle: 'italic',
                        fontSize: '0.8em'
                    }}
                    className="m-2"
                >
                    <i className="fas fa-info-circle text-danger"></i>{' '}
                    <span className="text-danger">{hint}</span>
                </span>
            )}
        </div>
    )
}

EducaFormPair.propTypes = {
    label: PropTypes.string.isRequired,
    children: PropTypes.any,
    isMandatory: PropTypes.bool,
    hasError: PropTypes.bool,
    flex: PropTypes.bool,
    flexGrow: PropTypes.bool,
    hint: PropTypes.string,
    className : PropTypes.string
}

export default EducaFormPair
