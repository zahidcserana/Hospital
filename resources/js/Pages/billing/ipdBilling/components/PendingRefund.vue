<template>
    <div class="hospital-list">
        <slot name="header"></slot>

        <div class="row">

            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-border table-striped custom-table datatable mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Bill Type </th>
                            <th>Patient </th>
                            <th>Patient Code</th>
                            <th>Bill No.</th>
                            <th>Bill Date </th>
                            <th>Total Bill Amount</th>
                            <th>Refund</th>
                            <th>Due Amount</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in data.ipd" :key="row.id">
                                <td>{{ row.id }}</td>
                                <td>IPD</td>
                                <td>{{ row.patient_admission.patient.name }}</td>
                                <td>{{ row.patient_admission.patient.code }}</td>
                                <td>{{ row.bill_no }}</td>
                                <td>{{ row.created_at | formatDate }}</td>
                                <td>{{ row.total_bill_amount }}</td>
                                <td>
                                        <span v-if="row.refund_amount>0"> {{ row.refund_amount }} |
                                        <i v-if="row.refund_status == 0" class="fa fa-clock-o"></i>
                                        <i v-if="row.refund_status == 1" class="fa fa-check"></i>
                                        <i v-if="row.refund_status == 2" class="fa fa-ban"></i>
                                    </span></td>
                                <td>{{ row.total_due_amount }}</td>
                                <td><span class="custom-badge" v-bind:class="row.status == 'COMPLETE' ? 'status-green' : 'status-red'">{{ row.status }}</span></td>

                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">

                                            <jet-responsive-nav-link :href="route('approve_refund', [row.id])"  class="dropdown-item" v-if="row.status == 'COMPLETE' && row.refund_amount > 0 &&  row.refund_status == 0 && ($page.auth.user.role == 'FD' || $page.isAdmin || $page.auth.user.email == 'abu.sayed@afc.com.bd'  )">
                                                <i class="fa fa-pencil m-r-5"></i>Approve Refund
                                            </jet-responsive-nav-link>

                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr v-for="row in data.opd" :key="row.id">
                                <td>{{ row.id }}</td>
                                <td>OPD</td>
                                <td>{{ row.patient.name }}</td>
                                <td>{{ row.patient.code }}</td>
                                <td>{{ row.bill_no }}</td>
                                <td>{{ row.created_at | formatDate }}</td>
                                <td>{{ row.total_bill_amount }}</td>
                                <td>
                                    <span v-if="row.refund_amount>0"> {{ row.refund_amount }} |
                                    <i v-if="row.refund_status == 0" class="fa fa-clock-o"></i>
                                    <i v-if="row.refund_status == 1" class="fa fa-check"></i>
                                    <i v-if="row.refund_status == 2" class="fa fa-ban"></i>
                                    </span>
                                </td>
                                <td>{{ row.total_due_amount }}</td>
                                <td><span class="custom-badge" v-bind:class="row.status == 'COMPLETE' ? 'status-green' : 'status-red'">{{ row.status }}</span></td>

                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">

                                            <jet-responsive-nav-link :href="route('opd_approve_refund', [row.id])"  class="dropdown-item" v-if="row.status == 'COMPLETE' && row.refund_amount > 0 &&  row.refund_status == 0 && ($page.auth.user.role == 'FD' || $page.isAdmin || $page.auth.user.email == 'abu.sayed@afc.com.bd' )">
                                                <i class="fa fa-pencil m-r-5"></i>Approve Refund
                                            </jet-responsive-nav-link>

                                         </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!--        invoice modal section start-->

            <!-- Modal -->
            <div class="modal fade  bd-example-modal-lg" id="receipt" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

                <invoice :bill="bill" />
            </div>

            <!--         invoice modal section end-->


        </div>
    </div>
</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import Invoice from './Invoice'
import {HTTP} from '@/utils/http-common'
import { Datetime } from 'vue-datetime';

export default {
    components: {
        JetResponsiveNavLink, Invoice,
        datetime: Datetime
    },
    props: ['data', 'errors'],
    data() {
        return {
            bill: {},
            filter_types: [
                {'key_value': '', 'key_name': 'All'},
                {'key_value': 'patient_name', 'key_name': 'Patient Name'},
                {'key_value': 'patient_mobile', 'key_name': 'Patient Mobile'},
                {'key_value': 'patient_code', 'key_name': 'Patient Code'},
                {'key_value': 'patient_id', 'key_name': 'Patient ID'},
                {'key_value': 'bill_no', 'key_name': 'Bill No'},
            ],
            query: {
                search_type: '',
                search_text: '',
                from_date: undefined,
                to_date: undefined
            }
        }
    },
    mounted() {
        if (!_.isEmpty(this.data.query)) {
            this.query = this.data.query
        }
    },
    methods: {
        resetQuery: function () {
            this.query = null
        },
        resetData: function () {
            this.resetQuery()
            this.$inertia.get('/ipdBillings', this.query)
        },
        getData: function () {
            this.$inertia.get('/ipdBillings', this.query)
        },
        deleteRow: function (data) {
            if (!confirm('Are you sure want to remove?')) return;
            data._method = 'DELETE';
            this.$inertia.post('/ipdBillings/' + data.id, data)
        },
        invoice: function (bill) {
            HTTP.get('/ipdBillings/'+bill.id+'/receipt')
                .then((response)=>{
                    this.bill = response.data;
                })
        }
    }
}
</script>
