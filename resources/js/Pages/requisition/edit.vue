<template>
    <app-layout>
        <Model :param="param" :editMode="1">
            <template #header>
                <Alert
                    :title="title"
                    :flash="flash"
                    :errors="errors"
                    :link="link"
                    :label="label"
                />
            </template>
        </Model>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Model from './components/Model'
import Alert from '@/Pages/Component/Alert'
export default {
    components: {
        AppLayout,
        Model,
        Alert
    },
    props: ['param', 'errors', 'flash'],
    data () {
        return {
            title: 'Update Requisition',
            link: 'requisitions.index',
            label: 'Requisition List'
        }
    },
    created () {
        if (this.param.total_item == 0) {
            this.title = 'Create Requisition'
        }

        if (this.param.data.type == 2) {
            this.link = 'dept_requisitions'
        }
    }
}
</script>
