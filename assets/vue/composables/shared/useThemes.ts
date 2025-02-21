import { doRequest } from "../../utilities/request";
import { Methods } from "../../enums/methods";
import { ObjectType } from "../../enums/object-type";
import { ref } from "vue";
import { Theme } from "../../types/theme";

export function useThemes() {
  const themes = ref([] as Theme[]);
  const fetchingThemes = ref(false);
  const themesFetched = ref(false);

  async function fetchThemes(objectType: ObjectType): Promise<void> {
    let themesList: Theme[] = [];
    fetchingThemes.value = true;

    doRequest(`/api/theme/${objectType}`, Methods.GET)
      .then((results: Theme[]) => {
        results.forEach((theme) => {
          themesList.push({
            name: theme.name,
            code: theme.code,
          });
        });
        themes.value = themesList;
        themesFetched.value = true;
      })
      .catch((error) => {
        // TODO: use toast
        console.log(error);
      })
      .finally((): void => {
        fetchingThemes.value = false;
      });
  }

  return {
    themes,
    fetchingThemes,
    themesFetched,
    fetchThemes,
  };
}
