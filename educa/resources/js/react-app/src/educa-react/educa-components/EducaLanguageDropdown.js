import React, { useEffect, useState } from "react";
import { useEducaLocalizedStrings } from "../helpers/StringLocalizationHelper";
import Flag from "react-flags";
import { useSelector } from "react-redux";
import { EDUCA_LANGUAGES_LOCALIZED } from "./EducaLanguageSelect";
import {Dropdown} from "react-bootstrap";

export function EducaLanguageDropdown(props) {
    const [translate, _] = useEducaLocalizedStrings();
    const store = useSelector((state) => state);
    let [currentLanguage, setCurrentLanguage] = useState(props.value);

    let languages = EDUCA_LANGUAGES_LOCALIZED(translate, store);
    if (Array.isArray(props.hide))
        languages = languages.filter(
            (language) => !props.hide.includes(language.name)
        );
    if (Array.isArray(props.show))
        languages = languages.filter((language) =>
            props.show.includes(language.name)
        );

    useEffect(() => {
        setCurrentLanguage(props.value);
    }, [props.value]);

    function formatOptionLabel({ label, flag }, withLabel = false) {
        if (flag == null) return null;
        return (
            <div
                style={{
                    display: "flex",
                    flexDirection: "row",
                    alignItems: "center",
                    columnGap: "0.5rem",
                    height: "25px",
                    width: "25px",
                }}
                className={"flag-holder"}
            >
                <Flag
                    name={flag}
                    format="svg"
                    width={"100%"}
                    height={"100%"}
                    shiny={false}
                    basePath={"/images/flags"}
                />
                {withLabel ? label : null}
            </div>
        );
    }

    function getCustomBootstrapDropDownToggle() {
        return React.forwardRef(({ children, onClick }, ref) => (
            <div
                style={{ display: "flex", flexDirection: "row", margin: "8px" }}
                ref={ref}
                onClick={(e) => {
                    e.preventDefault();
                    onClick(e);
                }}
            >
                {formatOptionLabel(
                    languages.find(
                        (language) =>
                            language.code === currentLanguage?.toLowerCase()
                    )
                )}
            </div>
        ));
    }


    return (
        <>
            <Dropdown alignRight={true} className="language-navbar">
                <Dropdown.Toggle  as={getCustomBootstrapDropDownToggle()}>
                </Dropdown.Toggle>
                <Dropdown.Menu>
                    {languages.map((language) => {
                        return (
                            <div
                                key={language.code}
                                onClick={() => {
                                    setCurrentLanguage(language.code);
                                    props.onChange(language);
                                }}
                                className="dropdown-item"
                            >
                                {formatOptionLabel(language, true)}
                            </div>
                        );
                    })}
                </Dropdown.Menu>
            </Dropdown>
        </>
    );
}
