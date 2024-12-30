import {
    useAsyncDebounce,
    useColumnOrder,
    useExpanded,
    useFilters,
    useFlexLayout,
    useGlobalFilter,
    usePagination,
    useResizeColumns,
    useRowSelect,
    useSortBy,
    useTable
} from 'react-table'
import React, {useEffect, useMemo, useState} from "react";
import BTable from 'react-bootstrap/Table';
import {Button, Dropdown, FormControl, InputGroup, OverlayTrigger, Popover} from "react-bootstrap";
import jsPDF from "jspdf";
import "jspdf-autotable";
import SharedHelper from "../shared-helpers/SharedHelper";
import "./Tables.css"
import Select from "react-select";

const CHECKBOX_ROW_NAME = "selection"
const ACTIONS_ROW_NAME = "actions"

//Can be set ion each column via `filter`
export const FILTER_FUNCTION_TYPES =
    {
        TEXT_INCLUDES : "text_include",
        TEXT_STARTS_WITH : "text_start",

    }


export const selectBoxFilterFunction = (rows, colIds, filterValue) => {

    let colId = colIds?.length? colIds[0] : null
    if(!filterValue?.length || !colId)
        return rows

    return (rows??[]).filter(row => {
        const val = row?.values?.[colId]
        let flag = false
        filterValue.forEach( filtVal => {
            flag = flag || (val??"").toLowerCase().includes((filtVal?.value??"").toLowerCase())
        })
        return flag
    })
}

/**
 *
 * Select-based Column `Filter` Component
 *
 * Use it like so:
 *  { Header: 'Some Header', accessor: 'firstname', filter: <selectBoxFilterFunction | myCustomFunc>, Filter : SelectBoxFilter, preFilter : ["Mike","Josh","Anna"], selectProps : <undefined | { options : myCustomOptions, isMulti : true}> },
 */
export const SelectBoxFilter = ({
                                    rows,
                                    column: {id, filterValue, preFilteredRows, setFilter, preFilter,
                                    selectProps : { options, isMulti, closeMenuOnSelect, isClearable = true, placeholder = "Filtern..."} = [] }}) =>
{
    const count = preFilteredRows.length

    useEffect(() =>
    {
        setFilter(preFilter)
    },[preFilter])

    const fallbackOptions = useMemo(() => {
        return rows?.map(row => row?.values?.[id])
            ?.reduce( (prev,next) => prev.find(o => String(o) == String(next))? prev : [...prev, next] , [] )
            ?.map(o => ({value : String(o), label : String(o)}))
    },[rows])

    return (
        <InputGroup className={"d-block"}>
            <Select
                placeholder={placeholder}
                isClearable={isClearable}
                closeMenuOnSelect={isMulti? !!closeMenuOnSelect : true}
                isMulti={!!isMulti}
                value={filterValue??[]}
                onChange={(val) => {setFilter(val??(isMulti?[] : null))}}
                options={options??fallbackOptions}
            />
        </InputGroup>
    )
}

// default UI for filtering
export function DefaultColumnFilter({
                                 column: { filterValue, preFilteredRows, setFilter, preFilter },
                             }) {
    const count = preFilteredRows.length

    useEffect(() =>
    {
        setFilter(preFilter)
    },[preFilter])

    return (
        <InputGroup>
            <FormControl
                value={filterValue || ""}
                onClick={(e) => e.stopPropagation()}
                onChange={e => {
                    setFilter(e.target.value || undefined) // Set undefined to remove the filter entirely
                    e.stopPropagation()
                }}
                placeholder={`Filter (${count})`}
            />
        </InputGroup>
    )
}


const defaultColumnSortFunc = (rowA, rowB, id, desc) => {

    let a
    let b

    if(typeof id == "string")
    {
        let ids = id?.split(".")
        if(ids?.length > 1 )
        {
            a = rowA?.original
            b = rowB?.original
            ids.forEach( idStr =>
            {
                if(typeof a == "object" && a[idStr])
                    a = a[idStr]
                if(typeof b == "object" && b[idStr])
                    b = b[idStr]
            })
            a = typeof a !== "object"? a : undefined
            b = typeof b !== "object"? b : undefined
        }
        else
        {
            a = rowA?.original[id];
            b = rowB?.original[id];
        }

    }

    if (typeof a == "string") {
        return a.localeCompare(b)
    } else {
        if (a > b) return -1;
        if (b > a) return 1;
        return 0;
    }
}

