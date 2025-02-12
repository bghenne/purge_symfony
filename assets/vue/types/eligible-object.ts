export interface EligibleObject {
    key: string, // generated in PHP
    campaignDate: string, // yyyy-mm-dd
    clientName: string,
    environment: string,
    familyId: number,
    beneficiaryName: string, // TODO: typo
    beneficiaryFirstname : string, // TODO: typo
    beneficiaryBirthdate: string,
    details: {
        key: string,
        conservationTime: number,
        contributionCallPeriod: string,
        contributionCallYear: number,
        contributionPaymentDate: string,
        purgeRuleLabel ?: string
    }
}
