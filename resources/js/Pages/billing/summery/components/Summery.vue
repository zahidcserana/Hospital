<template>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title pull-left" id="exampleModalLongTitle"><button type="button" @click="printElement()" class="btn btn-primary">Print</button></h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"  v-if="invoice.id">
                <div class="wrapper " >
                    <!-- Main content -->
                    <section class="invoice"  id="printSection" style="width: 1000px">
                        <!-- title row -->
                        <div class="row border-dark border-bottom pb-2">
                            <div class="col-12">
                                <h3 class="page-header">
                                <span class="float-left">
<!--                                     <img src="assets/img/logo.jpg" style="height: 45px;">-->
                                    <img src="/assets/img/logo.jpg"   style="height: 45px;">
                                    <span style="vertical-align: -webkit-baseline-middle">Manipal AFC Hospitals</span>
                                </span>
                                    <small class="float-right">Print Time: {{ date }}</small>
                                </h3>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- info row -->
                        <div style="display: block!important;text-align: center;" class="row mt-1">
                            <p style="font-size: medium;">
                                <strong>Manipal AFC Hospitals</strong>,
                                Cumilla
                            </p>
                            <p>
                            <h3>DAILY COLLECTION SUMMERY</h3>
                            <p>Employee Name: <strong>{{invoice.name}}</strong></p>
                            <p>Employee Email: <strong>{{invoice.email}}</strong></p>
                            </p>
                        </div>

                        <!-- Table row -->
                        <div class="row mt-4 border-dark border-bottom">
                            <div class="col-12 table-responsive">
                                <table id="product_table" class="table table-striped">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Collection Time</th>
                                        <th scope="col">Bill Type</th>
                                        <th scope="col">Patient Name</th>
                                        <th scope="col">Patient Mobile</th>
                                        <th scope="col">Collection Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="row in invoice.summery">
                                        <td>{{ row.created_at | formatDateTime }}</td>
                                        <td>{{row.billing_type}}</td>
                                        <td>{{row.patient.name}}</td>
                                        <td>{{row.patient.mobile}}</td>
                                        <td>{{row.payment_amount}} tk</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td>Collection Amount:</td>
                                        <td>{{ invoice.total }} tk</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td>Refund Amount:</td>
                                        <td>{{ invoice.refund_amount }} tk</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td>Total Collection Amount:</td>
                                        <td>{{ invoice.total - invoice.refund_amount }} tk</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                        <!-- /.row -->
                    </section>
                    <!-- /.content -->
                </div>
                <!-- ./wrapper -->
                <div id="printSections"></div>
            </div>
        </div>
    </div>

</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import moment from 'moment'
export default {
    components: {
        JetResponsiveNavLink,
    },
    props: ['invoice'],
    data() {
        return {
            date: moment().format('DD-MMM-YYYY hh:MM a')
        }
    },
    methods: {
        printElement: function () {
            this.$htmlToPaper('printSection');
            // let elem = document.getElementById("printSection")
            // var domClone = elem.cloneNode(true);
            // //
            // var $printSection = document.getElementById("printSections");
            // //
            // if (!$printSection) {
            //     var $printSection = document.createElement("div");
            //     $printSection.id = "printSection";
            //     document.body.appendChild($printSection);
            // }
            //
            // $printSection.innerHTML = "";
            // $printSection.appendChild(domClone);
            // window.print();
            // $printSection.innerHTML = "";
        }
    }
}
</script>
<style>

.modal-dialog {
    max-width: 1050px;
    /*margin: 1.75rem auto;*/
}
@media print {
    body * {
        visibility:hidden;
    }
    #printSection, #printSection * {
        visibility:visible;
    }
    #printSection {
        position:absolute;
        left:0;
        top:0;
    }
}
</style>
