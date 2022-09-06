<template>
    <div class="hospital-create">
        <slot name="header"></slot>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <form>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>OpdService Name</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="form.name"
                                    />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Tariff</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="form.tariff"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>OpdService Status</label><br />
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
    </div>
</template>

<script>
import Alert from '@/Pages/Component/Alert'
export default {
    components: {
        Alert
    },
    props: ['data', 'editMode'],
    data () {
        return {
            form: {
                id: undefined,
                name: null,
                tariff: null,
                status: 'ACTIVE'
            }
        }
    },
    created () {
        if (this.editMode) {
            this.form.id = this.data.id
            this.form.name = this.data.name
            this.form.tariff = this.data.tariff
            this.form.status = this.data.status
        }
    },
    methods: {
        reset: function () {
            this.form = {
                name: null,
                tariff: null,
                status: 'ACTIVE'
            }
        },
        save: function (data) {
            this.$inertia.post('/opdServices', data)
            this.reset()
        },
        update: function (data) {
            data._method = 'PUT'
            this.$inertia.post('/opdServices/' + data.id, data)
        }
    }
}
</script>