function toRowData( headerGroups, contentSource)
{
    let rows = []
    let header = []
    if(headerGroups?.length <= 0)
        return
    headerGroups[0].headers?.forEach(h =>
    {
        if(h.id !== CHECKBOX_ROW_NAME && h.id !== ACTIONS_ROW_NAME)
            header.push(h.Header)
    })
    rows.push(header)
    contentSource?.forEach(c =>
    {
        let arr = []
        c.cells?.forEach(cell =>
        {
            if(cell?.column?.id && cell?.column?.id !== CHECKBOX_ROW_NAME && cell?.column?.id !== ACTIONS_ROW_NAME)
            {
                if(typeof cell.column?.excelFormatter == "function")
                    arr.push(cell.value?cell.column.excelFormatter(cell) : "")
                else
                    arr.push(cell.value?cell.value : "")
            }

        })
        rows.push(arr)
    })
    return rows
}

function exportToPdf(filename, headerGroups, contentSource)
{
    let rows = toRowData(headerGroups, contentSource)
    if(!rows || rows?.length === 0)
        return

    const unit = "pt";
    const size = "A4"; // Use A1, A2, A3 or A4
    const orientation = "portrait"; // portrait or landscape

    const marginLeft = 40;
    const doc = new jsPDF(orientation, unit, size);

    doc.setFontSize(15);

    const title = filename;
    let content = {
        startY: 50,
        head: [rows[0]],
        body: rows.slice(1, rows.length)
    };

    doc.text(title, marginLeft, 40);
    doc.autoTable(content);
    doc.save(filename+".pdf")
}

