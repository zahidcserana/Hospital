<template>
    <div class="hospital-create">
        <slot name="header"></slot>
        <div class="row  card-box">
            <div class="col-lg-8 offset-lg-2">
                <form>
                    <div class="row">

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Company Name</label>
                                <input class="form-control" type="text" v-model="form.company_name"/>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Contact Person Name</label>
                                <input class="form-control" type="text" v-model="form.contact_person_name"/>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Contact Person Mobile</label>
                                <input class="form-control" type="text" v-model="form.contact_person_mobile"/>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Contact Person Email</label>
                                <input class="form-control" type="email" v-model="form.contact_person_email"/>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Address</label>
                                <textarea
                                    v-model="form.address"
                                    cols="30"
                                    rows="4"
                                    class="form-control"
                                ></textarea>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-6">
                        <div class="form-group">
                            <label> Status</label><br/>
                            <div class="form-check form-check-inline">
                                <input
                                    v-model="form.status"
                                    class="form-check-input"
                                    type="radio"
                                    name="status"
                                    value="ACTIVE"
                                />
                                <label
                                    class="form-check-label"
                                    for="product_active"
                                >
                                    Active
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input
                                    v-model="form.status"
                                    class="form-check-input"
                                    type="radio"
                                    name="status"
                                    value="INACTIVE"
                                />
                                <label
                                    class="form-check-label"
                                    for="product_inactive"
                                >
                                    Inactive
                                </label>
                            </div>
                        </div>
                    </div>


                    <div class="m-t-20 text-center">
                        <button
                            type="button"
                            class="btn btn-primary submit-btn"
                            wire:click.prevent="store()"
                            v-show="!editMode"
                            @click="save(form)"
                        >
                            Save
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary submit-btn"
                            wire:click.prevent="store()"
                            v-show="editMode"
                            @click="update(form)"
                        >
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Alert from "@/Pages/Component/Alert";
 export default {
     components: {
         Alert
     },
        props: ['data', 'errors', 'editMode'],
        data() {
            return {
                isOpen: false,
                form: {
                    id: undefined,
                    company_name: null,
                    address: null,
                    contact_person_name: null,
                    contact_person_mobile: null,
                    contact_person_email: null,
                    status: 'ACTIVE',
                },
            }
        },
        created() {
            if (this.editMode) {
                this.form = this.data
            }
        },
        methods: {
            reset: function () {
                this.form = {  }
            },
            save: function (data) {
                this.$inertia.post('/corporateClients', data)
            },
            edit: function (data) {
                this.form = Object.assign({}, data);
                this.editMode = true;
                this.openModal();
            },
            update: function (data) {
                data._method = 'PUT';
                this.$inertia.post('/corporateClients/' + data.id, data)
            }
        }
    }
</script>
