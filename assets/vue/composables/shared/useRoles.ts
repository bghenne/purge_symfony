import { Identity } from "../../types/identity";

export function useRoles() {
  function getRoles(): string[] {
    let roles: string[] = [];

    if ((localStorage.getItem("identity") as string).length > 0) {
      const identity: Identity = JSON.parse(
        localStorage.getItem("identity") as string,
      ) as Identity;

      roles = identity.roles;
    }

    return roles;
  }

  function hasRole(role: string): boolean {
    return getRoles().includes(role);
  }

  return {
    getRoles,
    hasRole,
  };
}
