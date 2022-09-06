const ServiceModel = {
    search: {
        hospital_id: '',
        department_id: '',
        requisition_category_id: '',
        status: '',
        date_range: undefined,
        requisition_no: '',
    },

    statusMap: {
        INITIATED: 'status-purple',
        INITIATING: 'status-grey',
        APPROVED_BY_FD: 'status-green',
        APPROVED_BY_MS: 'status-green',
        APPROVED_BY_GM: 'status-green',
        APPROVED_BY_DF: 'status-green',
        PROCESSING: 'status-green',
        ACCEPTED: 'status-blue',
        ARCHIVED: 'status-red',
        VERIFIED: 'status-pink',
        DELIVERED: 'status-orange',
        LOCAL_PURCHASE: 'status-orange',
        RECEIVED: 'status-receive'
    }
}

export default ServiceModel