function rawCSVExport(filename, headerGroups, contentSource) {

    let rows = toRowData(headerGroups, contentSource)

    if(!rows || rows?.length === 0)
        return
    let processRow = function (row) {
        let finalVal = '';
        for (let j = 0; j < row.length; j++) {
            let innerValue = row[j] === null ? '' : row[j].toString();
            if (row[j] instanceof Date) {
                innerValue = row[j].toLocaleString();
            };
            let result = innerValue.replace(/"/g, '""');
            if (result.search(/("|,|\n)/g) >= 0)
                result = '"' + result + '"';
            if (j > 0)
                finalVal += ',';
            finalVal += result;
        }
        return finalVal + '\n';
    };

    let csvFile = '';
    for (var i = 0; i < rows.length; i++) {
        csvFile += processRow(rows[i]);
    }

    let blob = new Blob([csvFile], { type: 'text/csv;charset=utf-8;' });
    if (navigator.msSaveBlob) { // IE 10+
        navigator.msSaveBlob(blob, filename+".csv");
    } else {
        let link = document.createElement("a");
        if (link.download !== undefined) { // feature detection
            // Browsers that support HTML5 download attribute
            let url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", filename+".csv");
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
}

function exportToCsv(filename, headerGroups, contentSource){

    if(!window.XLSX) // If sheetjs not available -> export CSV
    {
        rawCSVExport(filename, headerGroups, contentSource)
    }
    else
    {
        let rows = toRowData(headerGroups, contentSource)
        const ws = XLSX.utils.aoa_to_sheet(rows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Blatt 1");
        /* generate XLSX file and send to client */
        XLSX.writeFile(wb, filename+".xlsx")
    }
}

// Define a default UI for filtering
function GlobalFilter({
                          preGlobalFilteredRows,
                          globalFilter,
                          setGlobalFilter,
                      }) {
    const count = preGlobalFilteredRows.length
    const [value, setValue] = React.useState(globalFilter)

    useEffect(() =>
    {
        setValue(globalFilter)
    },[globalFilter])

    return (

        <div style={{maxWidth : "350px"}}>
            <InputGroup>
                <InputGroup.Prepend>
                    <InputGroup.Text>
                        <i className="fas fa-search"></i>
                    </InputGroup.Text>
                </InputGroup.Prepend>
                <FormControl
                    value={value || ""}
                    onChange={e => {
                        setValue(e.target.value);
                        setGlobalFilter(e.target.value)
                    }}
                    placeholder={`${count} Einträge...`}
                />
            </InputGroup>
        </div>
    )
}


const IndeterminateCheckbox = React.forwardRef(
    ({ indeterminate, ...rest }, ref) => {
        const defaultRef = React.useRef()
        const resolvedRef = ref || defaultRef

        React.useEffect(() => {
            resolvedRef.current.indeterminate = indeterminate
        }, [resolvedRef, indeterminate])

        return (
            <>
                <input type="checkbox" ref={resolvedRef} {...rest} />
            </>
        )
    }
)


export function EducaDefaultTable(props) {

    let data = props.data ? props.data : []
    let columns = React.useMemo( () => props.columns?.map(col => ({sortType : defaultColumnSortFunc,...col})), [props.columns])

    let filenamePrefix = props.filename ? props.filename : "educa_"
    /*
     * FLAGS
     */
    let hasPagination = !!props.pagination
    let defaultPageSize = props.defaultPageSize ? props.defaultPageSize : 10
    let hasResizing = !!props.columnResizing
    let hasGlobalFilter = !!props.globalFilter
    let hasExcelExportButton = !!props.buttonExcelExport
    let hasPDFExportButton = !!props.buttonPdfExport
    let hasPageSizePicker = !!props.pageSizePicker
    let hasColumnPicker = !!props.columnPicker
    let hasMultiSelect = !!props.multiSelect
    let hasCustomButtons = Array.isArray(props.customButtonBarComponents) && props.customButtonBarComponents.length > 0
    let hasCustomOnSelectionButtons = Array.isArray(props.customOnSelectionButtons) && props.customOnSelectionButtons.length > 0


    const filterTypes = React.useMemo(
        () => ({
            text_start : (rows, id, filterValue) => {
                return rows.filter(row => {
                    const rowValue = row.values[id]
                    return rowValue !== undefined
                        ? String(rowValue)
                            .toLowerCase()
                            .startsWith(String(filterValue).toLowerCase())
                        : true
                })
            },
            text_include : (rows, id, filterValue) => {
                return rows.filter(row => {
                    const rowValue = row.values[id]
                    return rowValue !== undefined
                        ? String(rowValue)
                            .toLowerCase()
                            .includes(String(filterValue).toLowerCase())
                        : true
                })
            },
        }),
        []
    )

    const defaultColumn = React.useMemo(
        () => ({
            // Let's set up our default Filter UI
            Filter: DefaultColumnFilter,
        }),
        []
    )

    // STATES
    const {
        //filter
        preGlobalFilteredRows,
        setGlobalFilter,
        setAllFilters,

        getTableProps,
        getTableBodyProps,
        headerGroups,
        prepareRow,
        state,
        rows,
        page,
        allColumns,

        // The rest of these things are super handy, too ;)
        canPreviousPage,
        canNextPage,
        pageOptions,
        pageCount,
        gotoPage,
        nextPage,
        previousPage,
        setPageSize,
        setColumnOrder,
        selectedFlatRows, // Multi select
        toggleAllRowsSelected,
        state: {pageIndex, pageSize, hiddenColumns, selectedRowIds, expanded, filters},

        //hidden columns
        setHiddenColumns
    } = useTable(
        {
            columns,
            data,
            defaultColumn,
            autoResetPage : false,
            autoResetExpanded: false,
            autoResetGroupBy: false,
            autoResetSelectedRows: false,
            autoResetSortBy: false,
            autoResetFilters: false,
            autoResetRowState: false,
            autoResetHiddenColumns : false,
            filterTypes,
            initialState: {
                pageIndex: 0,
                autoResetSelectedRows: false,
                pageSize: defaultPageSize,
                hiddenColumns : props.defaultHiddenColumns? props.defaultHiddenColumns : [],
                selectedRowIds : props.preSelectedRelectedRowIds? props.preSelectedRelectedRowIds : {}},
        },


        useFlexLayout,
        useResizeColumns,
        useFilters, // useFilters!
        useGlobalFilter, // useGlobalFilter!
        useSortBy,
        useExpanded,// Use the useExpanded plugin hook
        usePagination,
        useColumnOrder,
        useRowSelect,
    hooks => {
            hasMultiSelect?
            hooks.visibleColumns.push(columns => [
                // Let's make a column for selection
                {
                    id: 'selection',
                    // The header can use the table's getToggleAllRowsSelectedProps method
                    // to render a checkbox
                    Header: ({ getToggleAllRowsSelectedProps }) => (
                        <div style={{display:"flex", flex : 1}}>
                           <IndeterminateCheckbox {...getToggleAllRowsSelectedProps()} />
                        </div>
                    ),
                    // The cell can use the individual row's getToggleRowSelectedProps method
                    // to the render a checkbox
                    Cell: ({ row }) => (
                        <div style={{display: "flex", height : "100%"}}>
                        <div style={{display: "flex", flex: 1, justifyContent: "center", flexDirection :"row"}}>
                            <div style={{display: "flex", flex: 1, flexDirection :"column", justifyContent: "center"}}>
                                <IndeterminateCheckbox {...row.getToggleRowSelectedProps()} />
                            </div>
                        </div>
                        </div>
                    ),
                    width : "30"
                },
                ...columns,
            ]) : null
        }
    )

    useEffect(() => {
        if(typeof props.onFilterChanged == "function")
            props.onFilterChanged(filters)
    },[filters])

    const getDefaultCollapsedStates = () =>
    {
        let ret =  {}
        columns.forEach( c => c.preFilter? ret[c.accessor] = true : null )
        return ret
    }


    // Notify Parent if selection changed. DONT USE IN COMBINATION W/ setState() -> endless loop
    useEffect(() =>{
        if(typeof props.onSelectionChanged == "function")
            props.onSelectionChanged(selectedFlatRows? selectedFlatRows?.map( r => r.original) : [], Object.keys(selectedRowIds)?.map(k => parseInt(k)))

    }, [selectedFlatRows])

    let [filterCollapsedStates, setFilterCollapsedStates] = useState(getDefaultCollapsedStates())

    let contentSource = hasPagination ? page : rows
    //let tooltipsToBeAdded = []
    return (
        <>
            {hasGlobalFilter || hasExcelExportButton || hasPDFExportButton || hasCustomButtons || hasColumnPicker || hasCustomOnSelectionButtons?
                <div className={"mb-1"} style={{flexDirection: "row", display: "flex", flex : 1, justifyContent :"flex-end"}}>
                    {hasGlobalFilter ? <div className={"mr-1"} style={{flexDirection: "row", display: "flex", flex : 1, justifyContent :"flex-start"}}><GlobalFilter
                        preGlobalFilteredRows={preGlobalFilteredRows}
                        globalFilter={state.globalFilter}
                        setGlobalFilter={setGlobalFilter}
                    />
                    </div> : null}
                    {hasCustomButtons?
                        props.customButtonBarComponents?.map( (btn,i) => {
                            return <div key={i} className={"mr-1"}>{btn}</div>

                        }) : null}
                    {hasCustomOnSelectionButtons && selectedFlatRows?.length > 0?
                        <div className={"mr-1"}>
                            <Dropdown>
                                <Dropdown.Toggle
                                    as={DropDownIndicator}
                                />
                                <Dropdown.Menu>
                                    {props.customOnSelectionButtons?.map((btn, i) => {
                                        return <Dropdown.Item key={"dd1"+i}>
                                            {<btn.type {...btn.props} style={{...btn.props.style, width: "100%"}}
                                                       onClick={() => {
                                                           btn.props.onClickWithSelection(selectedFlatRows?.map(fr => fr.original), (flag) => toggleAllRowsSelected(flag))
                                                       }}/>}
                                        </Dropdown.Item>
                                    })}
                                </Dropdown.Menu>
                            </Dropdown>
                        </div>
                        : null}
                    {hasExcelExportButton ?
                        <Button
                            className={"mr-1"}
                            variant={"secondary"}
                            onClick={() => exportToCsv(filenamePrefix + moment().format("DD_MM_YYYY"), headerGroups, rows?.map( r => {prepareRow(r); return r}))}
                        ><i className="fas fa-file-excel"></i> Excel</Button> : null}

                    {hasPDFExportButton ?
                        <Button
                            className={"mr-1"}
                            variant={"secondary"}
                            onClick={() => exportToPdf(filenamePrefix + moment().format("DD_MM_YYYY"), headerGroups, rows?.map( r => {prepareRow(r); return r}))}
                        ><i className="fas fa-file-pdf"></i> PDF</Button> : null}
                    {state.globalFilter?.length || state.filters?.length ? <Button
                        className={"mr-1"}
                        variant={"secondary"}
                        onClick={() => {
                            setAllFilters([]);
                            setGlobalFilter([]);
                            setFilterCollapsedStates({})
                        }}
                    >
                        <i className="fas fa-trash"></i> Filter zurücksetzen
                    </Button> : null}

                    {hasColumnPicker ? <ColumnPicker
                        columns={allColumns}
                        hiddenColumns={hiddenColumns}
                        onChange={ (newHiddenColumns) => setHiddenColumns(newHiddenColumns)}
                        orderChange={ (newOrderIds) => setColumnOrder(newOrderIds)}
                    /> : null}

                </div> : null}

            <BTable striped bordered hover size={props.size} {...getTableProps()}>
                <thead>
                {// Loop over the header rows
                    headerGroups.map( (headerGroup,i) => (
                        <tr
                            key={"hg"+i}
                            {...headerGroup.getHeaderGroupProps()}
                            style={{...headerGroup.getHeaderGroupProps().style, border: "none"}}
                        >
                            {// Loop over the headers in each row
                                headerGroup.headers.map( (column,i) => (
                                    <th key={"th1"+i} {...column.getHeaderProps(column.getSortByToggleProps({ title: "Sortieren" }))}>
                                        <div style={{display :"flex", flexDirection : "row"}}>
                                            <div>
                                                {column.render('Header')}
                                            </div>
                                            <span>
                                        {column.isSorted
                                            ? column.isSortedDesc
                                                ? <i className="fas fa-chevron-up ml-1"></i>
                                                : <i className="fas fa-chevron-down  ml-1"></i>
                                            : ''}
                                      </span>
                                            {column.canFilter && column.filter ?
                                                <div style={{
                                                    display: "flex",
                                                    flexDirection: "row",
                                                    flex: 1,
                                                    justifyContent: "flex-end"
                                                }}>
                                                    <div
                                                        style={{width : "25px"}}
                                                        onClick={(e) =>
                                                        {
                                                            let obj = JSON.parse(JSON.stringify(filterCollapsedStates))
                                                            let isSet = !!filterCollapsedStates[column.id]
                                                            obj[column.id] = !isSet
                                                            setFilterCollapsedStates(obj)
                                                            e.stopPropagation()
                                                        }
                                                        }>
                                                        <i className="fas fa-filter"></i>
                                                    </div>
                                                </div> : null}
                                            {hasResizing ? <div
                                                {...column.getResizerProps()}
                                                onClick={(evt) => evt.stopPropagation()}
                                                onMouseDown={(evt) => {
                                                    column.getResizerProps().onMouseDown(evt);
                                                }}
                                                style={{
                                                    display: "inline-block",
                                                    cursor: "col-resize",
                                                    width: "10px",
                                                    height: "100%",
                                                    position: "absolute",
                                                    right: 0,
                                                    top: 0,
                                                    transform: "translateX(50%)",
                                                    zIndex: 1
                                                }}

                                            /> : null}
                                        </div>
                                        {/* Render the columns filter UI */}
                                        <div>{ filterCollapsedStates[column.id] && column.canFilter && column.filter ? column.render('Filter') : null}</div>

                                    </th>
                                ))}
                        </tr>
                    ))}
                </thead>
                <tbody {...getTableBodyProps()}>
                {contentSource.length === 0? <tr><td><div style={{display : "flex", justifyContent : "center", flex : 1, flexDirection : "row"}}><i>Keine Daten vorhanden</i></div></td></tr> :null}
                {contentSource.map((row, i) => {
                    let showToolTip = !!row.original?.tooltip
                    let uuidRow
                    let popover
                    if(showToolTip)
                    {
                        uuidRow = SharedHelper.createUUID()
                         popover = <Popover
                             show={undefined}
                             arrowProps={undefined}
                             id={uuidRow}
                             style={{display :"flex", flex : 1}}>
                                {row.original?.tooltip?.component}
                            </Popover>

                    }

                    prepareRow(row)
                     // Create row content
                    let rowContent = row.cells.map( (cell,i) => {

                        let cellContent = <td key={"tdd2"+i} {...cell.getCellProps()} >
                            {cell.render('Cell')}
                        </td>
                        if (showToolTip && cell?.column?.id !== ACTIONS_ROW_NAME && cell?.column?.id !== CHECKBOX_ROW_NAME ) // dont show tooltip on action cell
                            return <OverlayTrigger
                                key={uuidRow+i}
                                rootClose={!row.original?.tooltip?.closeOnClickOutside ? false : true}
                                trigger={Array.isArray( row.original?.tooltip?.triggers) ? row.original?.tooltip?.triggers : ['hover', 'focus']}
                                placement="auto"
                                overlay={popover}>
                                {cellContent}
                            </OverlayTrigger>
                        return cellContent

                    })
                    return (
                            <tr
                                key={"trr"+i}
                                {...row.getRowProps()} style={{...row.getRowProps().style, border: "none", background : row.original?.isSelected? "lightgray" : "" }}
                                onClick={(evt) => {
                                    if (row.getRowProps().onClick)
                                        row.getRowProps().onClick(evt)
                                    if (props.onRowClicked)
                                        props.onRowClicked(evt, row, i)
                                }}
                            >
                                {rowContent}
                            </tr>
                    )
                })}

                </tbody>
            </BTable>
            <>
                {/*tooltipsToBeAdded.map(c => c)*/}
            </>
            <div style={{display: "flex", flexDirection: "row"}}>
                {hasPageSizePicker ?
                    <div className={"m-1"}>
                        <PageSizePicker
                            onChange={(newValue) => setPageSize(newValue)}>
                            {pageSize}
                        </PageSizePicker></div>
                    : null}

                {pageCount !== 1 && hasPagination ?
                    <div style={{display :"flex", flexDirection :"row", justifyContent :"flex-end", flex : 1}}>
                        <div className="pagination">
                            <Button variant={"secondary"} className={"m-1"} onClick={() => gotoPage(0)}
                                    disabled={!canPreviousPage}>
                                <i className="fas fa-chevron-left"></i><i className="fas fa-chevron-left"></i>
                            </Button>{' '}
                            <Button variant={"secondary"} className={"m-1"} onClick={() => previousPage()}
                                    disabled={!canPreviousPage}>
                                <i className="fas fa-chevron-left"></i>
                            </Button>{' '}
                            <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}}>
                            <span> Seite{' '}
                                <strong>{pageIndex + 1}</strong> von  <strong>{pageOptions.length}</strong>{' '}</span>
                            </div>
                            <Button variant={"secondary"} className={"m-1"} onClick={() => nextPage()}
                                    disabled={!canNextPage}>
                                <i className="fas fa-chevron-right"></i>
                            </Button>{' '}
                            <Button variant={"secondary"} className={"m-1"} onClick={() => gotoPage(pageCount - 1)}
                                    disabled={!canNextPage}>
                                <i className="fas fa-chevron-right"></i><i className="fas fa-chevron-right"></i>
                            </Button>{' '}
                        </div>
                    </div>: null}
            </div>

        </>
    )
}
/**
 *  PAGINATION STUFF
 */

const CustomTogglePageSizePicker = React.forwardRef((props, ref) => {
    return <div style={{width : "175px"}}>
        <InputGroup size={"sm"}>
            <InputGroup.Prepend>
                <InputGroup.Text>
                    Seitengröße
                </InputGroup.Text>
            </InputGroup.Prepend>
            <FormControl
                onChange={() => {}}
                value={props.children}
                ref={ref}
                onClick={(e) => {
                    e.preventDefault();
                    props.onClick(e);
                }}
            >
            </FormControl>
            <InputGroup.Append>
                <Button
                    onClick={(e) => {
                        e.preventDefault();
                        props.onClick(e);
                    }}
                    variant={"secondary"}><i className={"fas fa-chevron-down"}></i> </Button>
            </InputGroup.Append>
        </InputGroup>
    </div>}
)


function PageSizePicker (props)
{

    return  <Dropdown>
        <Dropdown.Toggle as={CustomTogglePageSizePicker} id="CustomTogglePageSizePicker">
            {props.children}
        </Dropdown.Toggle>

        <Dropdown.Menu>
            <Dropdown.Item onSelect={(key) => props.onChange(parseInt(key))} eventKey="10">10</Dropdown.Item>
            <Dropdown.Item onSelect={(key) => props.onChange(parseInt(key))} eventKey="25">25</Dropdown.Item>
            <Dropdown.Item onSelect={(key) => props.onChange(parseInt(key))} eventKey="50">50</Dropdown.Item>
            <Dropdown.Item onSelect={(key) => props.onChange(parseInt(key))} eventKey="100">100</Dropdown.Item>
            <Dropdown.Item onSelect={(key) => props.onChange(parseInt(key))} eventKey="1000">1000</Dropdown.Item>
            <Dropdown.Item onSelect={(key) => props.onChange(parseInt(key))} eventKey="5000">5000</Dropdown.Item>
        </Dropdown.Menu>
    </Dropdown>
}


function ColumnPicker( props )
{

    let [show, setShow] = useState(null)
    let [hiddenColumns, setHiddenColumns] = useState(props.hiddenColumns)

    useEffect(() => {
        setHiddenColumns(props.hiddenColumns)
    },[props.hiddenColumns] )

    useEffect(() =>
    {
        setHiddenColumns(props.hiddenColumns)
        setShow(false)
    },[])



    return  <Dropdown show={show}>
        <Dropdown.Toggle as={Button} id="CustomTogglePageSizePicker" variant={show? "danger" : "secondary"} onClick={() => show=== null? null : setShow(!show) }>
            {show? "Schließen" : "Spalten"}
        </Dropdown.Toggle>

        <Dropdown.Menu >
            {props.columns?.map( (column,i) =>
            {
                let selectedFlag = !!hiddenColumns?.find( id => id == column.id )
                return <Dropdown.Item
                    key={i}
                    onSelect={() => {
                        let cols = hiddenColumns.concat([]);
                        if(selectedFlag)
                        {
                            let index = cols.findIndex( id => id == column.id)
                            if(index !== undefined)
                            {
                                cols.splice(index, 1)
                            }
                        }
                        else
                        {
                            cols = cols.concat([column.id])
                        }
                        setHiddenColumns(cols)
                        props.onChange(cols)
                    }}
                    active={!selectedFlag}
                    eventKey={i}>
                    {column.Header}</Dropdown.Item>
            })}
        </Dropdown.Menu>
    </Dropdown>
}


export const EducaDefaultTableExpander = {
    id: "expander",
    width : 20,
    Header: ({ getToggleAllRowsExpandedProps, isAllRowsExpanded }) => (
        <span {...getToggleAllRowsExpandedProps()}>
                                            {isAllRowsExpanded ? <i className={"fa fa-chevron-down"}/> : <i className={"fa fa-chevron-right"}/>}
                                          </span>
    ),
    Cell: ({ row }) =>
        row.canExpand ? (
            <span
                {...row.getToggleRowExpandedProps({
                    style: {
                        // We can even use the row.depth property
                        // and paddingLeft to indicate the depth
                        // of the row
                        paddingLeft: `${row.depth * 1.25}rem`
                    }
                })}
            >
                                          {row.isExpanded ?<i className={"fa fa-chevron-down"}/> : <i className={"fa fa-chevron-right"}/>}
                                        </span>
        ) : null
}

let DropDownIndicator = React.forwardRef(({children, onClick}, ref) => (
    <Button
    ref={ref}
    onClick={(e) => {
        e.preventDefault();
        onClick(e);
    }}><i className={"fa fa-layer-group"}/> Stapel... <i className={"fa fa-chevron-down"}/></Button>

))
