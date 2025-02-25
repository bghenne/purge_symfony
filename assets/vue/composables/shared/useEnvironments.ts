import {Identity} from "../../../types/identity";
import {ref} from "vue";

export function useEnvironments() {
    const environments = ref([] as {name: string}[]);

    function getEnvironments(): void {
        let environmentsList: { name: string }[] = [];

        if ((localStorage.getItem('identity') as string).length > 0) {
            const identity: Identity = JSON.parse(localStorage.getItem('identity') as string) as Identity;
            identity.environments.forEach((environment, index) => {
                environmentsList.push({
                    'name': environment
                })
            })
        }

      environments.value = environmentsList;
    }

    return {
        environments,
        getEnvironments,
    }
}
