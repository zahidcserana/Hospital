<template>
    <div class="hospital-list">
        <slot name="header"></slot>


        <div class="card-box">
            <form id="filter-form">

                <div class="row">
<!--                    <div class="col-md-4" v-if="!$page.isAdmin  && $page.auth.user.email != 'abu.sayed@afc.com.bd'">-->
<!--                        <div class="form-group">-->
<!--                            <label> Summery Type</label>-->
<!--                            <select class="form-control select" v-model="filter_type" >-->

<!--                                <option  v-for="row in filter_types" :key="row.key_value" :value="row.key_value">{{ row.key_name }}</option>-->
<!--                            </select>-->
<!--                        </div>-->
<!--                    </div>-->
                    <div class="col-md-3" v-if="$page.isAdmin || $page.auth.user.email == 'abu.sayed@afc.com.bd' || $page.auth.user.email == 'Shaad.is4you@gmail.com'">
                        <div class="form-group">
                            <label> Select Employee</label>
                            <select class="form-control select" v-model="employee_id" >
                                <option value="all">All</option>
                                <option  v-for="row in data.users" :key="row.id" :value="row.id">{{ row.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label> Start Date</label>
                            <datetime  v-model="from_date" type="date" input-class="form-control" format="yyyy-MM-dd"></datetime>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label> End Date</label>
                            <datetime   v-model="to_date"  type="date" input-class="form-control" format="yyyy-MM-dd"></datetime>
                        </div>
                    </div>
                    <div class="col-sm-2 col-md-2">
                        <label for="">&nbsp;</label>
                        <br/>
                        <button type="button"  data-toggle="modal" data-target="#receipt"  @click="getData()" class="btn btn-success btn-small"> Show Summery </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-md-12">

            </div>

            <!--        invoice modal section start-->

            <!-- Modal -->
            <div class="modal fade  bd-example-modal-lg" id="receipt" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

                <summery :invoice="invoice_data" />
            </div>

            <!--         invoice modal section end-->

        </div>
    </div>
</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import Summery from './Summery'
import {HTTP} from "@/utils/http-common";
import { Datetime } from 'vue-datetime';
import Invoice from "@/Pages/billing/opdBilling/components/Invoice";
import moment from "moment";

export default {
    components: {
        JetResponsiveNavLink, Summery,
        datetime: Datetime
    },
    props: ['data', 'errors'],
    data() {
        return {
            filter_type: 'today',
            from_date: null,
            to_date: null,
            employee_id: '',
            filter_types: [
                {'key_value': 'today', 'key_name': 'Today'},
                {'key_value': 'till_date', 'key_name': 'Till Date'}
            ],
            invoice_data: {}
        }
    },
    methods: {
        getData: function () {
            var from_date = this.from_date ? moment(this.from_date).format('YYYY-MM-DD') : 0;
            var to_date = this.to_date ? moment(this.to_date).format('YYYY-MM-DD') : 0;
            HTTP.get('/payment/get-bill-summery?filter_type='+this.filter_type+'&employee_id='+this.employee_id+'&from_date='+from_date+'&to_date='+to_date)
                .then((response)=>{
                    this.invoice_data = response.data;
                })
        }
    }
}
</script>
