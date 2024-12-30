import React, {useEffect, useState} from 'react';
import {Badge, Card, ListGroupItem, Pagination} from "react-bootstrap";
import PropTypes from "prop-types";
import _ from "lodash";


export function EducaListView(props) {

    let [thisData, setThisData] = useState([])

    const pagesize = props.defaultPagesize? props.defaultPagesize : 10
    const hasPagination = props.pagination

    useEffect(() =>
        {
            if(!hasPagination)
                setThisData(props.data)
        },[props.data])


    return (
        <div>
            {thisData.map( (e,i) => <RendererComponent
                Renderer={props.Renderer}
                key={i}
                index={i}
                content={e?e : {}}
                onClick={ ()=> typeof props?.onRowClick == "function"?props?.onRowClick(e,i) : null  }
                pointer={typeof props?.onRowClick == "function"}
                /* Keep up to date w/ Renderer Props*/
            />)}
            {hasPagination?
                <div className={"mt-1"}>
                    <PaginationComponent
                        data={props.data}
                        pagesize={pagesize}
                        setCurrentData={d => setThisData(d)}
                        currentData={thisData}
                    />
                </div> : null}
        </div>
    );
}

EducaListView.propTypes =
    {
        data: PropTypes.array.isRequired,
        Renderer : PropTypes.elementType,
        onRowClick : PropTypes.func
    }


const RendererComponent = React.memo((cProps) => {
        let RendererComponent = cProps.Renderer
        if (!RendererComponent)
            RendererComponent = EducaListViewItemRenderer

        return <RendererComponent {...cProps}/>
    }, (prev, next) =>
        _.isEqual(prev.content, next.content)
        && _.isEqual(prev.onClick, next.onClick)
        && _.isEqual(prev.pointer, next.pointer)
    /* Keep up to date w/ Renderer Props*/
)



const PaginationComponent = React.memo((cProps) => {

        let [pages, setPages] = useState({})
        let [currentPageIndex, setCurrentPageIndex] = useState(null)

        const localPagesize = cProps.pagesize
        const localData = cProps.data
        const currentData =  cProps.currentData
        const setCurrentData = cProps.setCurrentData

        useEffect(() =>
        {
            if(pages && pages[currentPageIndex])
                setCurrentData(pages[currentPageIndex])
        },[currentPageIndex])

        const generatePages = () =>
        {
            if( !Array.isArray(currentData) )
                return setCurrentData([])

            //generate Chunks
            let p = { 0 : []}

            let loopPage = 0
            let loopCounter = 1
            let loopArray = []
            localData?.forEach( (entry,i) =>
            {
                loopArray.push(entry)
                if(loopCounter == localPagesize ||  ( i == localData.length-1) )
                {
                    p[loopPage] = _.cloneDeep(loopArray)
                    loopCounter = 0
                    loopPage++
                    loopArray = []
                }
                loopCounter++
            })
            setPages(_.cloneDeep(p))
            setCurrentData(p[0])
            setCurrentPageIndex(0)

        }

        useEffect(() =>
        {
            if(Array.isArray(localData) && localData.length > 0)
            generatePages()
        },[localData])

  //  console.log("pagination render")

        return (
            <Pagination size={undefined}>
                    {Object.keys(pages)?.map( pageIndex =>
                    {
                        let index = parseInt(pageIndex)
                        return  <Pagination.Item
                            active={index == currentPageIndex}
                            onClick={() => setCurrentPageIndex(index)}>{index+1}</Pagination.Item>
                    })}
            </Pagination>)
    }, (prev, next) =>
    {
        return _.isEqual(prev.data, next.data)
            && _.isEqual(prev.pagesize, next.pagesize)
    }

)

export const EducaListViewMemo = React.memo( (props) => <EducaListView {...props}/>,
    (prev,next) => _.isEqual(prev.data, next.data))


export const EducaListViewCardRenderer = (props) => {

    const content = props.content

    return <div style={props.pointer? {cursor : "pointer"} : undefined} onClick={props.onClick}>
        <Card style={props.index%2? {background : "rgba(0, 0, 0, 0.03)"} : null}>
            <Card.Title>{content.title}</Card.Title>
            <Card.Subtitle className="mb-2 text-muted">{content.subtitle}</Card.Subtitle>
            <Card.Body>
                {content.body}
            </Card.Body>
            {content.footer ? <Card.Footer>
                {content.footer}
            </Card.Footer> : null}
        </Card>
    </div>
}

export const EducaListViewItemRenderer = (props) => {

    const content = props.content

    const bgStyle = props.index%2? {background : "rgba(0, 0, 0, 0.03)"} : {}
    const pointerStyle = props.pointer? {cursor : "pointer"} : {}
    return <ListGroupItem className="d-flex justify-content-between align-items-start" onClick={props.onClick}
                          style={ {...bgStyle, ...pointerStyle}} >
            <div className="ms-2 me-auto">
                <div className="fw-bold">
                    {content.title}
                </div>
                <div className="mb-2 text-muted">
                    {content.subtitle}
                </div>
                {content.body}
            </div>
        {content.badge != null?
            <Badge variant="primary" pill>
                {content.badge}
            </Badge> : null}
    </ListGroupItem>
}
