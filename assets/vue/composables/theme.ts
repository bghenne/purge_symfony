import {doRequest} from "../utilities/request";
import {Methods} from "../enums/methods";
import {Theme} from "../types/theme";
import {ObjectType} from "../enums/object-type";

export function fetchThemes(objectType: ObjectType) {

    let themesList: Theme[] = [];

    doRequest(`/api/theme/${objectType}`, Methods.GET)
        .then((themes: Theme[]) => {
            themes.forEach((theme) => {
                themesList.push({
                    'name': theme.name,
                    'code': theme.code
                })
            })
        })
        .catch(error => console.log(error))

    return themesList;
}
