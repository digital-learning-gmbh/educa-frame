import React from "react";
import Select from "react-select";
import { useEducaLocalizedStrings } from "../helpers/StringLocalizationHelper";
import Flag from "react-flags";
import { useSelector } from "react-redux";

const EDUCA_LANGUAGES = [
    {
        name: "german",
        code: "de",
        flag: "de",
    },
    {
        name: "english",
        code: "en",
        flag: "us",
    },
    {
        name: "french",
        code: "fr",
        flag: "fr",
    },
    {
        name: "spanish",
        code: "es",
        flag: "es",
    },
    {
        name: "russian",
        code: "ru",
        flag: "ru",
    },
    {
        name: "ukrainian",
        code: "uk",
        flag: "ua",
    },
    {
        name: "polish",
        code: "pl",
        flag: "pl",
    },
    {
        name: "czech",
        code: "cs",
        flag: "cz",
    },
    {
        name: "turkish",
        code: "tr",
        flag: "tr",
    },
    {
        name: "slovak",
        code: "sk",
        flag: "sk",
    },
    {
        name: "serbian",
        code: "sr",
        flag: "sr",
    },
    {
        name: "hungary",
        code: "hu",
        flag: "hu",
    },
];

export function EDUCA_LANGUAGES_ACTIVATED(store) {
    let availableLanguages = store?.tenant?.availableLanguages;

    if (Array.isArray(availableLanguages))
        return EDUCA_LANGUAGES.filter((language) =>
            availableLanguages.includes(language.code)
        );
    else return EDUCA_LANGUAGES;
}

export function EDUCA_LANGUAGES_LOCALIZED(translate, store) {
    return EDUCA_LANGUAGES_ACTIVATED(store).map((language) => {
        return {
            label: translate("language.type." + language.name),
            ...language,
        };
    });
}

export function EducaLanguageSelect(props) {
    const [translate, _] = useEducaLocalizedStrings();
    const store = useSelector((state) => state);

    let languages = EDUCA_LANGUAGES_LOCALIZED(translate, store);
    if (Array.isArray(props.hide))
        languages = languages.filter(
            (language) => !props.hide.includes(language.code)
        );
    if (Array.isArray(props.show))
        languages = languages.filter((language) =>
            props.show.includes(language.code)
        );

    function formatOptionLabel({ label, flag }) {
        if (flag == null) return null;
        return (
            <div
                style={{
                    display: "flex",
                    flexDirection: "row",
                    alignItems: "center",
                    columnGap: "0.5rem",
                }}
            >
                <Flag
                    name={flag}
                    format="png"
                    pngSize={32}
                    shiny={false}
                    basePath={"/images/flags"}
                />
                {label}
            </div>
        );
    }

    return (
        <Select
            styles={{
                // Fixes the overlapping problem of the component
                menu: (provided) => ({ ...provided, zIndex: 9999 }),
            }}
            isMulti={props.isMulti}
            closeMenuOnSelect={!props.isMulti}
            isDisabled={props.isDisabled}
            isClearable={props.isClearable}
            placeholder={
                props.placeholder
                    ? props.placeholder
                    : translate("language.action.select")
            }
            defaultValue={
                props.defaultValue
                    ? languages.filter((language) =>
                          props.defaultValue?.includes(language.code)
                      )
                    : undefined
            }
            value={
                props.value
                    ? languages.find(
                          (language) =>
                              language.code === props.value?.toLowerCase()
                      )
                    : undefined
            }
            onChange={(language) => props.onChange(language)}
            getOptionLabel={(language) => language.code}
            getOptionValue={(language) => language.label}
            formatOptionLabel={formatOptionLabel}
            options={languages}
        />
    );
}
