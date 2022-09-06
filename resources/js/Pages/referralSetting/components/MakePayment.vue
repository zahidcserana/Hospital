<template>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title pull-left" id="exampleModalLongTitle">
                    Make Payment
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" v-if="data.person">
                <div class="wrapper " >

                    <form>
                        <div class="row">

                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label>Due Amount</label>
                                    <input class="form-control" type="number" :disabled="true" :value="data.referral_amount - data.payment_amount"/>
                                </div>
                            </div>

                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label>Payment Amount</label>
                                    <input class="form-control" type="number" min="0" :max="data.referral_amount - data.payment_amount" v-model="form.payment_amount"/>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="m-t-20 text-center">
                                    <button
                                        type="button"
                                        class="btn btn-success"
                                        @click="save(form)"
                                    >
                                        Save
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
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
import {HTTP} from "@/utils/http-common";
import {error_message, success_message} from "@/utils/alert";

export default {
    components: {
        JetResponsiveNavLink,
    },
    props: ['data'],
    data() {
        return {
            form: {}
        }
    },
    methods: {
        save: function (data) {
            this.$inertia.post('/referral/'+this.data.person.id+'/makePayment', data)
                .then((response)=>{
                    this.form = {}
                success_message((response.data.message));
            }).catch(function (error) {
                error_message(error.response.data.errors)
            })
        },
        printElement: function () {
            let elem = document.getElementById("printSection")
            var domClone = elem.cloneNode(true);
            //
            var $printSection = document.getElementById("printSections");
            //
            if (!$printSection) {
                var $printSection = document.createElement("div");
                $printSection.id = "printSection";
                document.body.appendChild($printSection);
            }

            $printSection.innerHTML = "";
            $printSection.appendChild(domClone);
            window.print();
        }
    }
}
</script>
<style>

.modal-dialog {
    max-width: 490px;
    /*max-width: 1050px;*/
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
