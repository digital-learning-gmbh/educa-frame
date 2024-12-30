import Embed from '@editorjs/embed'
import Table from '@editorjs/table'
import List from '@editorjs/list'
import Header from '@editorjs/header'
import Quote from '@editorjs/quote'
import Warning from '@editorjs/warning'
import Delimiter from '@editorjs/delimiter'
import Marker from '@editorjs/marker'
import SimpleImage from '@editorjs/simple-image'
import Image from '@editorjs/image'
import AjaxHelper from "../educa-react/helpers/EducaAjaxHelper";

export const EDITOR_JS_TOOLS = {
    // NOTE: Paragraph is default tool. Declare only when you want to change paragraph option.
    embed: Embed,
    table: Table,
    list: List,
    warning: Warning,
    header: Header,
    quote: Quote,
    delimiter: Delimiter,
    marker: Marker,
    image: {
        class: Image,
        config: {
            uploader: {uploadByFile(file) {
                // your own uploading logic here
                return AjaxHelper.uploadWikiPageImage(file).then((resp) => {
                    return {
                        success: 1,
                        file: {
                            url: resp.payload.image,
                        }
                    };
                });
            }
            }
        },
    },
    simpleImage: SimpleImage,
}

export const EDITOR_JS_I18N = {
    messages: {
        ui: {
            "blockTunes": {
                "toggler": {
                    "Click to tune": "Zum Einstellen klicken",
                    "or drag to move": "oder zum Verschieben ziehen"
                },
            },
            "inlineToolbar": {
                "converter": {
                    "Convert to": "Umwandeln in"
                }
            },
            "toolbar": {
                "toolbox": {
                    "Add": "Hinzufügen"
                }
            }
        },

        /**
         * Section for translation Tool Names: both block and inline tools
         */
        toolNames: {
            "Text": "Text",
            "Heading": "Titel",
            "List": "Liste",
            "Warning": "Warnung",
            "Checklist": "Checkliste",
            "Quote": "Zitat",
            "Code": "Code",
            "Delimiter": "Trenner",
            "Raw HTML": "HTML",
            "Table": "Tabelle",
            "Link": "Link",
            "Marker": "Мarker",
            "Bold": "Fett",
            "Italic": "Kursiv",
            "InlineCode": "Eingebetteter Code",
        },

        /**
         * Section for passing translations to the external tools classes
         */
        tools: {
            "AnyButton": {
                'Button Text': 'Button Text',
                'Link Url': 'Link',
                'Set': "Speichern",
                'Default Button': "Standard-Button",
            }
        },

        /**
         * Section allows to translate Block Tunes
         */
        blockTunes: {
            /**
             * Each subsection is the i18n dictionary that will be passed to the corresponded Block Tune plugin
             * The name of a plugin should be equal the name you specify in the 'tunes' section for that plugin
             *
             * Also, there are few internal block tunes: "delete", "moveUp" and "moveDown"
             */
            "delete": {
                "Delete": "Löschen"
            },
            "moveUp": {
                "Move up": "Nach oben verschieben"
            },
            "moveDown": {
                "Move down": "Nach unten verschieben"
            }
        },
    }
};
