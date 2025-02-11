export interface EligibleObject {
    key?: string, // generated in JS
    dateCampagne: string, // yyyy-mm-dd
    nomDuClient: string,
    environnement: string,
    // identifiantFamille: number,
    // nomBeneficiaire: string, // TODO: typo
    // prenomBeneficiaire: string, // TODO: typo
    // dateNaissanceBeneficiaire: string,
    // numeroSecuriteSociale: string,
    datePaiementCotisation: string,
    periodeAppelCotisation: string,
    anneeAppelCotisation: number,
    delaiConservation: number,
    libRegPurg?: string,
    details: EligibleObjectDetails,
}

export interface EligibleObjectDetails {
    key?: string, // generated in JS
    dateCampagne: string, // yyyy-mm-dd
    nomDuClient: string,
    environnement: string,
    datePaiementCotisation: string,
    periodeAppelCotisation: string,
    anneeAppelCotisation: number,
    delaiConservation: number,
    libRegPurg?: string
}
