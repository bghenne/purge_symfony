export interface EligibleObjects {
    eligibleObjects: EligibleObject[],
    total: number
}

export interface EligibleObject {
    key: string, // generated in PHP
    campaignDate: string, // yyyy-mm-dd
    clientName: string,
    environment: string,
    familyId: number,
    conservationTime: number,
    contributionCallPeriod: string,
    contributionCallYear: number,
    contributionPaymentDate: string,
    purgeRuleLabel ?: string
    details: {
        key: string,
        beneficiaryName: string,
        beneficiaryFirstname : string,
        beneficiaryBirthdate: string,
        socialSecurityNumber: string
    }

}
