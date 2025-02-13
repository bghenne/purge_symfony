import {Identity} from "../types/identity";

export function fetchEnvironments() : {name: string}[]
{
    let environmentsList : {name: string}[] = [];

    if ((localStorage.getItem('identity') as string).length > 0) {
        const identity : Identity = JSON.parse(localStorage.getItem('identity') as string) as Identity;
        identity.environments.forEach((environment, index) => {
            environmentsList.push({
                'name': environment
            })
        })
    }

    return environmentsList;

}